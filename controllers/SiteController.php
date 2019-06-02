<?php

namespace app\controllers;

use app\models\Transaction;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\User;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'cabinet', 'send-funds'],
                'rules' => [
                    [
                        'actions' => ['logout', 'cabinet', 'send-funds'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
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
        $dataProvider = User::getDataProvider();
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
            if(!User::isUserExist($model->login)) {
                $model->createNewUser();
            }
            $model->login();
            return $this->redirect(Url::to(['cabinet']));
        }

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

    public function actionCabinet()
    {
        $dataProvider = Transaction::getUserTransactions(Yii::$app->user->identity->login);
        return $this->render('cabinet', ['dataProvider' => $dataProvider]);
    }

    public function actionSendFunds()
    {
        $transactionModel = new Transaction();
        $recipientUsers = User::getRecipients();
        if(Yii::$app->request->isPost) {
            if($transactionModel->load(Yii::$app->request->post())) {
                if($transactionModel->checkBalance()) {
                    $transactionModel->save();
                    $transactionModel->completeFundsMovement();
                } else {
                    Yii::$app->session->setFlash('error', 'Your balance cannot be less than -1000');
                }
                return $this->redirect(Url::to(['/site/cabinet']));
            }
        }

        return $this->render('send-funds', ['transactionModel' => $transactionModel, 'recipients' => $recipientUsers]);
    }
}
