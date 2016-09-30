<?php
namespace WhatConverts;

use GuzzleHttp\Exception\TransferException;
use WhatConverts\Exception\WhatConvertsClientException;

trait LeadsResources
{

	/**
		* Get paginated leads from WhatConverts
		* @param array $options
		* supported params: https://www.whatconverts.com/api/leads
		* @throws WhatConverts\Exception\WhatConvertsClientException
		* @throws WhatConverts\Exception\WhatConvertsApiException
		* @return object
	*/
	public function getLeads(array $options = [])
	{
		try
		{
			$params = $options;
			$response = $this->wc_client->get("leads", [
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
		* Get all leads from WhatConverts
		* @param array $options
		* supported params: https://www.whatconverts.com/api/leads
		* @throws WhatConverts\Exception\WhatConvertsClientException
		* @throws WhatConverts\Exception\WhatConvertsApiException
		* @return array
	*/
	public function getAllLeads(array $options = [])
	{
		try
		{
			$accounts = [];
			$pageNumber = 1; //first page is considered 1, not 0 indexed
			$leadsPerPageDesired = 250; //250 is the max allowed or returned
			$params = [
				'page_number' => $pageNumber,
				'leads_per_page' => $leadsPerPageDesired
			];
			// merge in $options array to $params
			unset($options['page_number']);
			unset($options['leads_per_page']);
			$params += $options;
			$response = $this->wc_client->get("leads", [
				'query' => http_build_query($params)
			]);
			$result = $this->parseResponse($response);
			$totalPages = $result->total_pages;
			$accounts = $result->leads;
			$pageNumber++; //reflect that we pulled the first page already
			while($pageNumber <= $totalPages)
			{
				$params = [
					'page_number' => $pageNumber,
					'leads_per_page' => $leadsPerPageDesired
				];
				$params += $options;
				$response = $this->wc_client->get("leads", [
					'query' => http_build_query($params)
				]);
				$result = $this->parseResponse($response);
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
			return $result = $this->parseResponse($response)->leads[0];
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
	public function createLead($profile_id, $lead_type, array $attributes = [])
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
			return $result = $this->parseResponse($response);
		}
		catch (TransferException $e)
		{
			throw new WhatConvertsClientException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
		* Create lead details in WhatConverts
		* @param string $lead_id
		* @param array $attributes
		* supported params: https://www.whatconverts.com/api/leads
		* @throws WhatConverts\Exception\WhatConvertsClientException
		* @throws WhatConverts\Exception\WhatConvertsApiException
		* @return object
	*/
	public function editLead($lead_id, array $attributes = [])
	{
		try 
		{
			$params = (array)$attributes;
			$response = $this->wc_client->post("leads/$lead_id", [
				'form_params' => $params
			]);
			return $result = $this->parseResponse($response);
		}
		catch (TransferException $e)
		{
			throw new WhatConvertsClientException($e->getMessage(), $e->getCode(), $e);
		}
	}

}