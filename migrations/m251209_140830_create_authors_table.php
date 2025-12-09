<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%authors}}`.
 */
class m251209_140830_create_authors_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%authors}}', [
            'id' => $this->bigPrimaryKey(),
            'full_name' => $this->string(256)->notNull(),
        ]);

        // Опционально: индекс для поиска по имени
        $this->createIndex(
            'idx-authors-full_name',
            '{{%authors}}',
            'full_name'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%authors}}');
    }
}