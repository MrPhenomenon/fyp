<?php

namespace app\controllers;

use app\models\QecCommittee;
use app\models\Users;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;

class QecCommitteeController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'view', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $identity = Yii::$app->user->identity;
                            if ($identity->isRoleAdmin()) return true;
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all QecCommittee models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => QecCommittee::find()
                ->with('user'),
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single QecCommittee model.
     *
     * @param int $id
     * @return string
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new QecCommittee model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new QecCommittee();
        $userModel = new Users();
        
        $availableUsers = QecCommittee::getAvailableUsers();

        if ($model->load(Yii::$app->request->post())) {
            $createNewUser = Yii::$app->request->post('create_new_user', '0');
            
            if ($createNewUser === '1') {
                $userModel->load(Yii::$app->request->post());
                $userModel->role = $model->is_teacher ? Users::ROLE_TEACHER : Users::ROLE_CLERK;
                $userModel->password = Yii::$app->security->generatePasswordHash($userModel->password);
                
                if ($userModel->save()) {
                    $model->user_id = $userModel->user_id;
                } else {
                    Yii::$app->session->setFlash('error', 'Failed to create user: ' . implode(', ', $userModel->getFirstErrors()));
                    return $this->render('create', [
                        'model' => $model,
                        'userModel' => $userModel,
                        'availableUsers' => $availableUsers,
                    ]);
                }
            } else {
                $selectedUser = Users::findOne($model->user_id);
                if ($selectedUser && $selectedUser->isRoleTeacher()) {
                    $model->is_teacher = 1;
                }
            }

            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'QEC Committee member added successfully.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'userModel' => $userModel,
            'availableUsers' => $availableUsers,
        ]);
    }

    /**
     * Updates an existing QecCommittee model.
     * If update is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $userModel = $model->user;
        
        // Get all users for selection
        $allUsers = QecCommittee::getAllUsersForSelection();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Also update the user's role if is_teacher changed
            $newRole = $model->is_teacher ? Users::ROLE_TEACHER : Users::ROLE_CLERK;
            if ($userModel->role !== $newRole) {
                $userModel->role = $newRole;
                $userModel->save(false);
            }
            
            Yii::$app->session->setFlash('success', 'QEC Committee member updated successfully.');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'userModel' => $userModel,
            'allUsers' => $allUsers,
        ]);
    }

    /**
     * Deletes an existing QecCommittee model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Optionally: Ask if user should also be deleted
        $deleteUser = Yii::$app->request->post('delete_user', false);
        
        if ($deleteUser) {
            $user = $model->user;
            $model->delete();
            if ($user) {
                $user->delete();
            }
            Yii::$app->session->setFlash('success', 'QEC member and user account deleted.');
        } else {
            $model->delete();
            Yii::$app->session->setFlash('success', 'QEC member removed from committee.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the QecCommittee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     * @return QecCommittee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = QecCommittee::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
    }
}