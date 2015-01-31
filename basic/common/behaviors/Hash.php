<?php

namespace app\common\behaviors;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class Hash extends Behavior
{
	public $inAttribute = 'name';
	public $outAttribute = 'hash';

	public function events()
	{
		return [
			ActiveRecord::EVENT_BEFORE_VALIDATE => 'calculateHash'
		];
	}
	public function calculateHash($event)
	{
		if (empty($this->owner->{$this->outAttribute})) {
			$this->owner->{$this->outAttribute} = md5($this->owner->{$this->inAttribute});
		}
	}
}