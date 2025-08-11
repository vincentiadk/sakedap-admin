<?php

use App\Helper\GeneralHelper;

$validation = [
    'code' => [
        'validation' => 'unique:collections,code',
        'messages' => [
            'unique' => 'Kode (ex: ISBN, ISSN) tersebut sudah ada pada database!',
        ]
    ],
    'publisher_id' => [
        'validation' => 'required',
        'messages' => [
            'required' => 'Publisher Wajib diisi!',
        ]
    ],
    'title' => [
        'validation' => 'required',
        'messages' => [
            'required' => 'Judul wajib diisi!',
        ]
    ],
    'publication_month' => [
        'validation' => 'required|date_format:m',
        'messages' => [
            'required' => 'Bulan terbit wajib di isi!',
            'date_format' => 'Bulan terbit harus berupa bulan!'
        ]
    ],
    'publication_year'  => [
        'validation' => 'required|date_format:Y',
        'messages' => [
            'required' => 'Tahun terbit wajib di isi!',
            'date_format' => 'Tahun terbit harus berupa tahun!'
        ]
    ],
    'received_at' => [
        'validation' => 'required',
        'messages' => [
            'required' => 'Tanggal terbit wajib di isi!'
        ]
    ],
    'cover' => [
        'validation' => 'required|max:1024|mimes:jpg,jpeg,png',
        'messages' => [
            'required' => 'Cover wajib di isi!',
            'max' => 'Cover maksimal 1MB!',
            'mimes' => 'Cover harus bertipe jpg, jpeg, png!',
        ]
    ]
];

