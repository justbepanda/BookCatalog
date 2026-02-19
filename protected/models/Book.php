<?php

class Book extends CActiveRecord
{
    public $author_ids = [];
    public $new_author_names;

    public function tableName()
    {
        return 'books';
    }

    public function rules()
    {
        return [
            ['title, year, isbn', 'required'],
            ['title', 'length', 'max' => 255],
            ['year', 'numerical', 'integerOnly' => true],
            ['isbn', 'length', 'max' => 20],
            ['description, image_path', 'safe'],
            ['author_ids, new_author_names', 'safe'],
        ];
    }

    public function relations()
    {
        return [
            'authors' => [self::MANY_MANY, 'Author', 'book_author(book_id, author_id)'],
        ];
    }

    /**
     * Логика после сохранения: работа со связями и уведомлениями
     */
    protected function afterSave()
    {
        parent::afterSave();

        Yii::app()->db->createCommand()
            ->delete('book_author', 'book_id=:id', [':id' => $this->id]);

        $allAuthorIds = [];

        // Обработка существующих ID авторов из мультиселекта
        if (!empty($this->author_ids) && is_array($this->author_ids)) {
            foreach ($this->author_ids as $id) {
                if (is_numeric($id)) {
                    $this->linkAuthor($id);
                    $allAuthorIds[] = $id;
                }
            }
        }

        // Обработка новых авторов
        if (!empty($this->new_author_names)) {
            $names = explode(',', $this->new_author_names);
            foreach ($names as $name) {
                $trimmedName = trim($name);
                if ($trimmedName !== '') {
                    $author = Author::model()->findByAttributes(['full_name' => $trimmedName]);
                    if (!$author) {
                        $author = new Author();
                        $author->full_name = $trimmedName;
                        $author->save();
                    }
                    if ($author->id) {
                        $this->linkAuthor($author->id);
                        $allAuthorIds[] = $author->id;
                    }
                }
            }
        }

        // Отправка уведомлений, если это новая книга
        if ($this->isNewRecord && !empty($allAuthorIds)) {
            $this->notifySubscribers(array_unique($allAuthorIds));
        }
    }

    /**
     * Привязка автора к книге
     */
    private function linkAuthor($authorId)
    {
        $sql = "INSERT IGNORE INTO book_author (book_id, author_id) VALUES (:bid, :aid)";
        Yii::app()->db->createCommand($sql)->execute([
            ':bid' => $this->id,
            ':aid' => $authorId
        ]);
    }

    /**
     * Наполнение очереди СМС
     */
    private function notifySubscribers($authorIds)
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('author_id', $authorIds);
        $subscribers = Subscription::model()->findAll($criteria);

        $sentPhones = [];
        $message = "Новая книга: " . $this->title . "!";

        foreach ($subscribers as $sub) {
            if (!isset($sentPhones[$sub->phone])) {
                Yii::app()->db->createCommand()->insert('sms_queue', [
                    'phone'      => $sub->phone,
                    'message'    => $message,
                    'status'     => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $sentPhones[$sub->phone] = true;
            }
        }
    }

    public function beforeSave()
    {
        if (parent::beforeSave()) {
            $now = date('Y-m-d H:i:s');
            if ($this->isNewRecord) {
                $this->created_at = $now;
            }
            $this->updated_at = $now;
            return true;
        }
        return false;
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}