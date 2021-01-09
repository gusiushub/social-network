<?php

namespace app\models;

class Publication extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_DELETE = 'delete';
    const SCENARIO_GET = 'get';

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'publication';
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['text', 'user_id', 'image_uuid', 'like_count'],
            self::SCENARIO_CREATE => ['text', 'user_id', 'image_uuid',],
            self::SCENARIO_DELETE => [],
            self::SCENARIO_GET => [],
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['user_id', 'required'],
            [['text', 'image_uuid'], 'string'],
            [['user_id', 'like_count'], 'integer'],
        ];
    }

    public function getUser()
    {
        $this->hasOne(\app\models\User::className(), ['user_id' => 'user_id']);
    }
}