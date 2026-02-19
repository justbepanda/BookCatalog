<?php

class User extends CActiveRecord
{
    /**
     * @return string
     */
    public function tableName(): string
    {
        return 'users';
    }

    public function rules(): array
    {
        return [
            ['username, password_hash, role', 'required'],
            ['username, role', 'length', 'max'=>255],
        ];
    }

    /**
     * @param $className
     * @return mixed|User
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
