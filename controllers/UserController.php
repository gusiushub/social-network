<?php

namespace app\controllers;

use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class UserController extends Controller
{
    public function actionCreate()
    {
        $user = new User();
        $bodyParams = \Yii::$app->request->bodyParams;
        $user->setAttributes([
            'login' => ArrayHelper::getValue($bodyParams, 'login'),
            'password' => \Yii::$app->security
                ->generatePasswordHash(ArrayHelper::getValue($bodyParams, 'password')),
        ]);
        if (!$user->validate()) {
            throw new HttpException(400, Json::encode($user->errors));
        }
        $user->save();
        $user->refresh();
        $user->refreshToken();
        return $user;
    }

    public function actionGet(int $id)
    {
        return User::findIdentity($id);
    }

    public function actionUpdate(int $id)
    {
        $user = User::findIdentity($id);

        if (!$user) {
            throw new NotFoundHttpException('Этот пидарас еще не родился!!!');
        }

        $user->setScenario(User::SCENARIO_UPDATE);

        $bodyParams = \Yii::$app->request->bodyParams;

        $user->setAttributes([
            'login' => $bodyParams['login'],
            'password' => $bodyParams['password']
        ]);

        if (!$user->validate()) {
            throw new HttpException(400, Json::encode($user->errors));
        }

        $user->save();
        $user->refresh();
        $user->refreshToken();

        return $user;
    }
}