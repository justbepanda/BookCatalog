<?php

use PHPUnit\Framework\TestCase;

class SmsServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (Yii::app() === null) {
            $config = dirname(__FILE__) . '/../../config/test.php';
            Yii::createWebApplication($config);
        }
    }
    public function testJsonFormatting()
    {
        $messages = [
            ['to' => '79991112233', 'text' => 'Новая книжка']
        ];

        $result = SmsService::sendBatch($messages);

        $this->assertIsArray($result);

        if (isset($result['error'])) {
            $this->assertArrayHasKey('description_ru', $result['error']);
        }
    }
}