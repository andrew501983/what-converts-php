<?php
namespace WhatConverts;

use GuzzleHttp\Exception\TransferException;
use WhatConverts\Exception\WhatConvertsClientException;

trait AccountsResources
{

	/**
		* Get paginated accounts from WhatConverts
		* @param array $options
		* supported params: https://www.whatconverts.com/api/accounts
		* @throws WhatConverts\Exception\WhatConvertsClientException
		* @throws WhatConverts\Exception\WhatConvertsApiException
		* @return object
	*/
	public function getAccounts(array $options = [])
	{
		try
		{
			$params = $options;
			$response = $this->wc_client->get("accounts", [
				'query' => http_build_query($params)
			]);
			return $result = $this->parseResponse($response);
		}
		catch (TransferException $e)
		{
			throw new WhatConvertsClientException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
		* Get all accounts from WhatConverts
		* @param array $options
		* supported params: https://www.whatconverts.com/api/accounts
		* @throws WhatConverts\Exception\WhatConvertsClientException
		* @throws WhatConverts\Exception\WhatConvertsApiException
		* @return array
	*/
	public function getAllAccounts(array $options = [])
	{
		try
		{
			$accounts = [];
			$pageNumber = 1; //first page is considered 1, not 0 indexed
			$accountsPerPageDesired = 250; //250 is the max allowed or returned
			$params = [
				'page_number' => $pageNumber,
				'accounts_per_page' => $accountsPerPageDesired
			];
			// merge in $options array to $params
			unset($options['page_number']);
			unset($options['accounts_per_page']);
			$params += $options;
			$response = $this->wc_client->get("accounts", [
				'query' => http_build_query($params)
			]);
			$result = $this->parseResponse($response);
			$totalPages = $result->total_pages;
			$accounts = $result->accounts;
			$pageNumber++; //reflect that we pulled the first page already
			while($pageNumber <= $totalPages)
			{
				$params = [
					'page_number' => $pageNumber,
					'accounts_per_page' => $accountsPerPageDesired
				];
				$params += $options;
				$response = $this->wc_client->get("accounts", [
					'query' => http_build_query($params)
				]);
				$result = $this->parseResponse($response);
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
			$response = $this->wc_client->get("accounts/$account_id");
			return $this->parseResponse($response)->accounts[0];
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
			return $result = $this->parseResponse($response);
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
			return $result = $this->parseResponse($response);
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
			return $result = $this->parseResponse($response);
		}
		catch (TransferException $e)
		{
			throw new WhatConvertsClientException($e->getMessage(), $e->getCode(), $e);
		}
	}
	
}