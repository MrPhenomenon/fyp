<?php

namespace app\controllers;

use app\services\DashboardService;
use app\models\Blocks;
use app\models\Floors;
use app\models\QecCommittee;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Users;
use app\models\Departments;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'except' => ['login', 'error', 'captcha'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage - redirects to appropriate dashboard based on role
     *
     * @return Response
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        if ($user->role === Users::ROLE_ADMIN) {
            return $this->redirect(['site/admin-dashboard']);
        } elseif ($user->role === Users::ROLE_TEACHER) {
            return $this->redirect(['site/teacher-dashboard']);
        } elseif ($user->role === Users::ROLE_CLERK) {
            return $this->redirect(['site/clerk-dashboard']);
        }
        
        return $this->render('index');
    }

    public function actionAdminDashboard()
    {
        $user = Yii::$app->user->identity;
        if ($user->role !== Users::ROLE_ADMIN) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $department_id = Yii::$app->request->get('department_id');
        $faculty_id    = Yii::$app->request->get('faculty_id');
        $date_from     = Yii::$app->request->get('date_from', date('Y-m-01'));
        $date_to       = Yii::$app->request->get('date_to', date('Y-m-d'));

        $data        = DashboardService::getAdminDashboard($department_id, $faculty_id, $date_from, $date_to);
        $departments = Departments::find()->all();
        $faculties   = Users::find()->where(['role' => Users::ROLE_TEACHER])->orderBy('name')->all();

        return $this->render('admin-dashboard', [
            'data'                 => $data,
            'departments'          => $departments,
            'faculties'            => $faculties,
            'selectedDepartmentId' => $department_id,
            'selectedFacultyId'    => $faculty_id,
            'dateFrom'             => $date_from,
            'dateTo'               => $date_to,
        ]);
    }

    public function actionTeacherDashboard()
    {
        $user = Yii::$app->user->identity;
        $data = DashboardService::getTeacherDashboard(Yii::$app->user->id);

        return $this->render('teacher-dashboard', [
            'data' => $data
        ]);
    }

    public function actionClerkDashboard()
    {
        $user = Yii::$app->user->identity;
        $block_id = Yii::$app->session->get('clerk_block_id');
        $floor_id = Yii::$app->session->get('clerk_floor_id');
        
        $data = DashboardService::getClerkDashboard($block_id, $floor_id);
        $blocks = Blocks::find()->all();
        
        $floors = [];
        if ($block_id) {
            $floors = Floors::find()->where(['block_id' => $block_id])->all();
        }

        return $this->render('clerk-dashboard', [
            'data' => $data,
            'blocks' => $blocks,
            'floors' => $floors,
            'selectedBlockId' => $block_id,
            'selectedFloorId' => $floor_id
        ]);
    }

    /**
     * Set clerk filter (called via AJAX)
     */
    public function actionSetClerkFilter()
    {
        $block_id = Yii::$app->request->post('block_id');
        $floor_id = Yii::$app->request->post('floor_id');
        
        Yii::$app->session->set('clerk_block_id', $block_id);
        Yii::$app->session->set('clerk_floor_id', $floor_id);
        
        return $this->asJson(['success' => true]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $this->layout = 'login';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(Yii::$app->user->identity->DefaultRedirect);
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
