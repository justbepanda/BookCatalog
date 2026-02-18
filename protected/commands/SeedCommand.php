<?php

class SeedCommand extends CConsoleCommand
{
    /**
     * Заполняет базу тестовыми данными
     */
    public function actionRun()
    {
        $this->seedUsers();
        $this->seedAuthors();
        $this->seedBooks();
        $this->seedSubscriptions();

        echo "Seeding finished.\n";
    }

    /**
     * Сидинг пользователей
     */
    protected function seedUsers()
    {
        $username = 'admin';
        if (!User::model()->exists('username=:u', [':u' => $username])) {
            $user = new User();
            $user->username = $username;
            $user->password_hash = password_hash('123456', PASSWORD_DEFAULT);
            $user->role = 'user';
            $user->created_at = date('Y-m-d H:i:s');
            $user->save(false);
            echo "User '{$username}' created.\n";
        } else {
            echo "User '{$username}' already exists.\n";
        }
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
            ['title' => 'Книга 1', 'year' => 2020, 'isbn' => '1111-1111', 'description' => 'Описание книги 1'],
            ['title' => 'Книга 2', 'year' => 2021, 'isbn' => '2222-2222', 'description' => 'Описание книги 2'],
            ['title' => 'Книга 3', 'year' => 2022, 'isbn' => '3333-3333', 'description' => 'Описание книги 3'],
        ];

        foreach ($booksData as $data) {
            if (!Book::model()->exists('isbn=:i', [':i' => $data['isbn']])) {
                $book = new Book();
                $book->attributes = $data;
                $book->created_at = date('Y-m-d H:i:s');
                $book->save(false);

                // привязка авторов через pivot
                shuffle($authors);
                $linkedAuthors = array_slice($authors, 0, rand(1, 2));
                foreach ($linkedAuthors as $author) {
                    $sql = "INSERT IGNORE INTO book_author (book_id, author_id) VALUES (:book_id, :author_id)";
                    Yii::app()->db->createCommand($sql)->execute([
                        ':book_id' => $book->id,
                        ':author_id' => $author->id,
                    ]);
                }
            }
        }
        echo "Books and book-author links seeded.\n";
    }

    /**
     * Сидинг подписок на авторов
     */
    protected function seedSubscriptions()
    {
        $authors = Author::model()->findAll();
        foreach ($authors as $author) {
            $exists = Subscription::model()->exists('author_id=:a AND phone=:p', [
                ':a' => $author->id,
                ':p' => '+7999000' . rand(1000, 9999),
            ]);

            if (!$exists) {
                $sub = new Subscription();
                $sub->author_id = $author->id;
                $sub->phone = '+7999000' . rand(1000, 9999);
                $sub->created_at = date('Y-m-d H:i:s');
                $sub->save(false);
            }
        }
        echo "Subscriptions seeded.\n";
    }
}
