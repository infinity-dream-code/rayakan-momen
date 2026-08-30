<?php

if (! function_exists('wa_number')) {
    /** Nomor WA internasional tanpa + (untuk wa.me). */
    function wa_number(): string
    {
        return preg_replace('/\D+/', '', (string) config('undangan.wa_number', '6285777433886')) ?: '6285777433886';
    }
}

if (! function_exists('wa_display')) {
    /** Nomor WA tampilan (format lokal). */
    function wa_display(): string
    {
        return (string) config('undangan.wa_display', '0857-7743-3886');
    }
}

if (! function_exists('wa_me_url')) {
    function wa_me_url(?string $text = null): string
    {
        $url = 'https://wa.me/'.wa_number();
        if ($text !== null && $text !== '') {
            $url .= '?text='.rawurlencode($text);
        }

        return $url;
    }
}

if (! function_exists('wa_tel')) {
    function wa_tel(): string
    {
        return '+'.wa_number();
    }
}
