<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subscriber_phone}}`.
 */
class m251209_172628_create_subscriber_phone_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%subscriber_phone}}', [
            'phone' => $this->string(20)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // Делаем phone первичным ключом
        $this->addPrimaryKey('pk-subscriber_phone-phone', '{{%subscriber_phone}}', 'phone');

        // Индекс для поиска
        $this->createIndex(
            'idx-subscriber_phone-phone',
            '{{%subscriber_phone}}',
            'phone'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%subscriber_phone}}');
    }
}