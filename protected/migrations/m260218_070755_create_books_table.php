<?php

class m260218_070755_create_books_table extends CDbMigration
{
    public function up()
    {
        $this->createTable('books', [
            'id' => 'pk',
            'title' => 'string NOT NULL',
            'year' => 'integer NOT NULL',
            'description' => 'text',
            'isbn' => 'string NOT NULL',
            'image_path' => 'string',
            'created_at' => 'datetime NOT NULL',
            'updated_at' => 'datetime NOT NULL',
        ]);

        $this->createIndex('idx_books_isbn', 'books', 'isbn', true);
        $this->createIndex('idx_books_year', 'books', 'year');
    }

    public function down()
    {
        $this->dropTable('books');
    }
}