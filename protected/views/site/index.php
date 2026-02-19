<?php
/* @var $this SiteController */
$this->pageTitle = Yii::app()->name;
?>

<div class="welcome-container">
    <h1>Добро пожаловать в <i><?php echo CHtml::encode(Yii::app()->name); ?></i></h1>

    <p class="welcome-lead">
        Ваш персональный навигатор по миру литературы. Мы помогаем читателям и авторам находить друг друга.
    </p>

    <div class="main-actions">
        <ul class="action-list">
            <li>
                <a href="<?php echo Yii::app()->createUrl('Book/default/index'); ?>" class="btn-catalog">Каталог книг</a>
            </li>
            <li>
                <a href="<?php echo Yii::app()->createUrl('Author/default/index'); ?>" class="btn-outline">Наши авторы</a>
            </li>
        </ul>
    </div>
</div>

<div class="features-grid">
    <div class="feature-card">
        <h3>Актуальность</h3>
        <p>Информация о книгах обновляется в режиме реального времени. Вы всегда видите только актуальные издания.</p>
    </div>

    <div class="feature-card">
        <h3>Уведомления</h3>
        <p>Подпишитесь на любимого автора, и наша система отправит вам SMS, как только его новая книга появится в базе.</p>
    </div>

    <div class="feature-card">
        <h3>Удобство</h3>
        <p>Простой и понятный интерфейс для поиска информации. Без лишней рекламы и сложных переходов.</p>
    </div>
</div>

<div class="admin-notice">
    <?php if (Yii::app()->user->isGuest): ?>
        Для управления контентом, пожалуйста, перейдите на страницу <a href="<?php echo Yii::app()->createUrl('site/login'); ?>">авторизации</a>.
    <?php else: ?>
        Вы авторизованы как администратор. Вам доступны функции управления в верхнем меню.
    <?php endif; ?>
</div>