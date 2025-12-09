<?php

namespace app\controllers;

use app\models\LoginForm;
use app\models\SignupForm;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\Response;

class AuthController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'get-token'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    if ($action->id === 'login' || $action->id === 'signup') {
                        Yii::$app->session->setFlash('info', 'Вы уже авторизованы');
                        return $this->goHome();
                    }
                    return $this->redirect(['auth/login']);
                },
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionLogin()
    {
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Yii::$app->session->setFlash('success', 'Вы успешно вошли в систему!');
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionSignup()
    {
        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->signup()) {
                Yii::$app->session->setFlash('success', 'Регистрация прошла успешно! Теперь вы можете войти в систему.');
                return $this->redirect(['auth/login']);
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        // Очищаем токен при выходе
        $user = Yii::$app->user->identity;
        $user->access_token = null;
        $user->save(false, ['access_token', 'updated_at']);

        Yii::$app->user->logout();
        Yii::$app->session->setFlash('success', 'Вы успешно вышли из системы.');
        return $this->goHome();
    }

    public function actionGetToken()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = Yii::$app->user->identity;
        return [
            'success' => true,
            'token' => $user->access_token,
            'username' => $user->username,
        ];
    }
}