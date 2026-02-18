<?php

class Subscription extends CActiveRecord
{
    public function tableName()
    {
        return 'subscriptions';
    }

    public function rules()
    {
        return [
            ['author_id, phone', 'required'],
            ['phone', 'length', 'max' => 20],
            ['author_id', 'numerical', 'integerOnly' => true],
        ];
    }

    public function relations()
    {
        return [
            'author' => [self::BELONGS_TO, 'Author', 'author_id'],
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
