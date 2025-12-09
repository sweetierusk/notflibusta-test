<?php

namespace app\models;

use yii\db\ActiveRecord;
class BookAuthor extends ActiveRecord
{
    public static function tableName()
    {
        return 'book_authors';
    }

    public function rules()
    {
        return [
            [['book_id', 'author_id'], 'required'],
            [['book_id', 'author_id'], 'integer'],
            [['book_id'], 'exist', 'skipOnError' => true, 'targetClass' => Book::class, 'targetAttribute' => ['book_id' => 'id']],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Authors::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'book_id' => 'Книга',
            'author_id' => 'Автор',
        ];
    }
    public function getBook()
    {
        return $this->hasOne(Book::class, ['id' => 'book_id']);
    }
    public function getAuthor()
    {
        return $this->hasOne(Authors::class, ['id' => 'author_id']);
    }
}