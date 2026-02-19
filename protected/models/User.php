<?php

class User extends CActiveRecord
{
    public function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return [
            ['username, password_hash, role', 'required'],
            ['username, role', 'length', 'max'=>255],
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
