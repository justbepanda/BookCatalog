<?php

class DefaultController extends Controller
{
    public function filters(): array
    {
        return ['accessControl'];
    }

    public function accessRules(): array
    {
        return [
            ['allow', 'actions' => ['index'], 'users' => ['*']],
            ['allow', 'actions' => ['create', 'update', 'delete'], 'users' => ['@']],
            ['deny', 'users' => ['*']],
        ];
    }

    /**
     * Просмотр списка
     *
     * @return void
     */
    public function actionIndex(): void
    {
        $books = Book::model()->with('authors')->findAll();
        $this->render('index', ['books' => $books]);
    }

    /**
     * Создание записи
     *
     * @return void
     */
    public function actionCreate(): void
    {
        $book = new Book();

        if (isset($_POST['Book'])) {
            $book->attributes = $_POST['Book'];
            $book->author_ids = $_POST['author_ids'] ?? [];
            $book->new_author_names = $_POST['new_author_name'] ?? '';

            $uploadedFile = CUploadedFile::getInstanceByName('image_file');
            if ($uploadedFile) {
                $fileName = time() . '_' . $uploadedFile->name;
                $book->image_path = '/images/books/' . $fileName;
            }

            if ($book->save()) {
                if ($uploadedFile) {
                    $this->saveFile($uploadedFile, $fileName);
                }
                $this->redirect(['index']);
            }
        }

        $this->render('create', [
            'book' => $book,
            'authors' => Author::model()->findAll()
        ]);
    }

    /**
     * Обновление записи
     *
     * @param $id
     * @return void
     * @throws CHttpException
     */
    public function actionUpdate($id): void
    {
        $book = Book::model()->findByPk($id);
        if (!$book) throw new CHttpException(404);

        if (isset($_POST['Book'])) {
            $book->attributes = $_POST['Book'];
            $book->author_ids = $_POST['author_ids'] ?? [];
            $book->new_author_names = $_POST['new_author_name'] ?? '';

            $uploadedFile = CUploadedFile::getInstanceByName('image_file');
            if ($uploadedFile) {
                $fileName = time() . '_' . $uploadedFile->name;
                $book->image_path = '/images/books/' . $fileName;
            }

            if ($book->save()) {
                if ($uploadedFile) {
                    $this->saveFile($uploadedFile, $fileName);
                }
                $this->redirect(['index']);
            }
        }

        $this->render('update', [
            'book' => $book,
            'authors' => Author::model()->findAll(),
            'currentAuthorIds' => array_map(fn($a) => $a->id, $book->authors)
        ]);
    }

    /**
     * Удаление записи
     *
     * @param $id
     * @return void
     * @throws CDbException
     */
    public function actionDelete($id): void
    {
        $book = Book::model()->findByPk($id);
        if ($book) $book->delete();
        $this->redirect(['index']);
    }

    /**
     * Вспомогательный метод для сохранения файла
     */
    private function saveFile($uploadedFile, $fileName)
    {
        $dir = Yii::getPathOfAlias('webroot') . '/images/books';
        if (!is_dir($dir)) mkdir($dir, 0775, true);
        $uploadedFile->saveAs($dir . '/' . $fileName);
    }
}