<?php

namespace app\controllers;

use Yii;
use app\models\Authors;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;

class AuthorController extends Controller
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
    public function actionIndex()
    {
        return $this->redirect(['site/authors']);
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
        $model = new Authors();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Автор успешно добавлен');
            return $this->redirect(['site/authors']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Автор успешно обновлен');
            return $this->redirect(['site/authors']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $transaction = Yii::$app->db->beginTransaction();

        try {
            Yii::$app->db->createCommand()
                ->delete('book_authors', ['author_id' => $model->id])
                ->execute();

            if ($model->delete()) {
                $transaction->commit();

                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'success' => true,
                        'message' => 'Автор успешно удален. Связи с книгами были удалены.',
                        'redirect' => Yii::$app->urlManager->createUrl(['site/authors'])
                    ];
                } else {
                    Yii::$app->session->setFlash('success', 'Автор успешно удален. Связи с книгами были удалены.');
                    return $this->redirect(['site/authors']);
                }
            } else {
                throw new \Exception('Не удалось удалить автора');
            }

        } catch (\Exception $e) {
            $transaction->rollBack();

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => 'Ошибка при удалении автора: ' . $e->getMessage()
                ];
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка при удалении автора: ' . $e->getMessage());
                return $this->redirect(['site/authors']);
            }
        }
    }
    protected function findModel($id)
    {
        if (($model = Authors::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Автор не найден');
    }
}