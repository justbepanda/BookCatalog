<?php

class m260218_070818_create_book_author_table extends CDbMigration
{
    public function up()
    {
        $this->createTable('book_author', [
            'book_id' => 'integer NOT NULL',
            'author_id' => 'integer NOT NULL',
            'PRIMARY KEY(book_id, author_id)',
        ]);

        $this->addForeignKey(
            'fk_book_author_book',
            'book_author',
            'book_id',
            'books',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_book_author_author',
            'book_author',
            'author_id',
            'authors',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('book_author');
    }
}