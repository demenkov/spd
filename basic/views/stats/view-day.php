<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\models\Operator;
use yii\grid\GridView;

/* @var $this \yii\web\View */
/* @var $searchForm \app\models\StatisticSearchForm */
/* @var $dataProvider \yii\data\ActiveDataProvider */

?>
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($searchForm, 'country')->dropDownList(['' => ''] + Operator::countryList()) ?>
<?= $form->field($searchForm, 'operator_id')->dropDownList(['' => ''] + $searchForm->operators)->label(Yii::t('app', 'Operator')) ?>
<?= $form->field($searchForm, 'partner_id')->hiddenInput()->label('') ?>
<?= $form->field($searchForm, 'timestamp')->hiddenInput()->label('') ?>
<?= Html::submitButton(Yii::t('app', 'Filter'), ['class' => 'btn btn-success',]) ?>
<?php $form->end(); ?>
<?= Gridview::widget([
	'dataProvider' => $dataProvider,
	'columns' => [
		[
			'label' => Yii::t('app', 'Date'),
			'value' => function ($model) {return $model['timestamp'] + $model['hour']*3600;},
			'format' => 'dateTime',
		],
		'clicks',
		'transitions',
	]
]);
