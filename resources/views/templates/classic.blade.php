{{--
  TEMPLATE: Classic Wedding
  File ini yang kamu edit untuk ubah tampilan tema Classic.
--}}
@php
$theme = [
    'bg' => '#234338',
    'text' => '#f5f0e8',
    'muted' => 'rgba(245,240,232,0.65)',
    'accent' => '#e8c9b8',
    'card' => 'rgba(255,255,255,0.08)',
    'btn_text' => '#1a1510',
    'border' => 'rgba(255,255,255,0.12)',
    'nav_bg' => 'rgba(255,255,255,0.95)',
    'input_bg' => 'rgba(255,255,255,0.06)',
    'overlay' => 'linear-gradient(180deg, rgba(35,67,56,0.35) 0%, rgba(35,67,56,0.85) 100%)',
    'cover_filter' => 'brightness(0.7)',
    'cover' => 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?auto=format&fit=crop&w=900&q=80',
    'extra_css' => '.nav-bottom a { color: #5a4a40; } .nav-bottom a:hover { color: #1a1510; }',
];
@endphp
@include('templates._layout', ['undangan' => $undangan, 'theme' => $theme])
