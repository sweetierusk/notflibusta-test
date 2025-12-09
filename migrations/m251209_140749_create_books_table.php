<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%books}}`.
 */
class m251209_140749_create_books_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%books}}', [
            'id' => $this->bigPrimaryKey(),
            'book_name' => $this->string(256),
            'release_year' => $this->date(),
            'description' => $this->string(2000),
            'isbn' => $this->string(20),
            'img_link' => $this->string(512),
        ]);

        // Индекс для ISBN для быстрого поиска
        $this->createIndex(
            'idx-books-isbn',
            '{{%books}}',
            'isbn'
        );

        // Индекс для названия книги
        $this->createIndex(
            'idx-books-book_name',
            '{{%books}}',
            'book_name'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%books}}');
    }
}