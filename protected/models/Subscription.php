<?php

class Subscription extends CActiveRecord
{
    /**
     * @return string
     */
    public function tableName(): string
    {
        return 'subscriptions';
    }

    public function rules(): array
    {
        return array(
            array('author_id, phone', 'required'),
            array('author_id', 'numerical', 'integerOnly' => true),
            array('phone', 'filter', 'filter' => function ($value) {
                return preg_replace('/[^0-9]/', '', $value);
            }),
            array('phone', 'length', 'min' => 10, 'max' => 15),
            array('phone', 'uniqueCombination'),
        );
    }

    /**
     * @return array[]
     */
    public function relations(): array
    {
        return [
            'author' => [self::BELONGS_TO, 'Author', 'author_id'],
        ];
    }

    /**
     * @param $className
     * @return mixed|Subscription
     */
    public static function model($className = __CLASS__)
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
