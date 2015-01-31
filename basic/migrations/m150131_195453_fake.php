<?php

use yii\db\Schema;
use yii\db\Migration;
use app\models\Click;

class m150131_195453_fake extends Migration
{
    public function up()
    {
    	for ($i = 0; $i <= 500000; $i++) {
    		(new Click([
    			'partner_id'	=> rand(1,10),
				'ip'			=> long2ip(rand(0, 1000)),
				'uniq'			=> rand(0,1),
				'user_agent_id' => rand(1,2),
				'operator_id' 	=> rand(1,2),
    		]))->save();
    		echo $i, PHP_EOL;
    	}
    }

    public function down()
    {
        echo "m150131_195453_fake cannot be reverted.\n";

        return false;
    }
}
