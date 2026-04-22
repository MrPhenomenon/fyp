<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\Departments;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

class AdminController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'create', 'delete', 'view'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->role === Users::ROLE_ADMIN;
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Users (teachers, clerks, etc.)
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Users::find()->orderBy(['user_id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new User (teacher, clerk, etc.)
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Users();
        $departments = Departments::find()->all();
        $departmentList = ['' => 'Select Department'];
        foreach ($departments as $dept) {
            $departmentList[$dept->department_id] = $dept->department_name;
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->password = Yii::$app->security->generatePasswordHash($model->password);
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'User created successfully.');
                    return $this->redirect(['index']);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'departmentList' => $departmentList,
        ]);
    }

    /**
     * Deletes an existing User model.
     * @param int $id
     * @return Response
     */
    public function actionDelete($id)
    {
        $model = Users::findOne($id);
        if ($model) {
            // Prevent admin from deleting themselves
            if ($model->user_id === Yii::$app->user->identity->user_id) {
                Yii::$app->session->setFlash('error', 'You cannot delete your own account.');
            } else {
                $model->delete();
                Yii::$app->session->setFlash('success', 'User deleted successfully.');
            }
        }
        return $this->redirect(['index']);
    }

    /**
     * View user details
     * @param int $id
     * @return string
     */
    public function actionView($id)
    {
        $model = Users::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('User not found.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }
}