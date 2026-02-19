<?php
/* @var $this DefaultController */
/* @var $books Book[] */

$this->breadcrumbs = array(
        $this->module->id,
);
?>

<h1>Список книг</h1>

<?php if(!Yii::app()->user->isGuest): ?>
    <?= CHtml::link('Добавить книгу', ['create'], ['class' => 'btn-add']) ?>
<?php endif; ?>

<div class="books-container">
    <?php foreach ($books as $book): ?>
        <div class="book-card">
            <div class="book-image">
                <?php if ($book->image_path): ?>
                    <img src="<?= CHtml::encode($book->image_path) ?>" alt="<?= CHtml::encode($book->title) ?>">
                <?php else: ?>
                    <span style="color: #999;">Нет обложки</span>
                <?php endif; ?>
            </div>

            <div class="book-content">
                <div class="book-title"><?= CHtml::encode($book->title) ?></div>

                <div class="book-authors">
                    <?= implode(', ', array_map(fn($a) => CHtml::encode($a->full_name), $book->authors)) ?>
                </div>

                <div class="book-info">
                    <strong>Год:</strong> <?= CHtml::encode($book->year) ?><br>
                    <strong>ISBN:</strong> <?= CHtml::encode($book->isbn) ?>
                </div>

                <div class="book-description">
                    <?= CHtml::encode($book->description) ?>
                </div>

                <div class="book-actions">
                    <?php if(!Yii::app()->user->isGuest): ?>
                        <?= CHtml::link('Редактировать', ['update', 'id' => $book->id], ['class' => 'action-link']) ?>
                        <span style="color: #ccc;">|</span>
                        <?= CHtml::link('Удалить', ['delete', 'id' => $book->id], [
                                'class' => 'action-link delete-link',
                                'confirm' => 'Вы уверены, что хотите удалить эту книгу?'
                        ]) ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    <?php endforeach; ?>
</div>