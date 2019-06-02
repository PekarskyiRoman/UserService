<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%transaction}}`.
 */
class m190602_134140_create_transaction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%transaction}}', [
            'id' => $this->primaryKey(),
            'sender_login' => $this->string()->notNull(),
            'recipient_login' => $this->string()->notNull(),
            'amount' => $this->money(6,2)->notNull(),
        ]);
        $this->addForeignKey('fk_sender_login', 'transaction', 'sender_login', 'user', 'login');
        $this->addForeignKey('fk_recipient_login', 'transaction', 'recipient_login', 'user', 'login');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_sender_login', 'transaction');
        $this->dropForeignKey('fk_recipient_login', 'transaction');
        $this->dropTable('{{%transaction}}');
    }
}
