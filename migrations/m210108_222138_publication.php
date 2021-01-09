<?php

use yii\db\Migration;

/**
 * Class m210108_222138_publication
 */
class m210108_222138_publication extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('publication', [
            'publication_id' => $this->primaryKey()->notNull(),
            'text' => $this->text()->null(),
            'user_id' => $this->integer()->notNull(),
            'image_uuid' => $this->string()->null(),
            'like_count' => $this->integer()->defaultValue(0)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('publication');
    }
}
