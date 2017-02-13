<?php
namespace WhatConverts;

use GuzzleHttp\Exception\TransferException;
use WhatConverts\Exception\WhatConvertsClientException;
use WhatConverts\Exception\WhatConvertsApiException;

trait AccountsResources
{

    /**
     * Get paginated, filtered accounts
     * @param array $options
     * @return mixed
     * @throws WhatConvertsApiException
     * @throws WhatConvertsClientException
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
			throw new WhatConvertsClientException(
				$e->getMessage(),
				$e->getCode(),
				$e
			);
		}
	}

    /**
     * * Get a full account list, non-paginated
     * @param array $options
     * @return array
     * @throws WhatConvertsApiException
     * @throws WhatConvertsClientException
     */
    public function getAllAccounts(array $options = [])
	{
		try
		{
			$accounts = [];
			$pageNumber = 1; //not 0 indexed
			$accountsPerPageDesired = 250; //max allowed = 250
			$params = [
				'page_number' => $pageNumber,
				'accounts_per_page' => $accountsPerPageDesired
			];
			// merge in $options array to $params. if page_number and accounts_per_page
			// specified by the user, do not use them. This method returns a full account list!
			unset($options['page_number']);
			unset($options['accounts_per_page']);
			$params += $options;
			$response = $this->wc_client->get("accounts", [
				'query' => http_build_query($params)
			]);
			$result = $this->parseResponse($response);
			$totalPages = $result->total_pages;
			$accounts = $result->accounts;
			$pageNumber++;
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
				$accounts = array_merge($accounts, $result->accounts);
				$pageNumber++;
			}
			return $accounts;
		}
		catch (TransferException $e)
		{
			throw new WhatConvertsClientException(
				$e->getMessage(),
				$e->getCode(),
				$e
			);
		}
	}

    /**
     * Get detail for a single account
     * @param $account_id
     * @return mixed
     * @throws WhatConvertsApiException
     * @throws WhatConvertsClientException
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
			throw new WhatConvertsClientException(
				$e->getMessage(),
				$e->getCode(),
				$e
			);
		}
	}

    /**
     * Create a new account resource
     * @param $account_name
     * @param bool $create_profile
     * @return mixed
     * @throws WhatConvertsApiException
     * @throws WhatConvertsClientException
     */
    public function createAccount($account_name, $create_profile = true)
	{
		try 
		{
			$params = [
				'account_name' => $account_name,
				'create_profile' => $create_profile ? "true" : "false"
			];
			$response = $this->wc_client->post("accounts", [
				'form_params' => $params
			]);
			return $result = $this->parseResponse($response);
		}
		catch (TransferException $e)
		{
			throw new WhatConvertsClientException(
				$e->getMessage(),
				$e->getCode(),
				$e
			);
		}
	}

    /**
     * Edit an account
     * @param $account_id
     * @param $account_name
     * @return mixed
     * @throws WhatConvertsApiException
     * @throws WhatConvertsClientException
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
			throw new WhatConvertsClientException(
				$e->getMessage(),
				$e->getCode(),
				$e
			);
		}
	}

    /**
     * Delete an account
     * @param $account_id
     * @return mixed
     * @throws WhatConvertsApiException
     * @throws WhatConvertsClientException
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
			throw new WhatConvertsClientException(
				$e->getMessage(),
				$e->getCode(),
				$e
			);
		}
	}
	
}