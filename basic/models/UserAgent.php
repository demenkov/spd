<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_agent".
 *
 * @property integer $id
 * @property string $name
 * @property string $hash
 */
class UserAgent extends \yii\db\ActiveRecord
{

    const CACHE_TIME = 60; //default cache time for user agent
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_agent';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'hash'], 'required'],
            [['name'], 'string'],
            [['hash'], 'string', 'max' => 32],
            [['hash'], 'unique']
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'User agent id'),
            'name' => Yii::t('app', 'User agent name'),
            'hash' => Yii::t('app', 'Name hash'),
        ];
    }
    /**
    * @return \yii\db\ActiveQuery
    */
    public function getClicks()
    {
       return $this->hasMany(Click::className(), ['user_agent_id' => 'id']);
    }
    /**
     * Get user agent identifier.
     * @return int
     */
    public static function getUserAgentId() {
        $params = [
            'hash' => md5(Yii::$app->request->userAgent),
        ];
        //try to load from cache
        $userAgentId = Yii::$app->cache->get($params['hash']);
        if ($userAgentId === false) {
            //try to find in database
            if (empty($userAgent = UserAgent::findOne($params))) {
                //save new agent
                $params['name'] = Yii::$app->request->userAgent;
                $userAgent = new UserAgent($params);
                $userAgent->save();
            }
            $userAgentId = $userAgent->id;
            //put in cache
            $cacheTime = Yii::$app->params['userAgentCacheTime'] ?: static::CACHE_TIME;
            Yii::$app->cache->set($params['hash'], $userAgentId, $cacheTime);
        }
        return $userAgentId;
    }
}
