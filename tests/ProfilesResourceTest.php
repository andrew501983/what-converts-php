<?php
namespace Tests;

use WhatConverts\WhatConverts;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ProfilesResourceTest extends TestCase
{
	
	/** @test */
	public function it_returns_all_profiles()
	{
		$mock = new MockHandler([
		    new Response(200, ['X-Foo' => 'Bar'], file_get_contents(__DIR__ . '/fixtures/profiles.json'))
		]);
		$handler = HandlerStack::create($mock);
		$client = new WhatConverts(
			'Your API Token', 
			'Your API Secret',
			$handler
		);
		$result = $client->getProfiles(66);
		$this->assertObjectHasAttribute('profiles', $result);
		$this->assertEquals(count($result->profiles), 3);
	}

	/** @test */
	public function it_returns_the_correct_profile()
	{
		$mock = new MockHandler([
		    new Response(200, ['X-Foo' => 'Bar'], file_get_contents(__DIR__ . '/fixtures/profile.json'))
		]);
		$handler = HandlerStack::create($mock);
		$client = new WhatConverts(
			'Your API Token', 
			'Your API Secret',
			$handler
		);
		$result = $client->getProfile(66, 47);
		$this->assertObjectHasAttribute('profile_id', $result);
		$this->assertEquals($result->profile_id, 47);
	}

	/** @test */
	public function it_returns_filtered_profiles()
	{
		$mock = new MockHandler([
		    new Response(200, ['X-Foo' => 'Bar'], file_get_contents(__DIR__ . '/fixtures/profiles_filtered.json'))
		]);
		$handler = HandlerStack::create($mock);
		$client = new WhatConverts(
			'Your API Token', 
			'Your API Secret',
			$handler
		);
		$result = $client->getProfiles(650, ['profiles_per_page' => 1]);
		$this->assertObjectHasAttribute('profiles', $result);
		$this->assertEquals(count($result->profiles), 1);
		$this->assertEquals($result->profiles_per_page, 1);
	}

	/** @test */
	public function it_creates_a_profile()
	{
		$mock = new MockHandler([
		    new Response(200, ['X-Foo' => 'Bar'], file_get_contents(__DIR__ . '/fixtures/create_profile.json'))
		]);
		$handler = HandlerStack::create($mock);
		$client = new WhatConverts(
			'Your API Token', 
			'Your API Secret',
			$handler
		);
		$result = $client->createProfile(650, 'Horse');
		$this->assertObjectHasAttribute('profile_id', $result);
		$this->assertEquals($result->profile_id, 57);
		$this->assertEquals($result->profile_name, 'Horse');
	}

	/** @test */
	public function it_updates_a_profile()
	{
		$mock = new MockHandler([
		    new Response(200, ['X-Foo' => 'Bar'], file_get_contents(__DIR__ . '/fixtures/update_profile.json'))
		]);
		$handler = HandlerStack::create($mock);
		$client = new WhatConverts(
			'Your API Token', 
			'Your API Secret',
			$handler
		);
		$result = $client->editProfile(650, 47, 'Moose 2');
		$this->assertObjectHasAttribute('profile_name', $result);
		$this->assertEquals($result->profile_name, 'Moose 2');
	}

	/** @test */
	public function it_deletes_a_profile()
	{
		$mock = new MockHandler([
		    new Response(200, ['X-Foo' => 'Bar'], file_get_contents(__DIR__ . '/fixtures/delete_profile.json'))
		]);
		$handler = HandlerStack::create($mock);
		$client = new WhatConverts(
			'Your API Token', 
			'Your API Secret',
			$handler
		);
		$result = $client->deleteProfile(650, 57);
		$this->assertObjectHasAttribute('profile_id', $result);
		$this->assertEquals($result->profile_id, 57);
	}

}