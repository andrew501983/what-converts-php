<?php
namespace WhatConverts;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Response;
use WhatConverts\Exception\WhatConvertsApiException;
use WhatConverts\Exception\WhatConvertsClientException;

class WhatConverts
{

	const WC_BASE_ENDPOINT = 'https://app.whatconverts.com/api/v1/';
	protected $wc_api_token;
	protected $wc_api_secret;
	protected $wc_client;

	/**
		* WhatConverts constructor.
		* Create a Guzzle Client with default request settings
		* for base URI and HTTP Basic Auth.
		* @param string $wc_api_token
		* @param string $wc_api_secret
	*/
	public function __construct($wc_api_token, $wc_api_secret)
	{

		$this->wc_api_token = $wc_api_token;
		$this->wc_api_secret = $wc_api_secret;
		$this->wc_client = new GuzzleHttpClient([
			// Base URI is used with relative requests
			'base_uri' => static::WC_BASE_ENDPOINT,
			'auth' => [
				$this->wc_api_token,
				$this->wc_api_secret
			]
		]);
	}

	/**
		* Parse a Psr7 Response into JSON 
		* @param GuzzleHttp\Psr7\Response
		* @return object
	*/
	protected function parseResponse(Response $response)
	{
		return json_decode(
			$response->getBody()->getContents()
		);
	}

	/**
		* Check JSON Response Body for WhatConverts error_message
		* @param object
		* @throws WhatConverts\Exception\WhatConvertsApiException
	*/
	protected function checkForApiErrors($jsonResponseBody)
	{
		if(isset($jsonResponseBody->error_message))
		{
			throw new WhatConvertsApiException($jsonResponseBody->error_message);
		}
	}

