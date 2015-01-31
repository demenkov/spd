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
	const LIFETIME = 3600;
	public function actionIndex($partner_id)
	{
		$params = [
			'partner_id'	=> $partner_id,
			'ip'			=> Yii::$app->request->userIP,
			'uniq'			=> (int)empty($_COOKIE['partner_id']),
		];
		$userAgent = UserAgent::get();
		if (!empty($userAgent->id)) {
			$params['user_agent_id'] = $userAgent->id;
		}
		$operator = Operator::getOperatorByIp(Yii::$app->request->userIP);
		if (!empty($operator)) {
			$params['operator_id'] = $operator->id;
		}
		(new Click($params))->save();
		setcookie('partner_id', $partner_id, time() + static::LIFETIME, '/');
		header("HTTP/1.0 204 No Content");
		return;
	}
	public function actionView($partner_id)
	{
		$post = Yii::$app->request->post();
		$searchForm = new StatisticSearchForm();
		$defaultParams[$searchForm->formName()] = [
            'partner_id' => $partner_id,
        ];
        //
        $params = $post ?: $defaultParams;
		$dataProvider = $searchForm->getPartnerData($params);
		return $this->render('view', compact('searchForm', 'dataProvider'));
	}
}
