<?php

class Author extends CActiveRecord
{
    /**
     * @return string
     */
    public function tableName(): string
    {
        return 'authors';
    }

    public function rules(): array
    {
        return [
            ['full_name', 'required'],
            ['full_name', 'length', 'max' => 255],
        ];
    }

    /**
     * @return array[]
     */
    public function relations(): array
    {
        return [
            // связь с книгами через pivot
            'books' => [self::MANY_MANY, 'Book', 'book_author(author_id, book_id)'],

            // подписки гостей на этого автора
            'subscriptions' => [self::HAS_MANY, 'Subscription', 'author_id'],
        ];
    }

    /**
     * @param $className
     * @return Author|mixed
     */
    public static function model($className = __CLASS__): mixed
    {
        return parent::model($className);
    }

    /**
     * @return bool
     */
    public function beforeSave(): bool
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