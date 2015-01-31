<?php

use yii\db\Schema;
use yii\db\Migration;

class m150131_191143_init extends Migration
{
	public function up()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
		}
		$this->createTable('{{%operator}}', [
			'id'				=> Schema::TYPE_PK,
			'name'				=> Schema::TYPE_STRING . '(255) NOT NULL',
			'country'			=> Schema::TYPE_STRING . '(2) NULL COMMENT "Country code ISO 3166"',
		], $tableOptions);
		$this->createIndex('name', '{{%operator}}', 'name');
		$this->createIndex('country', '{{%operator}}', 'country');

		$this->createTable('{{%user_agent}}', [
			'id'	=> Schema::TYPE_PK,
			'name'	=> Schema::TYPE_TEXT . ' NOT NULL',
			'hash'	=> Schema::TYPE_STRING . '(32) NOT NULL',
		], $tableOptions);
		$this->createIndex('hash', '{{%user_agent}}', 'hash', TRUE);

		$this->createTable('{{%click}}', [
			'id'			=> Schema::TYPE_BIGPK,
			'timestamp'		=> Schema::TYPE_INTEGER . '(11) NOT NULL',
			'ip'			=> Schema::TYPE_STRING . '(45)  NOT NULL COMMENT "IPv4/IPv6"',
			'user_agent_id'	=> Schema::TYPE_INTEGER . '(11) NOT NULL',
			'partner_id'	=> Schema::TYPE_INTEGER . '(11) NOT NULL',
			'operator_id'	=> Schema::TYPE_INTEGER . '(11) NOT NULL',
			'uniq'			=> Schema::TYPE_BOOLEAN . '(1) NOT NULL',
		], $tableOptions);
		$this->createIndex('timestamp', '{{%click}}', 'timestamp');
		$this->createIndex('ip', '{{%click}}', 'ip');
		$this->createIndex('user_agent_id', '{{%click}}', 'user_agent_id');
		$this->createIndex('partner_id', '{{%click}}', 'partner_id');
		$this->createIndex('operator_id', '{{%click}}', 'operator_id');
		$this->createIndex('uniq', '{{%click}}', 'uniq');

		$this->addForeignKey('FK_click_operator', '{{%click}}', 'operator_id', '{{%operator}}', 'id', 'RESTRICT', 'CASCADE');
		$this->addForeignKey('FK_click_user_agent', '{{%click}}', 'user_agent_id', '{{%user_agent}}', 'id', 'RESTRICT', 'CASCADE');
		
	}

	public function down()
	{
		$this->dropTable('{{%click}}');
		$this->dropTable('{{%user_agent}}');
		$this->dropTable('{{%operator}}');
	}
}
