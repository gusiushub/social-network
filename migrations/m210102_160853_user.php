<?php

use yii\db\Migration;

/**
 * Class m210102_160853_user
 */
class m210102_160853_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user', [
            'user_id' => $this->primaryKey()->notNull(),
            'login' => $this->string()->notNull()->comment('Логин пользователя.'),
            'password' => $this->string()->notNull()->comment('Пароль пользователя'),
            'created_at' => $this->timestamp()->defaultExpression("now()")
                ->comment('Дата и время создания'),
            'access_token' => $this->string()->null()->unique(),
            'access_token_expired_at' => $this->string()->null()->unique(),
            'avatar_uuid' => $this->string()->null(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user');
    }
}
