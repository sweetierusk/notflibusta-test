<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',

    //TODO: удалить перед деплоем на гитхаб
    'telegram' => [
        'botToken' => '6952516016:AAEMzW19bWElWOsqJ2x8uXbnR-PPalqZQQU',
        'chatId' => '273492831',
        'apiUrl' => 'https://api.telegram.org/bot{token}/sendMessage',
    ],
];
