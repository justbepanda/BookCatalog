<?php

class m260218_070934_create_subscriptions_table extends CDbMigration
{
    public function up()
    {
        $this->createTable('subscriptions', [
            'id' => 'pk',
            'author_id' => 'integer NOT NULL',
            'phone' => 'string NOT NULL',
            'created_at' => 'datetime NOT NULL',
            'updated_at' => 'datetime NOT NULL',
        ]);

        $this->createIndex(
            'uniq_author_phone',
            'subscriptions',
            'author_id, phone',
            true
        );

        $this->addForeignKey(
            'fk_subscription_author',
            'subscriptions',
            'author_id',
            'authors',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('subscriptions');
    }
}