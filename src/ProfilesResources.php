<?php
namespace WhatConverts;

use GuzzleHttp\Exception\TransferException;
use WhatConverts\Exception\WhatConvertsClientException;

trait ProfilesResources
{

	/**
		* Get paginated profiles from WhatConverts
		* @param array $options
		* supported params: https://www.whatconverts.com/api/profiles
		* @throws WhatConverts\Exception\WhatConvertsClientException
		* @throws WhatConverts\Exception\WhatConvertsApiException
		* @return object
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
		* Get all profiles from WhatConverts
		* @param array $options
		* supported params: https://www.whatconverts.com/api/profiles
		* @throws WhatConverts\Exception\WhatConvertsClientException
		* @throws WhatConverts\Exception\WhatConvertsApiException
		* @return array
	*/
	public function getAllProfiles(array $options = [])
	{
		try
		{
			$accounts = [];
			$pageNumber = 1; //first page is considered 1, not 0 indexed
			$profilesPerPageDesired = 250; //250 is the max allowed or returned
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
			$accounts = $result->profiles;
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
		* Get a single account profile
		* @param string $account_id
		* @param string $profile_id
		* @throws WhatConverts\Exception\WhatConvertsClientException
		* @throws WhatConverts\Exception\WhatConvertsApiException
		* @return object
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
		* Create a new profile under an account
		* @param string $account_id
		* @param string $profile_name
		* @throws WhatConverts\Exception\WhatConvertsClientException
		* @throws WhatConverts\Exception\WhatConvertsApiException
		* @return object
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
		* Edit details for a single profile
		* @param string $account_id
		* @param string $profile_name (only editable field in profile resource)
		* @throws WhatConverts\Exception\WhatConvertsClientException
		* @throws WhatConverts\Exception\WhatConvertsApiException
		* @return object
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
		* Delete a single profile. This will delete all numbers, leads, and
		* other settings associated with the profile.
		* @param string $account_id
		* @param string $profile_id
		* @throws WhatConverts\Exception\WhatConvertsClientException
		* @throws WhatConverts\Exception\WhatConvertsApiException
		* @return object
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