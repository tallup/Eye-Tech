<?php

return [
    // EDIT ME: replace with the real Gambian WhatsApp number, digits only, including country code 220, no +, no spaces.
    'whatsapp_number' => env('SITE_WHATSAPP_NUMBER', '2207000000'),

    // EDIT ME: replace with the real visible phone (free formatting allowed — used for display only).
    'phone_display' => env('SITE_PHONE_DISPLAY', '+220 700 0000'),

    // EDIT ME (optional): default WhatsApp opener message used when no per-CTA message is set.
    'whatsapp_default_message' => env('SITE_WHATSAPP_DEFAULT_MESSAGE', 'Hello EyeTech, I have a question.'),
];
