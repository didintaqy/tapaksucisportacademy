<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fase A4 — Dokumen siswa.
 *
 * Menyimpan berkas identitas (KK, akta, ijazah, foto) secara terpisah dari
 * tabel siswa, sehingga akses bisa dibatasi lewat policy dan retensi diatur.
 *
 * Catatan: enkripsi kolom NIK/KK TIDAK dilakukan di sini karena data lama
 * masih plaintext. Enkripsi butuh migrasi data bertahap tersendiri.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('type', 40);            // kk | akta | ijazah | foto | lain
            $table->string('file_path');
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'type'], 'student_documents_student_type_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};
