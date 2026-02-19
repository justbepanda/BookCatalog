<?php

class m260219_103159_sms_queue_table extends CDbMigration
{
    public function up()
    {
        $this->createTable('sms_queue', [
            'id' => 'pk',
            'phone' => 'string NOT NULL',
            'message' => 'text NOT NULL',
            'status' => "tinyint NOT NULL DEFAULT 0", // Статус: 0 - ожидание, 1 - отправлено, 2 - ошибка
            'created_at' => 'datetime NOT NULL',
            'updated_at' => 'datetime DEFAULT NULL',
        ]);

        $this->createIndex('idx_sms_queue_status', 'sms_queue', 'status');
    }

    public function down()
    {
        $this->dropIndex('idx_sms_queue_status', 'sms_queue');
        $this->dropTable('sms_queue');
    }
}