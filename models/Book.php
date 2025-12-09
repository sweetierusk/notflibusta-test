<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

class Book extends ActiveRecord
{
    public $coverFile;

    public $authorIds = [];

    public static function tableName()
    {
        return 'books';
    }

    public function rules()
    {
        return [
            [['book_name', 'release_year', 'description', 'isbn'], 'required'],
            [['description'], 'string', 'max' => 512],
            [['book_name'], 'string', 'max' => 256],
            [['isbn'], 'string', 'max' => 20],
            [['isbn'], 'unique', 'message' => 'Книга с таким ISBN уже существует'],
            [['img_link'], 'string', 'max' => 2000],
            [['release_year'], 'date', 'format' => 'php:Y-m-d'],
            [['release_year'], 'validateYear'],
            [['authorIds'], 'safe'],
            [['coverFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif', 'maxSize' => 2 * 1024 * 1024],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'book_name' => 'Название книги',
            'release_year' => 'Год выпуска',
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'img_link' => 'Ссылка на обложку',
            'coverFile' => 'Загрузить обложку',
            'authorIds' => 'Авторы',
        ];
    }

    public function validateYear($attribute, $params)
    {
        $currentYear = date('Y');
        $bookYear = date('Y', strtotime($this->release_year));

        if ($bookYear > $currentYear) {
            $this->addError($attribute, 'Год выпуска не может быть в будущем');
        }
    }

    public function getAuthors()
    {
        return $this->hasMany(Authors::class, ['id' => 'author_id'])
            ->viaTable('book_authors', ['book_id' => 'id']);
    }

    public function getBookAuthors()
    {
        return $this->hasMany(BookAuthor::class, ['book_id' => 'id']);
    }

    public function getAuthorsString()
    {
        $authors = $this->authors;
        if (empty($authors)) {
            return 'Автор не указан';
        }

        $names = [];
        foreach ($authors as $author) {
            $names[] = $author->full_name;
        }
        return implode(', ', $names);
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->authorIds = [];
        foreach ($this->authors as $author) {
            $this->authorIds[] = $author->id;
        }
    }

    public function getCoverUrl()
    {
        if (!empty($this->img_link)) {
            if (strpos($this->img_link, 'http') === 0) {
                return $this->img_link;
            }

            return Yii::getAlias('@web') . '/' . ltrim($this->img_link, '/');
        }

        return $this->getDefaultCover();
    }

    public function getDefaultCover()
    {
        $defaultCover = 'img/default_cover.jpg';

        $filePath = Yii::getAlias('@webroot') . '/' . $defaultCover;
        if (file_exists($filePath)) {
            return Yii::getAlias('@web') . '/' . $defaultCover;
        }

        return 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
    }

    public function upload()
    {
        if ($this->coverFile === null) {
            return true;
        }

        $uploadDir = Yii::getAlias('@webroot/img/covers/');
        if (!is_dir($uploadDir)) {
            FileHelper::createDirectory($uploadDir, 0775, true);
        }

        $fileName = 'book_' . ($this->isNewRecord ? 'new' : $this->id) . '_' . time() . '_' . Yii::$app->security->generateRandomString(6) . '.' . $this->coverFile->extension;
        $filePath = $uploadDir . $fileName;

        if ($this->coverFile->saveAs($filePath)) {
            $this->img_link = 'img/covers/' . $fileName;

            if (!$this->isNewRecord && !empty($this->oldAttributes['img_link']) &&
                strpos($this->oldAttributes['img_link'], 'http') !== 0 &&
                strpos($this->oldAttributes['img_link'], 'img/covers/') === 0) {

                $oldFilePath = Yii::getAlias('@webroot') . '/' . $this->oldAttributes['img_link'];
                if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                    @unlink($oldFilePath);
                }
            }

            return true;
        }

        return false;
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            if (!parent::save($runValidation, $attributeNames)) {
                $transaction->rollBack();
                return false;
            }

            if ($this->coverFile !== null) {
                if (!$this->upload()) {
                    $this->addError('coverFile', 'Не удалось загрузить обложку');
                    $transaction->rollBack();
                    return false;
                }

                if (!parent::save(false, ['img_link'])) {
                    $transaction->rollBack();
                    return false;
                }
            }

            $this->saveAuthors();

            $transaction->commit();
            return true;

        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->authorIds !== null && is_array($this->authorIds)) {
            $this->saveAuthors();
        }
    }

    protected function saveAuthors()
    {
        BookAuthor::deleteAll(['book_id' => $this->id]);

        if (!empty($this->authorIds) && is_array($this->authorIds)) {
            foreach ($this->authorIds as $authorId) {
                $bookAuthor = new BookAuthor();
                $bookAuthor->book_id = $this->id;
                $bookAuthor->author_id = $authorId;

                if (!$bookAuthor->save()) {
                    Yii::error('Не удалось сохранить связь с автором: ' . print_r($bookAuthor->errors, true));
                }
            }
        }
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        \Yii::$app->db->createCommand()
            ->delete('book_authors', ['book_id' => $this->id])
            ->execute();

        return true;
    }

    public function afterDelete()
    {
        parent::afterDelete();

        // Удаляем файл обложки
        if (!empty($this->img_link) &&
            strpos($this->img_link, 'http') !== 0 &&
            strpos($this->img_link, 'img/covers/') === 0) {

            $filePath = Yii::getAlias('@webroot') . '/' . $this->img_link;
            if (file_exists($filePath) && is_file($filePath)) {
                @unlink($filePath);
            }
        }
    }

    public function getFormattedReleaseYear()
    {
        return date('Y', strtotime($this->release_year));
    }
}