/**
 * Pesan validasi HTML5 dalam Bahasa Indonesia.
 *
 * Pesan bawaan browser mengikuti bahasa browser pengguna, sehingga pengguna
 * berbahasa Inggris akan melihat "Please fill out this field." di formulir
 * yang seharusnya berbahasa Indonesia. Skrip ini mengganti pesan tersebut
 * melalui Constraint Validation API tanpa menonaktifkan validasi browser.
 *
 * Setiap kolom dapat diberi pesan khusus:
 *   data-required-message="Nama wajib diisi."
 *   data-type-message="Masukkan email yang valid."
 *   data-pattern-message="Format nomor telepon tidak sesuai."
 *   data-validation-message="Nilai tidak valid."
 */
(function () {
    'use strict';

    var typeMessages = {
        email: 'Masukkan alamat email yang valid.',
        url: 'Masukkan URL yang valid.',
        tel: 'Masukkan nomor telepon yang valid.',
        number: 'Masukkan angka yang valid.',
        date: 'Masukkan tanggal yang valid.',
        time: 'Masukkan waktu yang valid.',
        'datetime-local': 'Masukkan tanggal dan waktu yang valid.',
        month: 'Masukkan bulan yang valid.',
        week: 'Masukkan minggu yang valid.',
        color: 'Pilih warna yang valid.'
    };

    function requiredMessage(field) {
        if (field.dataset.requiredMessage) {
            return field.dataset.requiredMessage;
        }

        if (field.tagName === 'SELECT') {
            return 'Pilih salah satu opsi.';
        }

        if (field.type === 'checkbox' || field.type === 'radio') {
            return 'Pilih opsi ini.';
        }

        if (field.type === 'file') {
            return 'Pilih berkas yang akan diunggah.';
        }

        return 'Kolom ini wajib diisi.';
    }

    function messageFor(field) {
        var validity = field.validity;

        if (validity.valueMissing) {
            return requiredMessage(field);
        }

        if (validity.typeMismatch) {
            return field.dataset.typeMessage || typeMessages[field.type] || 'Format yang dimasukkan tidak valid.';
        }

        if (validity.patternMismatch) {
            return field.dataset.patternMessage || 'Format yang dimasukkan tidak sesuai.';
        }

        if (validity.tooShort && field.minLength > 0) {
            return 'Minimal ' + field.minLength + ' karakter.';
        }

        if (validity.tooLong && field.maxLength > 0) {
            return 'Maksimal ' + field.maxLength + ' karakter.';
        }

        if (validity.rangeUnderflow && field.min !== '') {
            return 'Nilai minimal adalah ' + field.min + '.';
        }

        if (validity.rangeOverflow && field.max !== '') {
            return 'Nilai maksimal adalah ' + field.max + '.';
        }

        if (validity.stepMismatch) {
            return 'Nilai tidak sesuai dengan langkah yang diizinkan.';
        }

        if (validity.badInput) {
            return 'Format yang dimasukkan tidak valid.';
        }

        return field.dataset.validationMessage || '';
    }

    function isFormControl(element) {
        return element instanceof HTMLInputElement
            || element instanceof HTMLSelectElement
            || element instanceof HTMLTextAreaElement;
    }

    document.addEventListener('invalid', function (event) {
        if (!isFormControl(event.target) || typeof event.target.setCustomValidity !== 'function') {
            return;
        }

        var message = messageFor(event.target);

        if (message) {
            event.target.setCustomValidity(message);
        }
    }, true);

    ['input', 'change'].forEach(function (eventName) {
        document.addEventListener(eventName, function (event) {
            if (isFormControl(event.target) && typeof event.target.setCustomValidity === 'function') {
                // Hapus pesan khusus agar browser memvalidasi ulang nilai terbaru.
                event.target.setCustomValidity('');
            }
        }, true);
    });
})();
