<h1>Добавить книгу</h1>

<?php $form = $book->getErrors(); ?>

<form method="post" enctype="multipart/form-data">

    <?php echo CHtml::errorSummary($book, null, null, [
            'style' => 'background: #fff3f3; border: 1px solid #ffcaca; border-radius: 4px; padding: 15px; margin-bottom: 20px; color: #cc0000;'
    ]); ?>

    <p>
        <label>Название</label><br>
        <input type="text" name="Book[title]"
               class="<?= $book->hasErrors('title') ? 'error' : '' ?>"
               value="<?= CHtml::encode($book->title) ?>" style="width: 100%;">
        <?= CHtml::error($book, 'title', ['style' => 'color: red; font-size: 0.85em;']); ?>
    </p>

    <p>
        <label>Год выпуска</label><br>
        <input type="number" name="Book[year]"
               class="<?= $book->hasErrors('year') ? 'error' : '' ?>"
               value="<?= CHtml::encode($book->year) ?>">
        <?= CHtml::error($book, 'year', ['style' => 'color: red; font-size: 0.85em;']); ?>
    </p>

    <p>
        <label>ISBN</label><br>
        <input type="text" name="Book[isbn]"
               class="<?= $book->hasErrors('isbn') ? 'error' : '' ?>"
               value="<?= CHtml::encode($book->isbn) ?>">
        <?= CHtml::error($book, 'isbn', ['style' => 'color: red; font-size: 0.85em;']); ?>
    </p>

    <p>
        <label>Описание</label><br>
        <textarea name="Book[description]" rows="5" style="width:100%"><?= CHtml::encode($book->description) ?></textarea>
    </p>

    <p>
        <label>Обложка книги</label><br>
        <input type="file" name="image_file">
    </p>

    <hr>

    <p>
        <label>Авторы (зажмите Ctrl для выбора нескольких)</label><br>
        <select name="author_ids[]" id="author_select" multiple style="height: 120px; width: 100%;">
            <option value="new">-- Добавить нового автора --</option>
            <?php foreach($authors as $author): ?>
                <option value="<?= $author->id ?>" <?= (is_array($book->author_ids) && in_array($author->id, $book->author_ids)) ? 'selected' : '' ?>>
                    <?= CHtml::encode($author->full_name) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>

    <p style="margin-top: 20px;">
        <button type="submit" style="padding: 10px 20px; cursor: pointer;">Сохранить книгу</button>
    </p>
</form>