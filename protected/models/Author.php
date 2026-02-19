<?php

class Author extends CActiveRecord
{
    public function tableName()
    {
        return 'authors';
    }

    public function rules()
    {
        return [
            ['full_name', 'required'],
            ['full_name', 'length', 'max' => 255],
        ];
    }

    public function relations()
    {
        return [
            // связь с книгами через pivot
            'books' => [self::MANY_MANY, 'Book', 'book_author(author_id, book_id)'],

            // подписки гостей на этого автора
            'subscriptions' => [self::HAS_MANY, 'Subscription', 'author_id'],
        ];
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
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
}