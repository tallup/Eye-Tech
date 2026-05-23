<?php

namespace App\Support;

class Whatsapp
{
    public static function url(?string $message = null): string
    {
        $number = preg_replace('/\D+/', '', (string) config('site.whatsapp_number'));
        $text = $message ?: config('site.whatsapp_default_message');

        return 'https://wa.me/'.$number.'?text='.rawurlencode($text);
    }
}
