<?php

namespace app\controllers;

use app\models\Book;
use app\models\Authors;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionBooks()
    {
        $books = Book::find()->all();
        return $this->render('books', ['books' => $books]);
    }

    public function actionAuthors()
    {
        $authors = Authors::find()->orderBy('full_name')->all();
        return $this->render('authors', ['authors' => $authors]);
    }

    public function actionReport($year = null)
    {
        if ($year === null) {
            $year = date('Y');
        }

        $currentYear = date('Y');
        if ($year > $currentYear || $year < ($currentYear - 9)) {
            $year = $currentYear;
        }

        $reportData = [];
        $authors = Authors::getTopAuthorsByYear($year);

        foreach ($authors as $author) {
            $reportData[] = [
                'id' => $author->id,
                'name' => $author->full_name,
                'book_count' => $author->getBooksCountByYear($year),
                'initials' => $author->getInitials(),
            ];
        }

        return $this->render('report', [
            'reportData' => $reportData,
            'year' => $year,
            'totalAuthors' => count($reportData),
        ]);
    }

    public function actionSubscribe()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $authorId = $request->post('author_id');
        $phone = $request->post('phone');

        if (empty($authorId) || empty($phone)) {
            return ['success' => false, 'message' => 'Заполните все поля'];
        }

        $phone = preg_replace('/\D/', '', $phone);
        if (strlen($phone) < 10) {
            return ['success' => false, 'message' => 'Некорректный номер телефона'];
        }

        return [
            'success' => true,
            'message' => 'Вы успешно подписались на обновления автора'
        ];
    }
}