<?php
namespace Tests;

use WhatConverts\WhatConverts;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class LeadsResourceTest extends TestCase
{

	/** @test */
	public function it_returns_all_leads()
	{
		$mock = new MockHandler([
		    new Response(200, ['X-Foo' => 'Bar'], file_get_contents(__DIR__ . '/fixtures/leads.json'))
		]);
		$handler = HandlerStack::create($mock);
		$client = new WhatConverts(
			'Your API Token', 
			'Your API Secret',
			$handler
		);
		$result = $client->getLeads();
		$this->assertObjectHasAttribute('leads', $result);
		$this->assertEquals(count($result->leads), 4);
	}

	/** @test */
	public function it_returns_the_correct_lead()
	{
		$mock = new MockHandler([
		    new Response(200, ['X-Foo' => 'Bar'], file_get_contents(__DIR__ . '/fixtures/lead.json'))
		]);
		$handler = HandlerStack::create($mock);
		$client = new WhatConverts(
			'Your API Token', 
			'Your API Secret',
			$handler
		);
		$result = $client->getLead(1000);
		$this->assertObjectHasAttribute('lead_id', $result);
		$this->assertEquals($result->lead_id, 1000);
	}

	/** @test */
	public function it_returns_filtered_leads()
	{
		$mock = new MockHandler([
		    new Response(200, ['X-Foo' => 'Bar'], file_get_contents(__DIR__ . '/fixtures/leads_filtered.json'))
		]);
		$handler = HandlerStack::create($mock);
		$client = new WhatConverts(
			'Your API Token', 
			'Your API Secret',
			$handler
		);
		$result = $client->getLeads(['lead_type' => 'phone_call', 'account_id' => 5555]);
		$this->assertEquals($result->leads[0]->account_id, 5555);
		$this->assertEquals($result->leads[0]->lead_type, "Phone Call");
	}

	/** @test */
	public function it_creates_a_lead()
	{
		$mock = new MockHandler([
		    new Response(200, ['X-Foo' => 'Bar'], file_get_contents(__DIR__ . '/fixtures/create_lead.json'))
		]);
		$handler = HandlerStack::create($mock);
		$client = new WhatConverts(
			'Your API Token', 
			'Your API Secret',
			$handler
		);
		$result = $client->createLead(55555, 'web_form', ['form_name' => "Quote Form"]);
		$this->assertObjectHasAttribute('lead_id', $result);
		$this->assertEquals($result->lead_id, 66666);
	}

	/** @test */
	public function it_updates_a_lead()
	{
		$mock = new MockHandler([
		    new Response(200, ['X-Foo' => 'Bar'], file_get_contents(__DIR__ . '/fixtures/update_lead.json'))
		]);
		$handler = HandlerStack::create($mock);
		$client = new WhatConverts(
			'Your API Token', 
			'Your API Secret',
			$handler
		);
		$result = $client->editLead(66666, ['quotable' => 'yes']);
		$this->assertObjectHasAttribute('lead_id', $result);
		$this->assertEquals($result->lead_id, 66666);
	}

}