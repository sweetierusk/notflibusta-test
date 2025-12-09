<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class QueueController extends Controller
{
    /**
     * Запускает воркер для обработки очереди
     */
    public function actionListen()
    {
        $queue = Yii::$app->queue;

        $this->stdout("Запуск воркера очереди...\n");
        $this->stdout("Канал: {$queue->channel}\n");
        $this->stdout("Нажмите Ctrl+C для остановки\n\n");

        // Запускаем воркер
        $queue->listen();

        return ExitCode::OK;
    }

    /**
     * Обрабатывает одно задание из очереди
     */
    public function actionRun()
    {
        $queue = Yii::$app->queue;

        $this->stdout("Обработка одного задания из очереди...\n");
        $queue->run();

        return ExitCode::OK;
    }

    /**
     * Показывает информацию об очереди
     */
    public function actionInfo()
    {
        $queue = Yii::$app->queue;

        $this->stdout("=== Информация об очереди ===\n");
        $this->stdout("Канал: {$queue->channel}\n");
        $this->stdout("Драйвер: " . get_class($queue) . "\n");

        // Для Redis получаем статистику
        if ($queue instanceof \yii\queue\redis\Queue) {
            $redis = $queue->redis;
            $waitingKey = $queue->channel . '.messages';
            $delayedKey = $queue->channel . '.delayed';
            $reservedKey = $queue->channel . '.reserved';

            $waitingCount = $redis->llen($waitingKey);
            $delayedCount = $redis->zcard($delayedKey);
            $reservedCount = $redis->zcard($reservedKey);

            $this->stdout("Заданий в ожидании: {$waitingCount}\n");
            $this->stdout("Отложенных заданий: {$delayedCount}\n");
            $this->stdout("Зарезервированных заданий: {$reservedCount}\n");
            $this->stdout("Всего заданий: " . ($waitingCount + $delayedCount + $reservedCount) . "\n");
        }

        return ExitCode::OK;
    }

    /**
     * Очищает очередь (осторожно!)
     */
    public function actionClear()
    {
        if (!$this->confirm("Вы уверены, что хотите очистить очередь? Все задания будут удалены.")) {
            $this->stdout("Отменено\n");
            return ExitCode::OK;
        }

        $queue = Yii::$app->queue;

        if ($queue instanceof \yii\queue\redis\Queue) {
            $redis = $queue->redis;
            $waitingKey = $queue->channel . '.messages';
            $delayedKey = $queue->channel . '.delayed';
            $reservedKey = $queue->channel . '.reserved';

            $redis->del($waitingKey, $delayedKey, $reservedKey);
            $this->stdout("Очередь очищена\n");
        } else {
            $this->stdout("Очистка доступна только для Redis очереди\n");
        }

        return ExitCode::OK;
    }

    /**
     * Тестирует добавление задания в очередь
     */
    public function actionTest()
    {
        $job = new \app\jobs\SendBookNotificationJob([
            'bookId' => 999,
            'bookName' => 'Тестовая книга',
            'authorIds' => [1],
        ]);

        Yii::$app->queue->push($job);
        $this->stdout("Тестовое задание добавлено в очередь\n");

        return ExitCode::OK;
    }
}