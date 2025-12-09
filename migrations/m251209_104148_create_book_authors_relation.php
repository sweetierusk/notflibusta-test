<?php

use yii\db\Migration;

/**
 * Class m251209_104148_create_book_authors_relation
 */
class m251209_104148_create_book_authors_relation extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $tableExists = Yii::$app->db->getSchema()->getTableSchema('book_authors') !== null;

        if ($tableExists) {
            echo "Таблица book_authors уже существует. Удаляем...\n";
            $this->dropTable('{{%book_authors}}');
        }

        echo "Создаем таблицу book_authors...\n";
        $this->createTable('{{%book_authors}}', [
            'id' => $this->primaryKey(),
            'book_id' => $this->bigInteger()->notNull(),
            'author_id' => $this->bigInteger()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        echo "Создаем индексы...\n";
        $this->createIndex(
            'idx-book_authors-book_id',
            'book_authors',
            'book_id'
        );

        $this->createIndex(
            'idx-book_authors-author_id',
            'book_authors',
            'author_id'
        );

        $this->createIndex(
            'idx-book_authors-unique',
            'book_authors',
            ['book_id', 'author_id'],
            true
        );

        echo "Добавляем внешние ключи...\n";
        $this->addForeignKey(
            'fk-book_authors-book_id',
            'book_authors',
            'book_id',
            'books',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-book_authors-author_id',
            'book_authors',
            'author_id',
            'authors',
            'id',
            'CASCADE',
            'CASCADE'
        );

        echo "Таблица book_authors успешно создана!\n";
    }

    public function safeDown()
    {
        echo "Удаляем таблицу book_authors...\n";
        $this->dropForeignKey('fk-book_authors-author_id', 'book_authors');
        $this->dropForeignKey('fk-book_authors-book_id', 'book_authors');
        $this->dropTable('{{%book_authors}}');
    }
}