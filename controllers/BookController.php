<?php

namespace app\controllers;

use Yii;
use app\models\Book;
use app\models\Authors;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class BookController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if ($action->id === 'delete') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $model = new Book();

        if ($model->load(Yii::$app->request->post())) {
            $model->coverFile = UploadedFile::getInstance($model, 'coverFile');

            $model->authorIds = Yii::$app->request->post('Book')['authorIds'] ?? [];

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Книга успешно добавлена');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка при добавлении книги');

                Yii::error('Ошибки при создании книги: ' . print_r($model->errors, true));
            }
        }

        $authors = Authors::find()->all();

        return $this->render('create', [
            'model' => $model,
            'authors' => $authors,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->coverFile = UploadedFile::getInstance($model, 'coverFile');
            $model->authorIds = Yii::$app->request->post('Book')['authorIds'] ?? [];

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Книга успешно обновлена');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка при обновлении книги');
            }
        }

        $authors = Authors::find()->all();

        return $this->render('update', [
            'model' => $model,
            'authors' => $authors,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->delete()) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'message' => 'Книга успешно удалена',
                    'redirect' => Yii::$app->urlManager->createUrl(['site/books'])
                ];
            } else {
                Yii::$app->session->setFlash('success', 'Книга успешно удалена');
                return $this->redirect(['site/books']);
            }
        } else {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => 'Ошибка при удалении книги'
                ];
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка при удалении книги');
                return $this->redirect(['site/books']);
            }
        }
    }

    protected function findModel($id)
    {
        if (($model = Book::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Книга не найдена');
    }
}