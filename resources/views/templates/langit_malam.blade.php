{{--
  TEMPLATE: Langit Malam
  File ini yang kamu edit untuk ubah tampilan tema Langit Malam / rasi bintang.
--}}
@php
$theme = [
    'bg' => '#0b1320',
    'text' => '#f5f0e8',
    'muted' => 'rgba(245,240,232,0.65)',
    'accent' => '#d4b56a',
    'card' => 'rgba(255,255,255,0.06)',
    'btn_text' => '#1a1510',
    'border' => 'rgba(212,181,106,0.2)',
    'nav_bg' => 'rgba(15,20,30,0.92)',
    'input_bg' => 'rgba(255,255,255,0.06)',
    'overlay' => 'linear-gradient(180deg, rgba(0,0,0,0.35) 0%, rgba(11,19,32,0.88) 100%)',
    'cover_filter' => 'brightness(0.5)',
    'cover' => 'https://images.unsplash.com/photo-1419242902214-272b3f66ee7a?auto=format&fit=crop&w=900&q=80',
    'extra_css' => 'body { background-image: radial-gradient(ellipse at top, #152238 0%, #0b1320 55%); }',
];
@endphp
@include('templates._layout', ['undangan' => $undangan, 'theme' => $theme])
