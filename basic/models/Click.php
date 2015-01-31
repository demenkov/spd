<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "click".
 *
 * @property string $id
 * @property string $ip
 * @property integer $user_agent_id
 * @property integer $timestamp
 * @property integer $partner_id
 * @property integer $operator_id
 * @property integer $uniq 
 *
 * @property Operator $operator
 * @property UserAgent $userAgent
 */
class Click extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'click';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ip', 'user_agent_id', 'operator_id', 'partner_id', 'uniq'], 'required'],
            [['user_agent_id', 'timestamp', 'partner_id', 'operator_id', 'uniq'], 'integer'],
            [['ip'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Click id'),
            'ip' => Yii::t('app', 'ipv4/ipv6'),
            'user_agent_id' => Yii::t('app', 'User agent id'),
            'timestamp' => Yii::t('app', 'Click timestamp'),
            'partner_id' => Yii::t('app', 'Partner id'),
            'operator_id' => Yii::t('app', 'Operator id'),
            'uniq' => Yii::t('app', 'Uniq'), 
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperator()
    {
        return $this->hasOne(Operator::className(), ['id' => 'operator_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAgent()
    {
        return $this->hasOne(UserAgent::className(), ['id' => 'user_agent_id']);
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
            ],
        ];
    }
}
