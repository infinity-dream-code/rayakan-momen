<?php

/**
 * Pintu masuk login admin (tanpa bergantung rewrite URL).
 *
 * Pakai salah satu:
 * - https://rayakanmomen.com/masuk.php
 * - https://rayakanmomen.com/index.php/panel/login
 * - https://rayakanmomen.com/panel/login.php
 */
header('Location: /index.php/panel/login', true, 302);
exit;
