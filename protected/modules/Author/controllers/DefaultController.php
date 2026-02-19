<?php

class DefaultController extends Controller
{
    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index', 'subscribe'),
                'users' => array('*'),
            ),
            array('allow',
                'actions' => array('create', 'update', 'delete'),
                'users' => array('@'),
            ),
            array('deny',
                'users' => array('*'),
            ),
        );
    }

    /**
     * Список авторов
     */
    public function actionIndex()
    {
        $authors = Author::model()->with('books')->findAll();
        $this->render('index', ['authors' => $authors]);
    }

    /**
     * Подписка гостя на автора
     */
    public function actionSubscribe()
    {
        $request = Yii::app()->request;

        if (!$request->isPostRequest) {
            throw new CHttpException(400, 'Некорректный запрос');
        }

        $sub = new Subscription();
        $sub->author_id = $request->getPost('author_id');
        $sub->phone = $request->getPost('phone');

        if ($sub->save()) {
            Yii::app()->user->setFlash('success', 'Вы успешно подписались!');
        } else {
            $errors = $sub->getErrors();
            $firstError = reset($errors);
            Yii::app()->user->setFlash('error', reset($firstError));
        }

        $this->redirect($request->urlReferrer);
    }
}