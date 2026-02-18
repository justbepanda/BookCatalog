<?php

class m260218_065903_create_users_table extends CDbMigration
{
    public function up()
    {
        $this->createTable('users', [
            'id' => 'pk',
            'username' => 'string NOT NULL',
            'password_hash' => 'string NOT NULL',
            'role' => "string NOT NULL DEFAULT 'user'",
            'created_at' => 'datetime NOT NULL',
            'updated_at' => 'datetime NOT NULL',
        ]);

        $this->createIndex('idx_users_username', 'users', 'username', true);
    }

    public function down()
    {
        $this->dropTable('users');
    }
}