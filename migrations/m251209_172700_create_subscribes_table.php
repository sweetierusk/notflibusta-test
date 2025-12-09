<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subscribes}}`.
 */
class m251209_172700_create_subscribes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%subscribes}}', [
            'id' => $this->primaryKey(),
            'phone' => $this->string(20)->notNull(),
            'author_id' => $this->bigInteger()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // Внешний ключ на subscriber_phone
        $this->addForeignKey(
            'fk-subscribes-phone',
            '{{%subscribes}}',
            'phone',
            '{{%subscriber_phone}}',
            'phone',
            'CASCADE',
            'CASCADE'
        );

        // Внешний ключ на authors
        $this->addForeignKey(
            'fk-subscribes-author_id',
            '{{%subscribes}}',
            'author_id',
            '{{%authors}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Уникальный индекс, чтобы не было дубликатов подписок
        $this->createIndex(
            'idx-subscribes-phone-author_id-unique',
            '{{%subscribes}}',
            ['phone', 'author_id'],
            true
        );

        // Индексы для быстрого поиска
        $this->createIndex(
            'idx-subscribes-phone',
            '{{%subscribes}}',
            'phone'
        );

        $this->createIndex(
            'idx-subscribes-author_id',
            '{{%subscribes}}',
            'author_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-subscribes-author_id', '{{%subscribes}}');
        $this->dropForeignKey('fk-subscribes-phone', '{{%subscribes}}');
        $this->dropTable('{{%subscribes}}');
    }
}