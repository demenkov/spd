<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Click;
use app\models\Operator;
use app\models\UserAgent;
use app\models\StatisticSearchForm;

class StatsController extends Controller
{
	/**
	 * Save statistics about transition.
	 * Set tracking cookie.
	 * @param int $partner_id 
	 * @return null
	 */
	public function actionIndex($partner_id)
	{
		//fill click params
		$params = [
			'partner_id'	=> $partner_id,
			'ip'			=> Yii::$app->request->userIP,
			'uniq'			=> (int)empty($_COOKIE['partner_id']),
		];
		$userAgentId = UserAgent::getUserAgentId();
		if (!empty($userAgentId)) {
			$params['user_agent_id'] = $userAgentId;
		}
		$operatorId = Operator::getOperatorId(Yii::$app->request->userIP);
		if (!empty($operatorId)) {
			$params['operator_id'] = $operatorId;
		}
		(new Click($params))->save();
		//set cookie until a new hour started
		setcookie('partner_id', $partner_id, mktime(date('H'), 59, 59) + 1, '/');
		header("HTTP/1.0 204 No Content");
		return;
	}
	/**
	 * View all partner statistics.
	 * @param int $partner_id 
	 * @return mixed
	 */
	public function actionView($partner_id)
	{
		$post = Yii::$app->request->post();
		$searchForm = new StatisticSearchForm();
		$defaultParams[$searchForm->formName()] = [
			'partner_id' => $partner_id,
		];
		$params = $post ?: $defaultParams;
		$dataProvider = $searchForm->getPartnerData($params);
		return $this->render('view', compact('searchForm', 'dataProvider'));
	}
	/**
	 * View daily statistics.
	 * @param int $partner_id 
	 * @param int $timestamp 
	 * @return mixed
	 */
	public function actionViewDay($partner_id, $timestamp)
	{
		$post = Yii::$app->request->post();
		$searchForm = new StatisticSearchForm();
		$defaultParams[$searchForm->formName()] = [
			'partner_id' => $partner_id,
			'timestamp' => $timestamp,
		];
		$params = $post ?: $defaultParams;
		$dataProvider = $searchForm->getDailyData($params);
		return $this->render('view-day', compact('searchForm', 'dataProvider'));
	}
}
