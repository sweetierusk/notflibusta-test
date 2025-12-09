<?php

use yii\db\Migration;

class m251209_092528_create_user_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->bigPrimaryKey(),
            'username' => $this->string(64)->notNull()->unique(),
            'password_hash' => $this->string(256)->notNull(),
            'access_token' => $this->string(256)->null(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->null()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex('idx-user-username', '{{%user}}', 'username', true);

        $this->insert('{{%user}}', [
            'username' => 'admin',
            'password_hash' => Yii::$app->security->generatePasswordHash('admin123'),
            'access_token' => Yii::$app->security->generateRandomString(32),
            'status' => 10,
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}