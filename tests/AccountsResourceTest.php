<?php
namespace Tests;

use WhatConverts\WhatConverts;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class AccountsResourceTest extends TestCase
{
	
	/** @test */
	public function it_returns_all_accounts()
	{
		$mock = new MockHandler([
		    new Response(200, ['X-Foo' => 'Bar'], file_get_contents(__DIR__ . '/fixtures/accounts.json'))
		]);
		$handler = HandlerStack::create($mock);
		$client = new WhatConverts(
			'Your API Token', 
			'Your API Secret', 
			$handler
		);
		$result = $client->getAccounts();
		$this->assertObjectHasAttribute('accounts', $result);
		$this->assertEquals(count($result->accounts), 3);
	}

	/** @test */
	public function it_returns_the_correct_account()
	{
		$mock = new MockHandler([
		    new Response(200, ['X-Foo' => 'Bar'], file_get_contents(__DIR__ . '/fixtures/account.json'))
		]);
		$handler = HandlerStack::create($mock);
		$client = new WhatConverts(
			'Your API Token', 
			'Your API Secret', 
			$handler
		);
		$result = $client->getAccount(33333);
		$this->assertObjectHasAttribute('account_id', $result);
		$this->assertEquals($result->account_id, 33333);
	}
	
	/** @test */
	public function it_returns_filtered_accounts()
	{
		$mock = new MockHandler([
		    new Response(200, ['X-Foo' => 'Bar'], file_get_contents(__DIR__ . '/fixtures/accounts_filtered.json'))
		]);
		$handler = HandlerStack::create($mock);
		$client = new WhatConverts(
			'Your API Token', 
			'Your API Secret',
			$handler
		);
		$result = $client->getAccounts(['accounts_per_page' => 1]);
		$this->assertObjectHasAttribute('accounts', $result);
		$this->assertEquals(count($result->accounts), 1);
		$this->assertEquals($result->accounts[0]->account_name, "Aardvark");
	}

	/** @test */
	public function it_creates_an_account()
	{
		$mock = new MockHandler([
		    new Response(200, ['X-Foo' => 'Bar'], file_get_contents(__DIR__ . '/fixtures/create_account.json'))
		]);
		$handler = HandlerStack::create($mock);
		$client = new WhatConverts(
			'Your API Token', 
			'Your API Secret',
			$handler
		);
		$result = $client->createAccount("Barneys Motor Corporation");
		$this->assertObjectHasAttribute('account_id', $result);
		$this->assertEquals($result->account_id, 12345678);
		$this->assertEquals($result->account_name, "Barneys Motor Corporation");
	}

	/** @test */
	public function it_updates_an_account()
	{
		$mock = new MockHandler([
		    new Response(200, ['X-Foo' => 'Bar'], file_get_contents(__DIR__ . '/fixtures/update_account.json'))
		]);
		$handler = HandlerStack::create($mock);
		$client = new WhatConverts(
			'Your API Token', 
			'Your API Secret',
			$handler
		);
		$result = $client->editAccount(12345678, "Barneys Motor Corporation New Location");
		$this->assertObjectHasAttribute('account_id', $result);
		$this->assertEquals($result->account_name, "Barneys Motor Corporation New Location");
	}

	/** @test */
	public function it_deletes_an_account()
	{
		$mock = new MockHandler([
		    new Response(200, ['X-Foo' => 'Bar'], file_get_contents(__DIR__ . '/fixtures/delete_account.json'))
		]);
		$handler = HandlerStack::create($mock);
		$client = new WhatConverts(
			'Your API Token', 
			'Your API Secret',
			$handler
		);
		$result = $client->deleteAccount(12345678);
		$this->assertObjectHasAttribute('account_id', $result);
		$this->assertEquals($result->account_id, 12345678);
	}

}