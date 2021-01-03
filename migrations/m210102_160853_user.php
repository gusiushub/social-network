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
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210102_160853_user cannot be reverted.\n";

        return false;
    }
    */
}
