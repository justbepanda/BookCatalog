<?php

/**
 * Команда для создания сидов
 */
class SeedCommand extends CConsoleCommand
{
    /**
     * Заполняет базу тестовыми данными
     */
    public function actionRun($fresh = false)
    {
        if ($fresh) {
            Yii::app()->db->createCommand('SET FOREIGN_KEY_CHECKS=0')->execute();

            Yii::app()->db->createCommand()->truncateTable('subscriptions');
            Yii::app()->db->createCommand()->truncateTable('book_author');
            Yii::app()->db->createCommand()->truncateTable('books');
            Yii::app()->db->createCommand()->truncateTable('authors');

            Yii::app()->db->createCommand('SET FOREIGN_KEY_CHECKS=1')->execute();

            echo "Tables cleared.\n";
        }

        $this->seedAuthors();
        $this->seedBooks();
        $this->seedSubscriptions();

        echo "Seeding finished.\n";
    }

    /**
     * Сидинг авторов
     */
    protected function seedAuthors()
    {
        $names = ['Иван Иванов', 'Пётр Петров', 'Мария Смирнова', 'Алексей Кузнецов'];
        foreach ($names as $name) {
            if (!Author::model()->exists('full_name=:n', [':n' => $name])) {
                $author = new Author();
                $author->full_name = $name;
                $author->created_at = date('Y-m-d H:i:s');
                $author->save(false);
            }
        }
        echo "Authors seeded.\n";
    }

    /**
     * Сидинг книг и связей с авторами
     */
    protected function seedBooks()
    {
        $authors = Author::model()->findAll();

        $booksData = [
            [
                'title' => 'Книга 1',
                'year' => 2020,
                'isbn' => '1111-1111',
                'description' => 'Описание книги 1',
                'image_path' => '/images/books/book1.jpg',
            ],
            [
                'title' => 'Книга 2',
                'year' => 2021,
                'isbn' => '2222-2222',
                'description' => 'Описание книги 2',
                'image_path' => '/images/books/book2.jpg',
            ],
            [
                'title' => 'Книга 3',
                'year' => 2022,
                'isbn' => '3333-3333',
                'description' => 'Описание книги 3',
                'image_path' => '/images/books/book3.jpg',
            ],
        ];

        foreach ($booksData as $data) {
            if (!Book::model()->exists('isbn=:i', [':i' => $data['isbn']])) {
                $book = new Book();
                $book->attributes = $data;
                $book->created_at = date('Y-m-d H:i:s');
                $book->updated_at = date('Y-m-d H:i:s');
                $book->save(false);

                // привязка авторов через pivot
                shuffle($authors);
                $count = rand(1, min(3, count($authors)));
                $linkedAuthors = array_slice($authors, 0, $count);

                foreach ($linkedAuthors as $author) {
                    Yii::app()->db->createCommand()
                        ->insert('book_author', [
                            'book_id' => $book->id,
                            'author_id' => $author->id,
                        ]);
                }
            }
        }

        echo "Books seeded.\n";
    }

    protected function seedSubscriptions()
    {
        $authors = Author::model()->findAll();
        foreach ($authors as $author) {
            $sub = new Subscription();
            $sub->author_id = $author->id;
            $sub->phone = '7999000' . rand(1000, 9999);
            $sub->save(false);
        }
        echo "Subscriptions created.\n";
    }
}
