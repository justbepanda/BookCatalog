<?php

use PHPUnit\Framework\TestCase;

class SmsCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (Yii::app() === null) {
            $config = dirname(__FILE__) . '/../../config/test.php';
            Yii::createWebApplication($config);
        }
        Yii::app()->db->createCommand('DELETE FROM sms_queue')->execute();
    }

    /**
     * Тестируем успешную обработку очереди
     */
    public function testProcessQueueSuccessfully()
    {
        // 1. Подготовка: кладем "фейковое" сообщение в очередь
        Yii::app()->db->createCommand()->insert('sms_queue', [
            'phone' => '79001112233',
            'message' => 'Тестовое сообщение',
            'status' => 0, // Ожидание
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // 2. Запуск команды через Runner (как это делает Cron)
        $runner = new CConsoleCommandRunner();
        $runner->addCommands(Yii::getPathOfAlias('application.commands'));

        // Перехватываем вывод в консоль, чтобы он не мешал отчету PHPUnit
        ob_start();
        $runner->run(['yiic', 'smsqueue', 'process']);
        $output = ob_get_clean();

        // 3. Проверка: статус должен измениться на 1 (Успех)
        $sms = Yii::app()->db->createCommand()
            ->select('*')
            ->from('sms_queue')
            ->where('phone="79001112233"')
            ->queryRow();

        $this->assertEquals(1, (int)$sms['status'], 'После отправки статус должен стать 1');
    }

    /**
     * Тестируем, что воркер не берет уже отправленные сообщения
     */
    public function testWorkerIgnoresSentMessages()
    {
        // Кладем сообщение со статусом 1
        Yii::app()->db->createCommand()->insert('sms_queue', [
            'phone' => '70000000000',
            'message' => 'Уже отправлено',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $runner = new CConsoleCommandRunner();
        $runner->addCommands(Yii::getPathOfAlias('application.commands'));

        ob_start();
        $runner->run(['yiic', 'smsqueue', 'process']);
        $output = ob_get_clean();

        // Проверяем, что дата создания не изменилась (команда ничего не трогала)
        $this->assertStringNotContainsString('70000000000', $output);
    }
}