<?php
namespace WhatConverts;

use GuzzleHttp\Exception\TransferException;
use WhatConverts\Exception\WhatConvertsClientException;
use WhatConverts\Exception\WhatConvertsApiException;

trait ProfilesResources
{

    /**
     * Get paginated, filtered profiles
     * @param array $options
     * @return mixed
     * @throws WhatConvertsApiException
     * @throws WhatConvertsClientException
     */
    public function getProfiles(array $options = [])
	{
		try
		{
			$params = $options;
			$response = $this->wc_client->get("profiles", [
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
     * Get all profiles, non-paginated
     * @param array $options
     * @return array
     * @throws WhatConvertsApiException
     * @throws WhatConvertsClientException
     */
    public function getAllProfiles(array $options = [])
	{
		try
		{
			$profiles = [];
			$pageNumber = 1; //first page is considered 1, not 0 indexed
			$profilesPerPageDesired = 250; //250 is the max allowed
			$params = [
				'page_number' => $pageNumber,
				'profiles_per_page' => $profilesPerPageDesired
			];
			// merge in $options array to $params
			unset($options['page_number']);
			unset($options['profiles_per_page']);
			$params += $options;
			$response = $this->wc_client->get("profiles", [
				'query' => http_build_query($params)
			]);
			$result = $this->parseResponse($response);
			$totalPages = $result->total_pages;
			$profiles = $result->profiles;
			$pageNumber++; //reflect that we pulled the first page already
			while($pageNumber <= $totalPages)
			{
				$params = [
					'page_number' => $pageNumber,
					'profiles_per_page' => $profilesPerPageDesired
				];
				$params += $options;
				$response = $this->wc_client->get("profiles", [
					'query' => http_build_query($params)
				]);
				$result = $this->parseResponse($response);
				//operator overloading (merge arrays, preserve keys)
				$profiles = array_merge($profiles, $result->profiles);
				$pageNumber++;
			}
			return $profiles;
		}
		catch (TransferException $e)
		{
			throw new WhatConvertsClientException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
     * Get detail for a single account profile
     * @param $account_id
     * @param $profile_id
     * @return mixed
     * @throws WhatConvertsApiException
     * @throws WhatConvertsClientException
     */
    public function getProfile($account_id, $profile_id)
	{
		try 
		{
			$response = $this->wc_client->get("accounts/$account_id/profiles/$profile_id");
			return $result = $this->parseResponse($response)->profiles[0];
		} 
		catch (TransferException $e)
		{
			throw new WhatConvertsClientException($e->getMessage(), $e->getCode(), $e);
		}
	}

    /**
     * Create a new account profile
     * @param $account_id
     * @param $profile_name
     * @return mixed
     * @throws WhatConvertsApiException
     * @throws WhatConvertsClientException
     */
    public function createProfile($account_id, $profile_name)
	{
		try 
		{
			$params = [
				'profile_name' => $profile_name
			];
			$response = $this->wc_client->post("accounts/$account_id/profiles", [
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
     * Edit a profile
     * @param $account_id
     * @param $profile_id
     * @param $profile_name
     * @return mixed
     * @throws WhatConvertsApiException
     * @throws WhatConvertsClientException
     */
    public function editProfile($account_id, $profile_id, $profile_name)
	{
		try 
		{
			$params = [
				'profile_name' => $profile_name
			];
			$response = $this->wc_client->post("accounts/$account_id/profiles/$profile_id", [
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
     * Delete an account profile
     * @param $account_id
     * @param $profile_id
     * @return mixed
     * @throws WhatConvertsApiException
     * @throws WhatConvertsClientException
     */
    public function deleteProfile($account_id, $profile_id)
	{
		try
		{
			$response = $this->wc_client->delete("accounts/$account_id/profiles/$profile_id");
			return $result = $this->parseResponse($response);
		}
		catch (TransferException $e)
		{
			throw new WhatConvertsClientException($e->getMessage(), $e->getCode(), $e);
		}
	}
	
}