<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class Authors extends ActiveRecord
{
    public static function tableName()
    {
        return 'authors';
    }

    public function rules()
    {
        return [
            [['full_name'], 'required'],
            [['full_name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'full_name' => 'ФИО автора',
        ];
    }

    public function getBooks()
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])
            ->viaTable('book_authors', ['author_id' => 'id']);
    }

    public function getBooksCount()
    {
        return $this->getBooks()->count();
    }

    public function getInitials()
    {
        $nameParts = explode(' ', $this->full_name);
        $initials = '';

        foreach ($nameParts as $part) {
            if (!empty($part)) {
                $initials .= mb_substr($part, 0, 1, 'UTF-8');
            }
        }

        return mb_strtoupper($initials, 'UTF-8');
    }

    public static function getTopAuthorsByYear($year)
    {
        return self::find()
            ->select([
                'authors.*',
                'COUNT(books.id) as book_count'
            ])
            ->innerJoin('book_authors', 'book_authors.author_id = authors.id')
            ->innerJoin('books', 'books.id = book_authors.book_id')
            ->where([
                'AND',
                ['>=', 'books.release_year', date('Y-m-d', strtotime($year . '-01-01'))],
                ['<=', 'books.release_year', date('Y-m-d', strtotime($year . '-12-31'))]
            ])
            ->groupBy(['authors.id'])
            ->orderBy(['book_count' => SORT_DESC])
            ->limit(10)
            ->all();
    }

    public function getBooksCountByYear($year)
    {
        return $this->getBooks()
            ->andWhere([
                'AND',
                ['>=', 'books.release_year', date('Y-m-d', strtotime($year . '-01-01'))],
                ['<=', 'books.release_year', date('Y-m-d', strtotime($year . '-12-31'))]
            ])
            ->count();
    }

    public function deleteBookRelations()
    {
        return Yii::$app->db->createCommand()
            ->delete('book_authors', ['author_id' => $this->id])
            ->execute();
    }

}