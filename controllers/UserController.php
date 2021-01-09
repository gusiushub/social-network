<?php

namespace app\controllers;

use app\models\Response;
use app\models\User;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\IdentityInterface;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class UserController extends Controller
{
    /**
     * @return User
     * @throws HttpException
     * @throws Exception
     * @throws \Exception
     */
    public function actionCreate()
    {
        $user = new User();
        $bodyParams = Yii::$app->request->bodyParams;
        $user->setAttributes([
            'login' => ArrayHelper::getValue($bodyParams, 'login'),
            'password' => ArrayHelper::getValue($bodyParams, 'password')
        ]);
        if (!$user->validate()) {
            throw new HttpException(Response::BAD_REQUEST, Json::encode($user->errors));
        }
        $user->save();
        $user->refresh();
        $user->refreshToken();
        return $user;
    }

    /**
     * @param int $id
     * @return User|IdentityInterface|null
     */
    public function actionGet(int $id)
    {
        return User::findIdentity($id);
    }

    /**
     * @param int $id
     * @return User|IdentityInterface|null
     * @throws Exception
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
    {
        $user = User::findIdentity($id);

        if (!$user) {
            throw new NotFoundHttpException('Этот пидарас еще не родился!!!');
        }

        $user->setScenario(User::SCENARIO_UPDATE);

        $bodyParams = Yii::$app->request->bodyParams;

        $user->setAttributes([
            'login' => $bodyParams['login'],
            'password' => $bodyParams['password']
        ]);
        if (!$user->validate()) {
            throw new HttpException(Response::BAD_REQUEST, Json::encode($user->errors));
        }
        $user->save();
        $user->refresh();
        $user->refreshToken();

        return $user;
    }

    public function actionHome()
    {
        $token = Yii::$app->request->headers->get('Authorization');
        if (!$user = User::findIdentityByAccessToken(str_replace('Bearer ', '', $token))) {
            throw new UnauthorizedHttpException('Иди от сюда!!! Тебя не звали!!!');
        }

        return $user;
    }
}