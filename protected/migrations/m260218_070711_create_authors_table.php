<?php

class m260218_070711_create_authors_table extends CDbMigration
{
    public function up()
    {
        $this->createTable('authors', [
            'id' => 'pk',
            'full_name' => 'string NOT NULL',
            'created_at' => 'datetime NOT NULL',
        ]);

        $this->createIndex('idx_authors_full_name', 'authors', 'full_name');
    }

    public function down()
    {
        $this->dropTable('authors');
    }
}