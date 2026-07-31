<?php

/**
 * Field groups yang bisa dipakai tiap template.
 * Form admin hanya menampilkan group yang ada di templates.*.fields
 */
$fieldGroups = [

    // ——— Wedding ———
    'mempelai' => [
        'label' => 'Data Mempelai',
        'kategori' => 'wedding',
    ],
    'ortu_mempelai' => [
        'label' => 'Orang Tua Mempelai',
        'kategori' => 'wedding',
    ],
    'foto_mempelai' => [
        'label' => 'Foto Mempelai',
        'kategori' => 'wedding',
    ],
    'foto_formal' => [
        'label' => 'Foto Formal',
        'kategori' => 'wedding',
    ],
    'akad' => [
        'label' => 'Akad Nikah',
        'kategori' => 'wedding',
    ],
    'resepsi' => [
        'label' => 'Resepsi',
        'kategori' => 'wedding',
    ],
    'cerita' => [
        'label' => 'Our Story / Timeline',
        'kategori' => ['wedding', 'ultah_anak'],
    ],
    'gift' => [
        'label' => 'Amplop Digital',
        'kategori' => 'wedding',
    ],

    // ——— Ultah ———
    'anak' => [
        'label' => 'Data Anak',
        'kategori' => 'ultah_anak',
    ],
    'ortu_host' => [
        'label' => 'Orang Tua / Host',
        'kategori' => 'ultah_anak',
    ],
    'foto_anak' => [
        'label' => 'Foto Anak',
        'kategori' => 'ultah_anak',
    ],
    'acara_pesta' => [
        'label' => 'Detail Pesta',
        'kategori' => 'ultah_anak',
    ],
    'dress_code' => [
        'label' => 'Dress Code',
        'kategori' => 'ultah_anak',
    ],
    'jadwal' => [
        'label' => 'Susunan Acara',
        'kategori' => 'ultah_anak',
    ],

    // ——— Couple ———
    'couple_nama' => [
        'label' => 'Data Pasangan',
        'kategori' => 'couple',
    ],
    'tanggal_spesial' => [
        'label' => 'Tanggal Spesial',
        'kategori' => 'couple',
    ],
    'surat_janji' => [
        'label' => 'Surat & Janji',
        'kategori' => 'couple',
    ],
    'alasan_sayang' => [
        'label' => 'Alasan Sayang',
        'kategori' => 'couple',
    ],
    'foto_couple' => [
        'label' => 'Foto Pasangan',
        'kategori' => 'couple',
    ],

    // ——— Shared ———
    'maps' => [
        'label' => 'Google Maps',
        'kategori' => ['wedding', 'ultah_anak'],
    ],
    'kutipan' => [
        'label' => 'Kutipan / Pesan',
        'kategori' => ['wedding', 'ultah_anak', 'couple'],
    ],
    'youtube' => [
        'label' => 'Musik (MP3)',
        'kategori' => ['wedding', 'ultah_anak', 'couple'],
    ],
    'galeri' => [
        'label' => 'Galeri Foto',
        'kategori' => ['wedding', 'ultah_anak', 'couple'],
    ],
];

