<?php

namespace app\commands;

use yii\console\Controller;
use yii\helpers\Console;
use app\models\Operator;

/**
 * Stats helpers.
 */
class StatsController extends Controller
{
    /**
     * Refresh operators map.
     * Run when operators list needs update.
     */
    public function actionIndex()
    {
        if (!empty(Operator::getMap(TRUE))) {
        	echo $this->ansiFormat('Successfully updated.', Console::BG_GREEN), PHP_EOL;
        }
        else {
        	echo $this->ansiFormat('Whoops. Something went wrong!', Console::BG_RED), PHP_EOL;
        }
    }
}
