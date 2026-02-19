<?php

class SmsService
{

    public static function sendBatch($messages) {

        $apiKey = Yii::app()->params['smsPilotApiKey'];

        $data = [
            'apikey' => $apiKey,
            'from'   => 'INFORM',
            'send'   => array_values($messages)
        ];

        $json = json_encode($data, JSON_UNESCAPED_UNICODE);

        $ch = curl_init('https://smspilot.ru/api2.php');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8', // Добавили кодировку в заголовок
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) return ['error' => ['description_ru' => $error]];

        return json_decode($response, true);
    }
}