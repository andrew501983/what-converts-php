<?php
namespace Tests;

use WhatConverts\WhatConverts;
use Dotenv\Dotenv;

class AccountResourceTest extends \PHPUnit_Framework_TestCase
{

	protected $WhatConverts;

	protected function setUp()
	{
		$dotenv = new Dotenv(dirname(__DIR__));
		$dotenv->load();
		$this->WhatConverts = new WhatConverts(
			getenv('WC_API_TOKEN'),
			getenv('WC_API_SECRET')
		);
	}
	
	/** @test */
	public function it_returns_a_list_of_accounts()
	{
		$accounts = $this->WhatConverts->getAccounts();
		$this->assertNotEmpty($accounts);
	}

	/** @test */
	public function it_returns_all_accounts()
	{
		$accounts = $this->WhatConverts->getAllAccounts();
		$this->assertCount(14, $accounts);
	}

	/** @test */
	public function it_returns_the_correct_account()
	{
		$correctAccountId = 24502;
		$account = $this->WhatConverts->getAccount($correctAccountId);
		$this->assertEquals($correctAccountId, $account->account_id);
	}

}