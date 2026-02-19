<?php
/* @var $this DefaultController */

$this->breadcrumbs=array(
	$this->module->id,
);
?>

<h1>Список авторов</h1>

<?php if(Yii::app()->user->hasFlash('success')): ?>
    <div class="flash-msg" style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb;">
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>
<?php endif; ?>

<?php if(Yii::app()->user->hasFlash('error')): ?>
    <div class="flash-msg" style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;">
        <?php echo Yii::app()->user->getFlash('error'); ?>
    </div>
<?php endif; ?>

<div class="authors-grid">
    <?php foreach($authors as $author): ?>
        <div class="author-card">
            <div>
                <div class="author-name"><?php echo CHtml::encode($author->full_name); ?></div>
                <div class="author-books">
                    <strong>Книги:</strong><br>
                    <?php if (!empty($author->books)): ?>
                        <?php echo implode(', ', array_map(fn($b) => CHtml::encode($b->title), $author->books)); ?>
                    <?php else: ?>
                        <span style="color: #ccc;">Книг пока нет</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="subscribe-section">
                <button class="btn-toggle-subscribe" onclick="toggleForm(this)">Подписаться на новинки</button>

                <form class="subscribe-form" method="post" action="<?php echo Yii::app()->createUrl('Author/default/subscribe'); ?>">
                    <input type="hidden" name="author_id" value="<?php echo $author->id; ?>">
                    <input type="tel" name="phone" placeholder="+7 (___) ___-__-__" required>
                    <button type="submit" class="btn-submit-sub">Отправить</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    function toggleForm(button) {
        const form = button.nextElementSibling;
        const isVisible = form.style.display === 'block';

        document.querySelectorAll('.subscribe-form').forEach(f => f.style.display = 'none');
        document.querySelectorAll('.btn-toggle-subscribe').forEach(b => b.style.display = 'block');

        if (!isVisible) {
            form.style.display = 'block';
            button.style.display = 'none';
            form.querySelector('input[name="phone"]').focus();
        }
    }
</script>