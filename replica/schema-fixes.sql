-- =====================================================================
-- Usulan DDL perbaikan skema — Tapak Suci Sport Academy (REMAJA)
-- Target: MySQL 8.0+ / MariaDB 10.6+ (produksi Hostinger)
-- Status: REFERENSI. Belum dijalankan. Terapkan lewat migrasi Laravel
--         (satu migrasi per blok) agar bisa rollback dan teruji.
-- SQLite (lokal/CI) butuh DDL berbeda untuk drop constraint; gunakan
-- Schema Builder Laravel, bukan file ini, untuk menjalankannya.
-- =====================================================================

-- ---------------------------------------------------------------------
-- A1. Tabel cabang + FK. Menggantikan string bebas di 4 tempat.
-- ---------------------------------------------------------------------
CREATE TABLE branches (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name          VARCHAR(120) NOT NULL,
  slug          VARCHAR(120) NOT NULL,
  address       TEXT NULL,
  phone         VARCHAR(40) NULL,
  is_active     TINYINT(1) NOT NULL DEFAULT 1,
  sort_order    INT NOT NULL DEFAULT 0,
  created_at    TIMESTAMP NULL,
  updated_at    TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY branches_slug_unique (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Backfill dari nilai unik yang sudah ada (jalankan sekali, tinjau hasilnya).
INSERT INTO branches (name, slug, is_active, sort_order, created_at, updated_at)
SELECT DISTINCT TRIM(branch), LOWER(REPLACE(TRIM(branch), ' ', '-')), 1, 0, NOW(), NOW()
FROM (
  SELECT branch FROM students WHERE branch IS NOT NULL AND branch <> ''
  UNION SELECT branch FROM training_schedules WHERE branch IS NOT NULL AND branch <> ''
  UNION SELECT branch FROM student_registrations WHERE branch IS NOT NULL AND branch <> ''
) AS existing_branches;

ALTER TABLE students              ADD COLUMN branch_id BIGINT UNSIGNED NULL AFTER branch;
ALTER TABLE training_schedules    ADD COLUMN branch_id BIGINT UNSIGNED NULL AFTER branch;
ALTER TABLE student_registrations ADD COLUMN branch_id BIGINT UNSIGNED NULL AFTER branch;

UPDATE students s
  JOIN branches b ON b.name = TRIM(s.branch)
  SET s.branch_id = b.id;
UPDATE training_schedules t
  JOIN branches b ON b.name = TRIM(t.branch)
  SET t.branch_id = b.id;
UPDATE student_registrations r
  JOIN branches b ON b.name = TRIM(r.branch)
  SET r.branch_id = b.id;

ALTER TABLE students
  ADD CONSTRAINT students_branch_id_fk FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL;
ALTER TABLE training_schedules
  ADD CONSTRAINT training_schedules_branch_id_fk FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL;
ALTER TABLE student_registrations
  ADD CONSTRAINT student_registrations_branch_id_fk FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL;

-- Setelah aplikasi membaca branch_id, kolom string lama bisa dihapus.

-- Pivot program <-> cabang (menggantikan programs.branches JSON).
CREATE TABLE program_branch (
  program_id BIGINT UNSIGNED NOT NULL,
  branch_id  BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (program_id, branch_id),
  CONSTRAINT program_branch_program_id_fk FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
  CONSTRAINT program_branch_branch_id_fk  FOREIGN KEY (branch_id)  REFERENCES branches(id)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- A2. Unifikasi identitas pelatih.
-- `coaches` menjadi sumber identitas; user pelatih ditautkan.
-- ---------------------------------------------------------------------
ALTER TABLE coaches ADD COLUMN user_id BIGINT UNSIGNED NULL AFTER id;
ALTER TABLE coaches
  ADD CONSTRAINT coaches_user_id_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;
ALTER TABLE coaches ADD UNIQUE KEY coaches_user_id_unique (user_id);

-- Setelah backfill, arahkan jadwal ke coaches (bukan users).
-- Langkah aman: tambah kolom baru, backfill, ganti nama, hapus yang lama.
ALTER TABLE training_schedules ADD COLUMN coach_ref_id BIGINT UNSIGNED NULL AFTER coach_id;
UPDATE training_schedules t
  JOIN coaches c ON c.user_id = t.coach_id
  SET t.coach_ref_id = c.id;
-- Tinjau baris yang coach_ref_id NULL (data lama tanpa profil) sebelum lanjut:
-- SELECT * FROM training_schedules WHERE coach_id IS NOT NULL AND coach_ref_id IS NULL;

-- ---------------------------------------------------------------------
-- A3. Constraint pembayaran: izinkan beda jenis di periode sama.
-- period_key: '' untuk tagihan sekali bayar agar UNIQUE tidak bolong oleh NULL.
-- ---------------------------------------------------------------------
ALTER TABLE payments ADD COLUMN period_key VARCHAR(20) NOT NULL DEFAULT '' AFTER period;
UPDATE payments SET period_key = COALESCE(period, '') WHERE period_key = '';

ALTER TABLE payments DROP INDEX payments_student_period_unique;
ALTER TABLE payments
  ADD UNIQUE KEY payments_student_type_period_unique (student_id, payment_type_id, period_key);

-- Jaga idempotensi webhook: referensi gateway tidak boleh dobel.
ALTER TABLE payments
  ADD UNIQUE KEY payments_gateway_reference_unique (gateway_name, gateway_reference);

-- Index untuk laporan.
ALTER TABLE payments ADD KEY payments_status_paid_at_index (status, paid_at);

-- ---------------------------------------------------------------------
-- A4. Dokumen siswa (KK, akta, ijazah, foto) + enkripsi NIK/KK.
-- Enkripsi kolom dilakukan di aplikasi (Laravel encrypted cast), bukan SQL.
-- ---------------------------------------------------------------------
CREATE TABLE student_documents (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  student_id   BIGINT UNSIGNED NOT NULL,
  type         VARCHAR(40) NOT NULL,   -- kk | akta | ijazah | foto | lain
  file_path    VARCHAR(255) NOT NULL,
  mime_type    VARCHAR(120) NULL,
  size_bytes   BIGINT UNSIGNED NULL,
  uploaded_by  BIGINT UNSIGNED NULL,
  verified_at  TIMESTAMP NULL,
  created_at   TIMESTAMP NULL,
  updated_at   TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY student_documents_student_type_index (student_id, type),
  CONSTRAINT student_documents_student_id_fk FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  CONSTRAINT student_documents_uploaded_by_fk FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- NIK & KK: ubah tipe agar aman menyimpan ciphertext (panjang > plaintext).
-- ALTER TABLE students MODIFY nik VARCHAR(255) NULL;
-- ALTER TABLE students MODIFY family_card_number VARCHAR(255) NULL;
-- Catatan: UNIQUE pada nik tetap bisa dipertahankan bila ciphertext deterministik,
-- atau gunakan kolom hash terpisah: nik_hash CHAR(64) UNIQUE.
-- ALTER TABLE students ADD COLUMN nik_hash CHAR(64) NULL, ADD UNIQUE KEY students_nik_hash_unique (nik_hash);

-- ---------------------------------------------------------------------
-- A5. Audit log umum.
-- ---------------------------------------------------------------------
CREATE TABLE activity_log (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  subject_type  VARCHAR(120) NOT NULL,
  subject_id    BIGINT UNSIGNED NOT NULL,
  causer_id     BIGINT UNSIGNED NULL,
  event         VARCHAR(20) NOT NULL,   -- created | updated | deleted
  properties    JSON NULL,              -- {old: {...}, attributes: {...}}
  created_at    TIMESTAMP NULL,
  updated_at    TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY activity_log_subject_index (subject_type, subject_id, created_at),
  KEY activity_log_causer_index (causer_id, created_at),
  CONSTRAINT activity_log_causer_id_fk FOREIGN KEY (causer_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- B1. Keanggotaan / riwayat program.
-- Satu baris aktif per siswa ditegakkan di aplikasi + unique parsial
-- (MySQL 8 tidak punya partial index; gunakan kolom active_key).
-- ---------------------------------------------------------------------
CREATE TABLE enrollments (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  student_id   BIGINT UNSIGNED NOT NULL,
  program_id   BIGINT UNSIGNED NOT NULL,
  branch_id    BIGINT UNSIGNED NULL,
  coach_id     BIGINT UNSIGNED NULL,
  status       VARCHAR(20) NOT NULL DEFAULT 'active', -- active | cuti | berhenti | lulus
  started_at   DATE NOT NULL,
  ended_at     DATE NULL,
  notes        TEXT NULL,
  active_key   BIGINT UNSIGNED GENERATED ALWAYS AS
                 (CASE WHEN status = 'active' AND ended_at IS NULL THEN student_id ELSE NULL END) STORED,
  created_at   TIMESTAMP NULL,
  updated_at   TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY enrollments_one_active_per_student (active_key),
  KEY enrollments_student_status_index (student_id, status),
  KEY enrollments_program_branch_index (program_id, branch_id),
  CONSTRAINT enrollments_student_id_fk FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  CONSTRAINT enrollments_program_id_fk FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE RESTRICT,
  CONSTRAINT enrollments_branch_id_fk  FOREIGN KEY (branch_id)  REFERENCES branches(id) ON DELETE SET NULL,
  CONSTRAINT enrollments_coach_id_fk   FOREIGN KEY (coach_id)   REFERENCES coaches(id)  ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Backfill dari students.program_id (status aktif).
INSERT INTO enrollments (student_id, program_id, branch_id, status, started_at, created_at, updated_at)
SELECT s.id, s.program_id, s.branch_id, 'active', COALESCE(DATE(s.created_at), CURDATE()), NOW(), NOW()
FROM students s
WHERE s.program_id IS NOT NULL AND s.deleted_at IS NULL;

-- ---------------------------------------------------------------------
-- B2. Penugasan atlet ke pelatih (bila tidak memakai enrollments.coach_id).
-- ---------------------------------------------------------------------
CREATE TABLE coach_student (
  coach_id    BIGINT UNSIGNED NOT NULL,
  student_id  BIGINT UNSIGNED NOT NULL,
  assigned_at DATE NOT NULL,
  released_at DATE NULL,
  PRIMARY KEY (coach_id, student_id, assigned_at),
  KEY coach_student_student_index (student_id),
  CONSTRAINT coach_student_coach_id_fk   FOREIGN KEY (coach_id)   REFERENCES coaches(id)   ON DELETE CASCADE,
  CONSTRAINT coach_student_student_id_fk FOREIGN KEY (student_id) REFERENCES students(id)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- B3. Peserta event / lomba / UKT.
-- ---------------------------------------------------------------------
CREATE TABLE event_participants (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  event_id      BIGINT UNSIGNED NOT NULL,
  student_id    BIGINT UNSIGNED NOT NULL,
  registered_at TIMESTAMP NULL,
  status        VARCHAR(20) NOT NULL DEFAULT 'registered', -- registered | confirmed | attended | cancelled
  fee_amount    DECIMAL(12,2) NULL,
  result_note   VARCHAR(255) NULL,
  created_at    TIMESTAMP NULL,
  updated_at    TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY event_participants_event_student_unique (event_id, student_id),
  CONSTRAINT event_participants_event_id_fk   FOREIGN KEY (event_id)   REFERENCES events(id)   ON DELETE CASCADE,
  CONSTRAINT event_participants_student_id_fk FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- B4. Log notifikasi keluar (email/WhatsApp) untuk audit & retry.
-- ---------------------------------------------------------------------
CREATE TABLE notification_logs (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  channel       VARCHAR(20) NOT NULL,       -- email | whatsapp | sms
  recipient     VARCHAR(180) NOT NULL,
  template      VARCHAR(80) NULL,
  subject_type  VARCHAR(120) NULL,
  subject_id    BIGINT UNSIGNED NULL,
  status        VARCHAR(20) NOT NULL DEFAULT 'queued', -- queued | sent | failed
  provider_ref  VARCHAR(120) NULL,
  error         TEXT NULL,
  sent_at       TIMESTAMP NULL,
  created_at    TIMESTAMP NULL,
  updated_at    TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY notification_logs_status_channel_index (status, channel, created_at),
  KEY notification_logs_subject_index (subject_type, subject_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- P2. Index & constraint tambahan.
-- ---------------------------------------------------------------------
ALTER TABLE fitness_benchmarks
  ADD KEY fitness_benchmarks_lookup_index (fitness_test_item_id, gender, age_min, age_max);

ALTER TABLE achievements
  ADD KEY achievements_medal_index (medal, event_date);

ALTER TABLE belt_promotions
  ADD KEY belt_promotions_exam_result_index (exam_date, result);

-- newsletter_deliveries: status kirim per penerima.
ALTER TABLE newsletter_deliveries
  ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'sent' AFTER sent_at,
  ADD COLUMN error TEXT NULL AFTER status;

-- =====================================================================
-- Catatan penerapan
-- 1. Jalankan di salinan database lebih dulu; ukur durasi pada data nyata.
-- 2. Urutan: A1 -> A2 -> A3 -> A4 -> A5 -> B1 -> B2 -> B3 -> B4 -> P2.
-- 3. Setelah backfill diverifikasi, hapus kolom lama dalam migrasi terpisah:
--    students.branch, training_schedules.branch, student_registrations.branch,
--    programs.branches, training_schedules.coach_name, posts.category,
--    students.belt_level (ganti turunan dari belt_promotions).
-- 4. Perbarui docs/ARCHITECTURE.md setelah skema berubah.
-- =====================================================================
