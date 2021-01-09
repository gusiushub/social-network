<?php

namespace app\controllers;

use app\models\Publication;
use app\models\Response;
use app\models\User;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\Json;
use yii\web\HttpException;

class PublicationController extends \yii\rest\Controller
{
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => HttpBearerAuth::class,
                'except' => ['login']
            ],
        ];
    }

    public function actionCreate()
    {

        $model = new Publication();
        $bodyParams = \Yii::$app->request->bodyParams;
        $user = User::findIdentityByBearerAccessToken(Yii::$app->request->headers->get('Authorization'));
        $model->setAttributes([
            'text' => $bodyParams['text'],
            'image_uuid' => $bodyParams['image_uuid'],
            'user_id' => $user['user_id'],
        ]);
        if (!$model->validate()) {
            throw new HttpException(Response::BAD_REQUEST, Json::encode($model->errors));
        }
        $model->save();
        return $model;
    }

    public function actionDelete()
    {

    }

    public function actionUserPublication()
    {
        $user = User::findIdentityByBearerAccessToken(Yii::$app->request->headers->get('Authorization'));
        $publication = Publication::find()->joinWith(['user'])->where(['user_id' => $user['user_id']])->all();
        return $publication;
    }

    public function actionGet()
    {

    }
}