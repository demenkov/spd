<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\data\ActiveDataProvider;

class StatisticSearchForm extends Model {
	public $partner_id;
	public $operator_id;
	public $country;
	public $timestamp;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['partner_id', 'operator_id', 'timestamp'], 'integer'],
			['country', 'in', 'range' => array_keys(Operator::countryList())],
		];
	}
	/**
	 * Get partner statistics by all time.
	 * @param array $params 
	 * @return \yii\data\ActiveDataProvider
	 */
	public function getPartnerData($params) {
		$q = (new Query())
		->select([
			'partner_id', 
			'clicks' => 'COUNT(c.id)',
			'transitions' => 'COUNT(IF(uniq = 1, 1, NULL))',
			'timestamp' => 'UNIX_TIMESTAMP(FROM_DAYS(TO_DAYS(FROM_UNIXTIME(timestamp))))+86400',
			'day' => 'TO_DAYS(FROM_UNIXTIME(timestamp))',
		])
		->from(Click::tableName() . ' c')
		->leftJoin(Operator::tableName() . ' o', 'o.id = c.operator_id')
		->groupBy(['day', 'partner_id'])
		->orderBy(['timestamp' => SORT_DESC]);
		$dataProvider = new ActiveDataProvider([
			'query' => $q,
		]);
		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}
		if (!empty($this->partner_id)) {
			$q->andFilterWhere(['partner_id' => $this->partner_id]);
		}
		if (!empty($this->operator_id)) {
			$q->andFilterWhere(['operator_id' => $this->operator_id]);
		}
		if (!empty($this->country)) {
			$q->andFilterWhere(['country' => $this->country]);
		}
		return $dataProvider;
	}
	/**
	 * Get partner daily statistics by hour.
	 * @param array $params 
	 * @return \yii\data\ActiveDataProvider
	 */
	public function getDailyData($params) {
		$q = (new Query())
		->select([
			'partner_id', 
			'clicks' => 'COUNT(c.id)',
			'transitions' => 'COUNT(IF(uniq = 1, 1, NULL))',
			'timestamp' => 'UNIX_TIMESTAMP(FROM_DAYS(TO_DAYS(FROM_UNIXTIME(timestamp))))+86400',
			'hour' => 'HOUR(FROM_UNIXTIME(c.timestamp))',
		])
		->from(Click::tableName() . ' c')
		->leftJoin(Operator::tableName() . ' o', 'o.id = c.operator_id')
		->groupBy(['hour', 'partner_id'])
		->orderBy(['hour' => SORT_DESC]);
		$dataProvider = new ActiveDataProvider([
			'query' => $q,
		]);
		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}
		if (!empty($this->partner_id)) {
			$q->andFilterWhere(['partner_id' => $this->partner_id]);
		}
		if (!empty($this->operator_id)) {
			$q->andFilterWhere(['operator_id' => $this->operator_id]);
		}
		if (!empty($this->country)) {
			$q->andFilterWhere(['country' => $this->country]);
		}
		if (!empty($this->timestamp)) {
			$q->andFilterWhere(['between', 'c.timestamp', $this->timestamp-86400, $this->timestamp]);
		}
		return $dataProvider;
	}
	/**
	 * Exists operator list from mysql database.
	 * @return array
	 */
	public function getOperators() {
		$operators = [];
		foreach (Operator::find()->all() as $operator) {
			$operators[$operator->id] = $operator->name;
		}
		return $operators;
	}
}