return [
    '7' => [
        'form_type' => null, //jenis koleksi
        'code' => GeneralHelper::validateConfig('code', $validation),
        'publisher_id' => GeneralHelper::validateConfig('publisher_id', $validation),
        'title' => GeneralHelper::validateConfig('title', $validation), //judul
        'series' => null, // volume 
        'edition' => null, // volume
        'publication_month' => GeneralHelper::validateConfig('publication_month', $validation), //bulan terbit
        'publication_year' => GeneralHelper::validateConfig('publication_year', $validation), //tahun terbit
        'received_at' => GeneralHelper::validateConfig('received_at', $validation), //tgl terima
        'total_page' => GeneralHelper::validateConfig('total_page', $validation), //deskripsi fisik 
        'dimension' => null, //deskripsi fisik
        'price' => null, //harga (new_fields?)
        'total_copy' => GeneralHelper::validateConfig('total_copy', $validation), //total eksemplar (new_fields?)
        'collection_category' => null,
        'collection_subject' => null,
        'collection_condition' => null, //kondisi (new_fields?) 
        'collection_location' => null, //lokasi penyimpanan (one to many / many to many)
        'collection_contributor' => null, //contributor (pengarang)
        'collection_media' => null, //contributor (pengarang)
        'collection_copy' => null, //collection eksemplar
        'description' => GeneralHelper::validateConfig('description', $validation), //ringkasan
        'cover' => GeneralHelper::validateConfig('cover', $validation), //cover
        'description' => GeneralHelper::validateConfig('description', $validation), //ringkasan
        'cover' => GeneralHelper::validateConfig('cover', $validation), //cover
    ],
    '8' => [
        'form_type' => null, //jenis koleksi
        'code' => GeneralHelper::validateConfig('code', $validation),
        'publisher_id' => GeneralHelper::validateConfig('publisher_id', $validation),
        'title' => GeneralHelper::validateConfig('title', $validation), //judul
        'series' => null, // volume 
        'edition' => null, // volume
        'publication_month' => GeneralHelper::validateConfig('publication_month', $validation), //bulan terbit
        'publication_year' => GeneralHelper::validateConfig('publication_year', $validation), //tahun terbit
        'received_at' => GeneralHelper::validateConfig('received_at', $validation), //tgl terima
        'total_page' => GeneralHelper::validateConfig('total_page', $validation), //deskripsi fisik 
        'dimension' => null, //deskripsi fisik
        'price' => null, //harga (new_fields?)
        'total_copy' => GeneralHelper::validateConfig('total_copy', $validation), //total eksemplar (new_fields?)
        'collection_category' => null,
        'collection_subject' => null,
        'collection_condition' => null, //kondisi (new_fields?) 
        'collection_location' => null, //lokasi penyimpanan (one to many / many to many)
        'collection_contributor' => null, //contributor (pengarang)
        'collection_copy' => null, //collection eksemplar
        'description' => GeneralHelper::validateConfig('description', $validation), //ringkasan
        'cover' => GeneralHelper::validateConfig('cover', $validation), //cover
    ],
    '9' => [
        'form_type' => null, //jenis koleksi
        'code' => GeneralHelper::validateConfig('code', $validation),
        'publisher_id' => GeneralHelper::validateConfig('publisher_id', $validation),
        'title' => GeneralHelper::validateConfig('title', $validation), //judul
        'series' => null, // volume 
        'edition' => null, // volume
        'publication_month' => GeneralHelper::validateConfig('publication_month', $validation), //bulan terbit
        'publication_year' => GeneralHelper::validateConfig('publication_year', $validation), //tahun terbit
        'received_at' => GeneralHelper::validateConfig('received_at', $validation), //tgl terima
        'total_page' => GeneralHelper::validateConfig('total_page', $validation), //deskripsi fisik 
        'dimension' => null, //deskripsi fisik
        'price' => null, //harga (new_fields?)
        'total_copy' => GeneralHelper::validateConfig('total_copy', $validation), //total eksemplar (new_fields?)
        'collection_category' => null,
        'collection_subject' => null,
        'collection_condition' => null, //kondisi (new_fields?) 
        'collection_location' => null, //lokasi penyimpanan (one to many / many to many)
        'collection_contributor' => null, //contributor (pengarang)
        'collection_copy' => null, //collection eksemplar
        'description' => GeneralHelper::validateConfig('description', $validation), //ringkasan
        'cover' => GeneralHelper::validateConfig('cover', $validation), //cover
    ],
    '10' => [
        'form_type' => null, //jenis koleksi
        'code' => GeneralHelper::validateConfig('code', $validation),
        'serial' => null, //isbn
        'publisher_id' => GeneralHelper::validateConfig('publisher_id', $validation),
        'title' => GeneralHelper::validateConfig('title', $validation), //judul
        'publication_month' => GeneralHelper::validateConfig('publication_month', $validation), //bulan terbit
        'publication_year' => GeneralHelper::validateConfig('publication_year', $validation), //tahun terbit
        'received_at' => GeneralHelper::validateConfig('received_at', $validation), //tgl terima
        'total_page' => GeneralHelper::validateConfig('total_page', $validation), //deskripsi fisik 
        'dimension' => null, //deskripsi fisik
        'price' => null, //harga (new_fields?)
        'total_copy' => GeneralHelper::validateConfig('total_copy', $validation), //total eksemplar (new_fields?)
        'collection_category' => null,
        'collection_subject' => null,
        'collection_condition' => null, //kondisi (new_fields?) 
        'collection_contributor' => null, //contributor (pengarang)
        'collection_edition' => null, //edition for serial (pengarang)
        'collection_copy' => null, //collection eksemplar
        'description' => GeneralHelper::validateConfig('description', $validation), //ringkasan
        'cover' => GeneralHelper::validateConfig('cover', $validation), //cover
    ],
    '11' => [
        'form_type' => null, //jenis koleksi
        'code' => GeneralHelper::validateConfig('code', $validation),
        'serial' => null, //isbn
        'publisher_id' => GeneralHelper::validateConfig('publisher_id', $validation),
        'title' => GeneralHelper::validateConfig('title', $validation), //judul
        'publication_month' => GeneralHelper::validateConfig('publication_month', $validation), //bulan terbit
        'publication_year' => GeneralHelper::validateConfig('publication_year', $validation), //tahun terbit
        'received_at' => GeneralHelper::validateConfig('received_at', $validation), //tgl terima
        'total_page' => GeneralHelper::validateConfig('total_page', $validation), //deskripsi fisik 
        'dimension' => null, //deskripsi fisik
        'price' => null, //harga (new_fields?)
        'total_copy' => GeneralHelper::validateConfig('total_copy', $validation), //total eksemplar (new_fields?)
        'collection_category' => null,
        'collection_subject' => null,
        'collection_condition' => null, //kondisi (new_fields?) 
        'collection_contributor' => null, //contributor (pengarang)
        'collection_edition' => null, //edition for serial (pengarang)
        'collection_copy' => null, //collection eksemplar
        'description' => GeneralHelper::validateConfig('description', $validation), //ringkasan
        'cover' => GeneralHelper::validateConfig('cover', $validation), //cover
    ],
    '12' => [
        'form_type' => null, //jenis koleksi
        'code' => GeneralHelper::validateConfig('code', $validation),
        'publisher_id' => GeneralHelper::validateConfig('publisher_id', $validation),
        'title' => GeneralHelper::validateConfig('title', $validation), //judul
        'series' => null, // volume 
        'edition' => null, // volume
        'publication_month' => GeneralHelper::validateConfig('publication_month', $validation), //bulan terbit
        'publication_year' => GeneralHelper::validateConfig('publication_year', $validation), //tahun terbit
        'received_at' => GeneralHelper::validateConfig('received_at', $validation), //tgl terima
        'total_page' => GeneralHelper::validateConfig('total_page', $validation), //deskripsi fisik 
        'dimension' => null, //deskripsi fisik
        'price' => null, //harga (new_fields?)
        'total_copy' => GeneralHelper::validateConfig('total_copy', $validation), //total eksemplar (new_fields?)
        'collection_category' => null,
        'collection_subject' => null,
        'collection_condition' => null, //kondisi (new_fields?) 
        'collection_location' => null, //lokasi penyimpanan (one to many / many to many)
        'collection_contributor' => null, //contributor (pengarang)
        'collection_copy' => null, //collection eksemplar
        'description' => GeneralHelper::validateConfig('description', $validation), //ringkasan
        'cover' => GeneralHelper::validateConfig('cover', $validation), //cover
    ],
    '13' => [
        'form_type' => null, //jenis koleksi
        'code' => GeneralHelper::validateConfig('code', $validation),
        'publisher_id' => GeneralHelper::validateConfig('publisher_id', $validation),
        'title' => GeneralHelper::validateConfig('title', $validation), //judul
        'series' => null, // volume 
        'edition' => null, // volume
        'publication_month' => GeneralHelper::validateConfig('publication_month', $validation), //bulan terbit
        'publication_year' => GeneralHelper::validateConfig('publication_year', $validation), //tahun terbit
        'received_at' => GeneralHelper::validateConfig('received_at', $validation), //tgl terima
        'total_page' => GeneralHelper::validateConfig('total_page', $validation), //deskripsi fisik 
        'dimension' => null, //deskripsi fisik
        'price' => null, //harga (new_fields?)
        'total_copy' => GeneralHelper::validateConfig('total_copy', $validation), //total eksemplar (new_fields?)
        'collection_category' => null,
        'collection_subject' => null,
        'collection_condition' => null, //kondisi (new_fields?) 
        'collection_location' => null, //lokasi penyimpanan (one to many / many to many)
        'collection_contributor' => null, //contributor (pengarang)
        'collection_copy' => null, //collection eksemplar
        'description' => GeneralHelper::validateConfig('description', $validation), //ringkasan
        'cover' => GeneralHelper::validateConfig('cover', $validation), //cover
    ],
    '14' => [
        'form_type' => null, //jenis koleksi
        'code' => GeneralHelper::validateConfig('code', $validation),
        'publisher_id' => GeneralHelper::validateConfig('publisher_id', $validation),
        'title' => GeneralHelper::validateConfig('title', $validation), //judul
        'series' => null, // volume 
        'edition' => null, // volume
        'publication_month' => GeneralHelper::validateConfig('publication_month', $validation), //bulan terbit
        'publication_year' => GeneralHelper::validateConfig('publication_year', $validation), //tahun terbit
        'received_at' => GeneralHelper::validateConfig('received_at', $validation), //tgl terima
        'total_page' => GeneralHelper::validateConfig('total_page', $validation), //deskripsi fisik 
        'dimension' => null, //deskripsi fisik
        'price' => null, //harga (new_fields?)
        'total_copy' => GeneralHelper::validateConfig('total_copy', $validation), //total eksemplar (new_fields?)
        'collection_category' => null,
        'collection_subject' => null,
        'collection_condition' => null, //kondisi (new_fields?) 
        'collection_location' => null, //lokasi penyimpanan (one to many / many to many)
        'collection_contributor' => null, //contributor (pengarang)
        'collection_copy' => null, //collection eksemplar
        'description' => GeneralHelper::validateConfig('description', $validation), //ringkasan
        'cover' => GeneralHelper::validateConfig('cover', $validation), //cover
    ],
    '15' => [
        'form_type' => null, //jenis koleksi
        'code' => GeneralHelper::validateConfig('code', $validation),
        'publisher_id' => GeneralHelper::validateConfig('publisher_id', $validation),
        'title' => GeneralHelper::validateConfig('title', $validation), //judul
        'series' => null, // volume 
        'edition' => null, // volume
        'publication_month' => GeneralHelper::validateConfig('publication_month', $validation), //bulan terbit
        'publication_year' => GeneralHelper::validateConfig('publication_year', $validation), //tahun terbit
        'received_at' => GeneralHelper::validateConfig('received_at', $validation), //tgl terima
        'total_page' => GeneralHelper::validateConfig('total_page', $validation), //deskripsi fisik 
        'dimension' => null, //deskripsi fisik
        'price' => null, //harga (new_fields?)
        'total_copy' => GeneralHelper::validateConfig('total_copy', $validation), //total eksemplar (new_fields?)
        'collection_category' => null,
        'collection_subject' => null,
        'collection_condition' => null, //kondisi (new_fields?) 
        'collection_location' => null, //lokasi penyimpanan (one to many / many to many)
        'collection_contributor' => null, //contributor (pengarang)
        'collection_copy' => null, //collection eksemplar
        'description' => GeneralHelper::validateConfig('description', $validation), //ringkasan
        'cover' => GeneralHelper::validateConfig('cover', $validation), //cover
    ],
];
