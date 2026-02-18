<?php

class Book extends CActiveRecord
{
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
        ];
    }

    public function relations()
    {
        return [
            'authors' => [self::MANY_MANY, 'Author', 'book_author(book_id, author_id)'],
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

            $this->updated_at = $now; // <- обязательно для всех записей

            return true;
        }
        return false;
    }
}
