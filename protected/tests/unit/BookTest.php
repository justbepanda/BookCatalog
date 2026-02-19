<?php

use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        if (Yii::app() === null) {
            $config = dirname(__FILE__) . '/../../config/test.php';
            Yii::createWebApplication($config);
        }
        // Очистка БД перед каждым тестом
        $db = Yii::app()->db;
        $db->createCommand('SET FOREIGN_KEY_CHECKS=0')->execute();
        $db->createCommand('DELETE FROM sms_queue')->execute();
        $db->createCommand('DELETE FROM book_author')->execute();
        $db->createCommand('DELETE FROM books')->execute();
        $db->createCommand('DELETE FROM subscriptions')->execute();
        $db->createCommand('DELETE FROM authors')->execute();
        $db->createCommand('SET FOREIGN_KEY_CHECKS=1')->execute();
    }

    public function testCreateBookWithAuthorsAndNotify()
    {
        // 1. Создаем автора
        $author = new Author();
        $author->full_name = 'Тестовый Автор';
        $author->save();

        // 2. Создаем подписчика на этого автора
        $sub = new Subscription();
        $sub->author_id = $author->id;
        $sub->phone = '79001112233';
        $sub->save();

        // 3. Создаем книгу (используя логику из нашей обновленной модели)
        $book = new Book();
        $book->title = 'Тестовая Книга';
        $book->year = 2024;
        $book->isbn = '111-222';
        $book->author_ids = [$author->id]; // Привязка через afterSave

        $this->assertTrue($book->save(), 'Книга должна успешно сохраниться');

        // 4. Проверяем связь в БД
        $linked = Yii::app()->db->createCommand()
            ->select('count(*)')
            ->from('book_author')
            ->where('book_id=:bid AND author_id=:aid', [':bid'=>$book->id, ':aid'=>$author->id])
            ->queryScalar();

        $this->assertEquals(1, $linked, 'Связь с автором должна быть создана в таблице book_author');

        // 5. Проверяем очередь СМС
        $sms = Yii::app()->db->createCommand()
            ->select('*')
            ->from('sms_queue')
            ->where('phone=:p', [':p'=>'79001112233'])
            ->queryRow();

        $this->assertNotFalse($sms, 'Запись в sms_queue должна существовать');
        $this->assertEquals('Новая книга: Тестовая Книга!', $sms['message']);
        $this->assertEquals(0, $sms['status']);
    }
}