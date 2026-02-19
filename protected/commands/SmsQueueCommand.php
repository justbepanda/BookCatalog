<?php
class SmsQueueCommand extends CConsoleCommand {

    public function actionProcess() {
        $rows = Yii::app()->db->createCommand()
            ->select('*')
            ->from('sms_queue')
            ->where('status = 0')
            ->limit(50)
            ->queryAll();

        if (empty($rows)) {
            echo "No messages to send.\n";
            return;
        }

        $messagesForApi = []; // То, что пойдет в API
        $ids = [];            // То, что нам нужно для обновления базы

        foreach ($rows as $row) {
            $ids[] = $row['id'];
            $messagesForApi[] = [
                'to'   => $row['phone'],
                'text' => $row['message']
            ];
        }
        echo "Sending data: " . json_encode($messagesForApi, JSON_UNESCAPED_UNICODE) . "\n";

        $result = SmsService::sendBatch($messagesForApi);

        // 4. Обрабатываем ответ
        if (isset($result['send'])) {
            Yii::app()->db->createCommand()->update('sms_queue', [
                'status' => 1,
                'updated_at' => new CDbExpression('NOW()'),
            ], ['in', 'id', $ids]);

            echo "Successfully sent " . count($ids) . " messages.\n";
        } elseif (isset($result['error'])) {
            Yii::app()->db->createCommand()->update('sms_queue', [
                'status' => 2, // Ошибка
                'updated_at' => new CDbExpression('NOW()'),
            ], ['in', 'id', $ids]);

            echo "Error: " . ($result['error']['description_ru'] ?? 'Unknown error') . "\n";
        }
    }
}