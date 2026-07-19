{{--
  TEMPLATE: Elegant
  File ini yang kamu edit untuk ubah tampilan tema Elegant.
  Warna/gaya utama diatur lewat $theme di bawah.
--}}
@php
$theme = [
    'bg' => '#faf7f2',
    'text' => '#2c1810',
    'muted' => 'rgba(44,24,16,0.6)',
    'accent' => '#8b3a3a',
    'card' => '#ffffff',
    'btn_text' => '#ffffff',
    'border' => 'rgba(139,58,58,0.12)',
    'nav_bg' => 'rgba(255,255,255,0.92)',
    'input_bg' => '#ffffff',
    'overlay' => 'linear-gradient(180deg, rgba(250,247,242,0.25) 0%, rgba(250,247,242,0.92) 100%)',
    'cover_filter' => 'none',
    'cover' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=900&q=80',
    'extra_css' => '',
];
@endphp
@include('templates._layout', ['undangan' => $undangan, 'theme' => $theme])
