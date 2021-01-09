<?php

namespace app\models;

use yii\db\ActiveRecord;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    public $rememberMe = true;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_GET = 'get';
    const SCENARIO_REFRESH_TOKEN = 'refresh_token';

    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['login', 'password', 'access_token', 'access_token_expired_at'],
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
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if ($this->password) {
            $this->password = \Yii::$app->security->generatePasswordHash($this->password);
        }
        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return self::findOne(['user_id' => $id]);
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

    public static function findIdentityByBearerAccessToken($token, $type = null)
    {
        return self::find()
            ->where('access_token=:access_token', [
                'access_token' => str_replace('Bearer ', '', $token)
            ])
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

    /**
     * @throws \yii\base\Exception
     */
    public function refreshToken()
    {
        $this->access_token = \Yii::$app->security->generateRandomString();
        // + Setting::getValue('token_lifetime', 604800));
        $this->access_token_expired_at = date('Y-m-d H:i:s', strtotime('now'));
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
     * @throws \yii\base\Exception
     */
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, \Yii::$app->security->generatePasswordHash($password));
    }
}
