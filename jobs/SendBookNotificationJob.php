<?php

namespace app\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use app\models\Subscribe;
use app\models\Authors;
use app\services\SmsPilotService;

class SendBookNotificationJob extends BaseObject implements JobInterface
{
    public $bookId;
    public $bookName;
    public $bookIsbn;
    public $authorIds;

    public function execute($queue)
    {
        Yii::info("Начинаем отправку SMS для книги ID: {$this->bookId} '{$this->bookName}'", 'sms_notifications');

        // Получаем имена авторов
        $authorNames = [];
        foreach ($this->authorIds as $authorId) {
            $author = Authors::findOne($authorId);
            if ($author) {
                $authorNames[] = $author->full_name;
            }
        }

        $authorsString = !empty($authorNames) ? implode(', ', $authorNames) : 'Неизвестный автор';

        // Формируем текст сообщения
        $isbnInfo = !empty($this->bookIsbn) ? "ISBN: {$this->bookIsbn}" : "ID: {$this->bookId}";
        $text = "Новая книга '{$this->bookName}' от {$authorsString}. {$isbnInfo}";

        // Обрезаем если слишком длинное (макс 160 символов для 1 SMS)
        if (mb_strlen($text) > 160) {
            $text = mb_substr($text, 0, 157) . '...';
            Yii::info("Текст SMS обрезан: {$text}", 'sms_notifications');
        }

        // Отправляем SMS каждому подписчику
        $totalSent = 0;
        $totalFailed = 0;

        foreach ($this->authorIds as $authorId) {
            $phones = Subscribe::getSubscriberPhonesByAuthor($authorId);

            if (!empty($phones)) {
                Yii::info("Автор ID {$authorId} имеет " . count($phones) . " подписчиков", 'sms_notifications');

                foreach ($phones as $phone) {
                    $result = SmsPilotService::send($phone, $text);

                    if ($result['success']) {
                        Yii::info("SMS отправлено на {$phone}: {$text}", 'sms_notifications');
                        $totalSent++;
                    } else {
                        Yii::error("Ошибка отправки SMS на {$phone}: " . json_encode($result), 'sms_notifications');
                        $totalFailed++;
                    }

                    // Небольшая пауза между отправками (0.1 секунда)
                    usleep(100000);
                }
            } else {
                Yii::info("У автора ID {$authorId} нет подписчиков", 'sms_notifications');
            }
        }

        Yii::info("Итог отправки SMS для книги '{$this->bookName}': отправлено {$totalSent}, ошибок {$totalFailed}", 'sms_notifications');
    }
}