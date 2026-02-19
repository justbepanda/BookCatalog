<?php

class DefaultController extends CController
{
    /**
     * ТОП 10 авторов по количеству книг за год
     */
    public function actionTopAuthors($year)
    {
        $sql = "
            SELECT a.full_name, COUNT(b.id) as books_count
            FROM authors a
            JOIN book_author ba ON ba.author_id = a.id
            JOIN books b ON b.id = ba.book_id
            WHERE b.year = :year
            GROUP BY a.id
            ORDER BY books_count DESC
            LIMIT 10
        ";
        $authors = Yii::app()->db->createCommand($sql)->queryAll(true, [':year' => $year]);

        header('Content-Type: application/json');
        echo CJSON::encode($authors);
        Yii::app()->end();
    }
}
