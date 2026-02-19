<?php

class Subscription extends CActiveRecord
{
    public function tableName()
    {
        return 'subscriptions';
    }

    public function rules()
    {
        return array(
            array('author_id, phone', 'required'),
            array('author_id', 'numerical', 'integerOnly' => true),
            // Очистка телефона перед валидацией
            array('phone', 'filter', 'filter' => function ($value) {
                return preg_replace('/[^0-9]/', '', $value);
            }),
            array('phone', 'length', 'min' => 10, 'max' => 15),
            // Проверка на уникальность пары автор+телефон
            array('phone', 'uniqueCombination'),
        );
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

            $this->updated_at = $now;

            return true;
        }
        return false;
    }


    /**
     * Проверка уникальности пары
     */
    public function uniqueCombination($attribute, $params)
    {
        $existing = self::model()->findByAttributes([
            'author_id' => $this->author_id,
            'phone' => $this->phone
        ]);
        if ($existing) {
            $this->addError($attribute, 'Этот номер уже подписан на данного автора.');
        }
    }

}
