<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\Subscribe;

class SubscribeController extends Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        // Отключаем CSRF валидацию для AJAX запросов
        if ($action->id === 'subscribe') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * Обработка подписки на автора
     */
    public function actionSubscribe()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $authorId = $request->post('author_id');
        $phone = $request->post('phone');

        if (empty($authorId) || empty($phone)) {
            return ['success' => false, 'message' => 'Заполните все поля'];
        }

        // Создаём подписку через модель
        $result = Subscribe::createSubscription($authorId, $phone);

        return $result;
    }
}