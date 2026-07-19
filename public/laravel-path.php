<?php

/**
 * Path ke project Laravel (folder yang berisi vendor/, app/, .env).
 *
 * LOCAL (Laragon): biarkan null.
 *
 * SERVER (cPanel kamu): project terpisah dari public_html, isi path absolut, contoh:
 *   return '/home/rayakanm/website-undangan';
 *
 * Struktur server:
 *   /home/rayakanm/website-undangan/  ← app, vendor, .env
 *   /home/rayakanm/public_html/       ← isi folder public/
 */
return null;