return [

    'categories' => [
        'wedding' => [
            'id' => 'wedding',
            'nama' => 'Pernikahan',
            'tagline' => 'Undangan pernikahan digital',
            'deskripsi' => 'Dari nuansa elegan, klasik, langit malam, hingga adat Jawa — undangan pernikahan yang siap dibagikan lewat WhatsApp.',
            'icon' => 'fa-ring',
            'warna' => '#8b3a3a',
        ],
        'ultah_anak' => [
            'id' => 'ultah_anak',
            'nama' => 'Ulang Tahun Anak',
            'tagline' => 'Pesta ulang tahun si kecil',
            'deskripsi' => 'Undangan interaktif penuh warna — balon, lilin, permainan, konfirmasi hadir, dan kejutan manis untuk tamu cilik.',
            'icon' => 'fa-cake-candles',
            'warna' => '#e85d75',
        ],
        'couple' => [
            'id' => 'couple',
            'nama' => 'Untuk Pasangan',
            'tagline' => 'Surat cinta & kejutan pasangan',
            'deskripsi' => 'Bukan undangan acara — ini pengalaman digital romantis: surat spesial, hitung mundur, foto kenangan, dan janji manis.',
            'icon' => 'fa-heart',
            'warna' => '#c45c7a',
        ],
    ],

    'field_groups' => $fieldGroups,

    /*
    | fields = daftar group input yang muncul di form admin untuk template ini
    */
    'templates' => [

        'elegan' => [
            'id' => 'elegan',
            'kategori' => 'wedding',
            'nama' => 'Elegant',
            'deskripsi' => 'Bersih, lembut, mewah — cocok konsep minimalis & elegan.',
            'warna' => '#8b3a3a',
            'harga' => 250000,
            'preview' => null,
            // Sumber asli: template_undangan/.../template 1/index.html (fallback di renderer)
            'file' => 'template_undangan/template_wedding/template 1/index.html',
            'blade' => 'templates.elegan',
            'demo_url' => 'https://dream-wedding-steel.vercel.app/',
            'aktif' => true,
            'fields' => [
                'mempelai',
                'ortu_mempelai',
                'foto_mempelai',
                'akad',
                'resepsi',
                'maps',
                'kutipan',
                'youtube',
                'galeri',
                'cerita',
                'gift',
            ],
        ],
        'classic' => [
            'id' => 'classic',
            'kategori' => 'wedding',
            'nama' => 'Classic Wedding',
            'deskripsi' => 'Hangat & meriah — rasa nikahan pada umumnya, floral & khidmat.',
            'warna' => '#234338',
            'harga' => 250000,
            'preview' => null,
            'file' => 'template_undangan/template_wedding/template 2/index.html',
            'blade' => 'templates.classic',
            'demo_url' => 'https://dream-wedding-2.vercel.app/',
            'aktif' => true,
            'fields' => [
                'mempelai',
                'ortu_mempelai',
                'foto_mempelai',
                'akad',
                'resepsi',
                'maps',
                'kutipan',
                'youtube',
                'galeri',
                'cerita',
                'gift',
            ],
        ],
        'langit_malam' => [
            'id' => 'langit_malam',
            'kategori' => 'wedding',
            'nama' => 'Langit Malam',
            'deskripsi' => 'Gelap romantis — nuansa malam & rasi bintang.',
            'warna' => '#0b1320',
            'harga' => 275000,
            'preview' => null,
            'file' => 'template_undangan/template_wedding/template 3/index.html',
            'blade' => 'templates.langit_malam',
            'demo_url' => 'https://dream-wedding-3.vercel.app/',
            'aktif' => true,
            'fields' => [
                'mempelai',
                'ortu_mempelai',
                'foto_mempelai',
                'akad',
                'resepsi',
                'maps',
                'kutipan',
                'youtube',
                'galeri',
                'cerita',
                'gift',
            ],
        ],
        'adat_jawa' => [
            'id' => 'adat_jawa',
            'kategori' => 'wedding',
            'nama' => 'Adat Jawa',
            'deskripsi' => 'Nuansa tradisional Jawa — khidmat, budaya, dan elegan.',
            'warna' => '#6b3a2a',
            'harga' => 350000,
            'preview' => null,
            'file' => 'template_undangan/template_wedding/wedding_adat_jawa/template 4/index.html',
            'blade' => 'undangan.preview.wedding',
            'demo_url' => 'https://rayakanmomen.com/yumna',
            'aktif' => true,
            'fields' => [
                'mempelai',
                'ortu_mempelai',
                'foto_mempelai',
                'foto_formal',
                'akad',
                'resepsi',
                'maps',
                'kutipan',
                'youtube',
                'galeri',
                'cerita',
                'gift',
            ],
        ],

        'wedding_islam' => [
            'id' => 'wedding_islam',
            'kategori' => 'wedding',
            'nama' => 'Wedding Islami',
            'deskripsi' => 'Nuansa islami elegan — hijau zamrud, emas, gerbang undangan, dan ayat suci.',
            'warna' => '#0e4a38',
            'harga' => 300000,
            'preview' => null,
            'file' => 'template_undangan/template_wedding/wedding_islam/index.html',
            'blade' => 'undangan.preview.wedding',
            'demo_url' => 'https://rayakanmomen.com/james',
            'aktif' => true,
            'fields' => [
                'mempelai',
                'ortu_mempelai',
                'foto_mempelai',
                'akad',
                'resepsi',
                'maps',
                'kutipan',
                'youtube',
                'galeri',
                'cerita',
                'gift',
            ],
        ],

        // Candyland: punya susunan acara + dress code, tanpa story panjang
        'ultah_candyland' => [
            'id' => 'ultah_candyland',
            'kategori' => 'ultah_anak',
            'nama' => 'Candyland Adventure',
            'deskripsi' => 'Pesta balon & permen — pecah balon, tiup lilin, RSVP, dan galeri ceria.',
            'warna' => '#e85d75',
            'harga' => 200000,
            'preview' => null,
            'file' => 'template_undangan/template_ultah/template ultah 1/index.html',
            'blade' => 'undangan.preview.ultah',
            'demo_url' => 'https://ultah-anak-anak.vercel.app/',
            'aktif' => true,
            'fields' => [
                'anak',
                'ortu_host',
                'foto_anak',
                'acara_pesta',
                'maps',
                'dress_code',
                'jadwal',
                'kutipan',
                'youtube',
                'galeri',
            ],
        ],
        // Pesta Bintang: ada timeline perjalanan + dress code, tanpa jadwal rinci
        'ultah_bintang' => [
            'id' => 'ultah_bintang',
            'kategori' => 'ultah_anak',
            'nama' => 'Pesta Bintang',
            'deskripsi' => 'Tema langit malam si kecil — ganti outfit, tiup lilin, kapsul bintang, dan RSVP.',
            'warna' => '#6b5b95',
            'harga' => 225000,
            'preview' => null,
            'file' => 'template_undangan/template_ultah/template ultah 2/index.blade.php',
            'blade' => 'undangan.preview.ultah',
            'demo_url' => 'https://ulang-tahun-ku.vercel.app/',
            'aktif' => true,
            'fields' => [
                'anak',
                'ortu_host',
                'foto_anak',
                'acara_pesta',
                'maps',
                'dress_code',
                'kutipan',
                'cerita',
                'youtube',
                'galeri',
            ],
        ],

        // Couple: surat, janji, alasan sayang, countdown — tanpa maps/gift
        'couple_surat' => [
            'id' => 'couple_surat',
            'kategori' => 'couple',
            'nama' => 'Surat Spesial',
            'deskripsi' => 'Amplop digital, countdown ulang tahun, galeri momen, tiup lilin, dan janji manis.',
            'warna' => '#c45c7a',
            'harga' => 150000,
            'preview' => null,
            'file' => 'template_undangan/template couple/index.html',
            'blade' => 'undangan.preview.couple',
            'demo_url' => 'https://for-my-love-five-rho.vercel.app/',
            'aktif' => true,
            'fields' => [
                'couple_nama',
                'tanggal_spesial',
                'surat_janji',
                'alasan_sayang',
                'foto_couple',
                'kutipan',
                'youtube',
                'galeri',
            ],
        ],
    ],
];
