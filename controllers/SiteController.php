<?php

namespace app\controllers;

use app\models\Response;
use app\models\User;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\filters\VerbFilter;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => HttpBearerAuth::class,
                'except' => ['login']
            ],
//            'access' => [
//                'class' => AccessControl::className(),
//                'rules' => [
//                    [
//                        'actions' => [''],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'login' => ['post'],
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
     * @return array
     * @throws HttpException
     */
    public function actionLogin()
    {
        $bodyParams = Yii::$app->request->bodyParams;

        $user = User::findByUsername(ArrayHelper::getValue($bodyParams, 'login'));

        if (!$user or !$user->validatePassword(ArrayHelper::getValue($bodyParams, 'password'))) {
            throw new HttpException(Response::BAD_REQUEST, 'Пошел в пизду уебан!!!');
        }

        Yii::$app->user->login($user, $user->rememberMe ? 3600*24*30 : 0);

        if ($user->accessTokenIsExpired()){
            $user->refreshToken();
        }
        return [
            'login' => $user->login,
            'access_token' => $user->access_token,
        ];
    }
}
