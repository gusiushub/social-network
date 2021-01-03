<?php

namespace app\models;

use yii\db\ActiveRecord;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_GET = 'get';
    const SCENARIO_REFRESH_TOKEN = 'refresh_token';


    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => ['login', 'password'],
            self::SCENARIO_UPDATE => ['login', 'password'],
            self::SCENARIO_GET => [],
            self::SCENARIO_REFRESH_TOKEN => ['access_token', 'access_token_expired_at'],
        ];
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['login', 'password'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['login', 'password'], 'string', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return self::findOne(['user_id'=>$id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::find()
            ->where('access_token=:access_token', ['access_token' => $token])
            ->andWhere('access_token is not null')
            ->one();
    }

    /**
     * @return bool
     */
    public function accessTokenIsExpired()
    {
        return !$this->access_token || strtotime($this->access_token_expired_at) < time();
    }

    public function refreshToken()
    {
        $this->access_token = \Yii::$app->security->generateRandomString();
        $this->access_token_expired_at = date('Y-m-d H:i:s', strtotime('now'));// + Setting::getValue('token_lifetime', 604800));
        $this->save();
        $this->refresh();

    }

    /**
     * @param $username
     * @return array|ActiveRecord|null
     */
    public static function findByUsername($username)
    {
        return self::find()->where('login=:login', ['login' => $username])->one();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
    }
}
