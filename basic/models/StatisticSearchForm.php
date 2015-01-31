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

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['partner_id', 'operator_id'], 'integer'],
			['country', 'in', 'range' => array_keys(Operator::countryList())],
		];
	}

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
	public function getOperators() {
		$operators = [];
		foreach (Operator::find()->all() as $operator) {
			$operators[$operator->id] = $operator->name;
		}
		return $operators;
	}
}