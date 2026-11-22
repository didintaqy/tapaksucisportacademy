<?php

return [

    'label' => 'Impor :label',

    'modal' => [

        'heading' => 'Impor :label',

        'form' => [

            'file' => [

                'label' => 'Berkas',

                'placeholder' => 'Unggah berkas CSV',

                'rules' => [
                    'duplicate_columns' => '{0} Berkas tidak boleh memiliki lebih dari satu header kolom kosong.|{1,*} Berkas tidak boleh memiliki header kolom ganda: :columns.',
                ],

            ],

            'columns' => [

                'label' => 'Kolom',
                'placeholder' => 'Pilih kolom',

            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Unduh contoh berkas CSV',
            ],

            'import' => [
                'label' => 'Impor',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Impor selesai',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Unduh informasi baris yang gagal|Unduh informasi baris-baris yang gagal',
                ],

            ],

        ],

        'max_rows' => [

            'title' => 'Berkas CSV terlalu besar',

            'body' => 'Anda tidak dapat mengimpor lebih dari 1 baris sekaligus.|Anda tidak dapat mengimpor lebih dari :count baris sekaligus.',

        ],

        'started' => [

            'title' => 'Impor dimulai',

            'body' => 'Impor Anda telah dimulai dan 1 baris akan diproses di latar belakang.|Impor Anda telah dimulai dan :count baris akan diproses di latar belakang.',

        ],

    ],

    'example_csv' => [

        'file_name' => ':importer-contoh',

    ],

    'failure_csv' => [

        'file_name' => 'impor-:import_id-:csv_name-baris-gagal',

        'error_header' => 'kesalahan',

        'system_error' => 'Terjadi kesalahan sistem. Hubungi administrator.',

        'column_mapping_required_for_new_record' => 'Kolom :attribute belum dipetakan ke kolom pada berkas, padahal kolom ini wajib untuk membuat data baru.',

    ],

];
