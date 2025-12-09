<?php

namespace app\services;

use Yii;

class SmsPilotService
{
    /**
     * Отправка одного SMS через SMSPilot
     *
     * @param string $phone Номер телефона
     * @param string $text Текст сообщения
     * @return array Результат отправки
     */
    public static function send($phone, $text)
    {
        // Ключ эмулятор
        $apikey = 'XXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZXXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZ';
        $sender = 'INFORM';

        // Нормализуем номер телефона
        $phone = self::normalizePhone($phone);

        $url = 'https://smspilot.ru/api.php'
            .'?send='.urlencode($text)
            .'&to='.urlencode($phone)
            .'&from='.$sender
            .'&apikey='.$apikey
            .'&format=json';

        try {
            $json = file_get_contents($url);
            $result = json_decode($json, true);

            Yii::info("SMSPilot отправка: телефон={$phone}, ответ=" . $json, 'sms_notifications');

            return [
                'success' => !isset($result['error']),
                'data' => $result,
            ];

        } catch (\Exception $e) {
            Yii::error("SMSPilot ошибка: " . $e->getMessage(), 'sms_notifications');

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Нормализует номер телефона для SMSPilot
     */
    private static function normalizePhone($phone)
    {
        // Убираем всё кроме цифр
        $phone = preg_replace('/\D/', '', $phone);

        // Если номер начинается с 8, меняем на 7
        if (strlen($phone) === 11 && $phone[0] === '8') {
            $phone = '7' . substr($phone, 1);
        }

        // Если номер 10 цифр, добавляем 7
        if (strlen($phone) === 10) {
            $phone = '7' . $phone;
        }

        return $phone;
    }
}