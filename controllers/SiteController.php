<?php

namespace app\controllers;

use app\services\DashboardService;
use app\models\Blocks;
use app\models\Floors;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Users;

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
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        if ($user->role === Users::ROLE_TEACHER) {
            $data = DashboardService::getTeacherDashboard(Yii::$app->user->id);

            return $this->render('teacher-dashboard', [
                'data' => $data
            ]);
        } elseif ($user->role === Users::ROLE_CLERK) {
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
        } else {
            return $this->render('index');
        }
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
            return $this->goBack();
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