	/**
		* Get account list from WhatConverts
		* @throws WhatConverts\Exception\WhatConvertsClientException
		* @throws WhatConverts\Exception\WhatConvertsApiException
		* @return array
	*/
	public function getAccounts()
	{
		try
		{
			$accounts = [];
			$pageNumber = 1; //first page is considered 1, not 0 indexed
			$accountsPerPageDesired = 250; //250 is the max allowed
			$params = [
				'page_number' => $pageNumber, 
				'accounts_per_page' => $accountsPerPageDesired
			];
			$response = $this->wc_client->get("accounts", [
				'query' => http_build_query($params)
			]);
			$result = $this->parseResponse($response);
			$this->checkForApiErrors($result);
			$totalPages = $result->total_pages;
			$accounts = $result->accounts;
			$pageNumber++; //reflect that we pulled the first page already
			while($pageNumber <= $totalPages) 
			{
				$params = [
					'accounts_per_page' => $accountsPerPageDesired,
					'page_number' => $pageNumber
				];
				$response = $this->wc_client->get("accounts", [
					'query' => http_build_query($params)
				]);
				$result = $this->parseResponse($response);
				$this->checkForApiErrors($result);
				//operator overloading (merge arrays, preserve keys)
				$accounts = array_merge($accounts, $result->accounts);
				$pageNumber++;
			}
			return $accounts;
		}
		catch (TransferException $e) 
		{
			throw new WhatConvertsClientException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
		* Get a single account from WhatConverts
		* @param string $account_id
		* @throws WhatConverts\Exception\WhatConvertsClientException
		* @throws WhatConverts\Exception\WhatConvertsApiException
		* @return object
	*/
	public function getAccount($account_id)
	{
		try 
		{
			// Bad account id simply throws a 400 error, no error message
			$response = $this->wc_client->get("accounts/$account_id");
			$result = $this->parseResponse($response);
			$this->checkForApiErrors($result);
			return $result->accounts[0];
		}
		catch (TransferException $e) 
		{
			throw new WhatConvertsClientException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
		* Create a new account in WhatConverts
		* @param string $account_name
		* @throws WhatConverts\Exception\WhatConvertsClientException
		* @throws WhatConverts\Exception\WhatConvertsApiException
		* @return object
	*/
	public function createAccount($account_name)
	{
		try 
		{
			$params = [
				'account_name' => $account_name,
				'create_profile' => "true"
			];
			$response = $this->wc_client->post("accounts", [
				'form_params' => $params
			]);
			$result = $this->parseResponse($response);
			$this->checkForApiErrors($result);
			return $result;
		}
		catch (TransferException $e)
		{
			throw new WhatConvertsClientException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
		* Edit an account
		* @param string $account_id
		* @param string $account_name (only editable field in account resource)
		* @throws WhatConverts\Exception\WhatConvertsClientException
		* @throws WhatConverts\Exception\WhatConvertsApiException
		* @return object
	*/
	public function editAccount($account_id, $account_name)
	{
		try 
		{
			$params = [
				'account_name' => $account_name
			];
			$response = $this->wc_client->post("accounts/$account_id", [
				'form_params' => $params
			]);
			$result = $this->parseResponse($response);
			$this->checkForApiErrors($result);
			return $result;
		}
		catch (TransferException $e)
		{
			throw new WhatConvertsClientException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
		* Delete an account from WhatConverts
		* @param string $account_id
		* @throws WhatConverts\Exception\WhatConvertsClientException
		* @throws WhatConverts\Exception\WhatConvertsApiException
		* @return object
	*/
	public function deleteAccount($account_id)
	{
		try 
		{
			$response = $this->wc_client->delete("accounts/$account_id");
			$result = $this->parseResponse($response);
			$this->checkForApiErrors($result);
			return $result;
		}
		catch (TransferException $e)
		{
			throw new WhatConvertsClientException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
		* Get Leads
		* @param $options - array
		* supported params: https://www.whatconverts.com/api/leads
		* @throws WhatConverts\Exception\WhatConvertsClientException
		* @throws WhatConverts\Exception\WhatConvertsApiException
		* @return object
	*/
	public function getLeads($options = [])
	{
		try 
		{
			$leads = [];
			$pageNumber = 1; //first page is considered 1, not 0 indexed
			$leadsPerPageDesired = 250; //250 is the max allowed
			$params = [
				'page_number' => $pageNumber, 
				'leads_per_page' => $leadsPerPageDesired
			];
			// merge in $options array to $params
			unset($options['page_number']);
			unset($options['leads_per_page']);
			$params += (array)$options;
			$response = $this->wc_client->get("leads", [
				'query' => http_build_query($params)
			]);
			$result = $this->parseResponse($response);
			$this->checkForApiErrors($result);
			$totalPages = $result->total_pages;
			$leads = $result->leads;
			$pageNumber++; //reflect that we pulled the first page already
			while($pageNumber <= $totalPages) 
			{
				$params = [
					'accounts_per_page' => $leadsPerPageDesired,
					'page_number' => $pageNumber
				];
				$response = $this->wc_client->get("leads", [
					'query' => http_build_query($params)
				]);
				$result = $this->parseResponse($response);
				$this->checkForApiErrors($result);
				//operator overloading (merge arrays, preserve keys)
				$leads = array_merge($leads, $result->leads);
				$pageNumber++;
			}
			return $leads;
		}
		catch (TransferException $e)
		{
			throw new WhatConvertsClientException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
		* Get a single lead from WhatConverts
		* @param string $lead_id
		* @throws WhatConverts\Exception\WhatConvertsClientException
		* @throws WhatConverts\Exception\WhatConvertsApiException
		* @return object
	*/
	public function getLead($lead_id)
	{
		try 
		{
			$response = $this->wc_client->get("leads/$lead_id");
			$result = $this->parseResponse($response);
			$this->checkForApiErrors($result);
			return $result->leads[0];
		} 
		catch (TransferException $e)
		{
			throw new WhatConvertsClientException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
		* Create a new lead in WhatConverts
		* @param string $profile_id
		* @param string $lead_type
		* @param array $attributes
		* @throws WhatConverts\Exception\WhatConvertsClientException
		* @throws WhatConverts\Exception\WhatConvertsApiException
		* @return object
	*/
	public function createLead($profile_id, $lead_type, $attributes = [])
	{
		try 
		{
			$params = [
				'profile_id' => $profile_id,
				'lead_type' => $lead_type
			];
			// merge in $attributes array to $params
			unset($attributes['profile_id']);
			unset($attributes['lead_type']);
			$params += (array)$attributes;
			$response = $this->wc_client->post("leads", [
				'form_params' => $params
			]);
			$result = $this->parseResponse($response);
			$this->checkForApiErrors($result);
			return $result;
		}
		catch (TransferException $e)
		{
			throw new WhatConvertsClientException($e->getMessage(), $e->getCode(), $e);
		}
	}

	//TODO - finish implementing API resource methods

}