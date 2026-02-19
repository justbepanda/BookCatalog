<h1>Добавить книгу</h1>

<form method="post" enctype="multipart/form-data">
    <p>
        <label>Название</label><br>
        <input type="text" name="Book[title]" value="<?= CHtml::encode($book->title) ?>">
    </p>
    <p>
        <label>Год выпуска</label><br>
        <input type="number" name="Book[year]" value="<?= CHtml::encode($book->year) ?>">
    </p>
    <p>
        <label>ISBN</label><br>
        <input type="text" name="Book[isbn]" value="<?= CHtml::encode($book->isbn) ?>">
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
                <option value="<?= $author->id ?>"><?= CHtml::encode($author->full_name) ?></option>
            <?php endforeach; ?>
        </select>
    </p>

    <div id="new_author_wrapper" style="display: none; background: #f9f9f9; padding: 10px; border: 1px dashed #ccc;">
        <label>Имена новых авторов (через запятую)</label><br>
        <input type="text" name="new_author_name" style="width: 100%;" placeholder="Достоевский Федор, Чехов Антон">
        <small style="color: #666;">Введите одно или несколько имен, разделяя их запятыми.</small>
    </div>

    <p style="margin-top: 20px;">
        <button type="submit" style="padding: 10px 20px;">Сохранить книгу</button>
    </p>
</form>

<script>
    document.getElementById('author_select').addEventListener('change', function() {
        const wrapper = document.getElementById('new_author_wrapper');
        const values = Array.from(this.selectedOptions).map(opt => opt.value);
        wrapper.style.display = values.includes('new') ? 'block' : 'none';
    });
</script>