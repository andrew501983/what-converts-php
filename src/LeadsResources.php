<?php
namespace WhatConverts;

use GuzzleHttp\Exception\TransferException;
use WhatConverts\Exception\WhatConvertsClientException;
use WhatConverts\Exception\WhatConvertsApiException;

trait LeadsResources
{

    /**
     * Get paginated, filtered leads
     * @param array $options
     * @return mixed
     * @throws WhatConvertsApiException
     * @throws WhatConvertsClientException
     */
    public function getLeads(array $options = [])
	{
		try
		{
			$params = $options;
			$response = $this->wc_client->get(
				"leads",
				[
					'query' => http_build_query($params)
				]
			);
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
     * Get all leads, non-paginated
     * @param array $options
     * @return array
     * @throws WhatConvertsApiException
     * @throws WhatConvertsClientException
     */
    public function getAllLeads(array $options = [])
	{
		try
		{
			$leads = [];
			$pageNumber = 1; //not 0 indexed
			$leadsPerPageDesired = 250; //max = 250
			$params = [
				'page_number' => $pageNumber,
				'leads_per_page' => $leadsPerPageDesired
			];
			// merge in $options array to $params. if page_number and leads_per_page
			// specified by the user, do not use them. This method returns a full lead list!
			unset($options['page_number']);
			unset($options['leads_per_page']);
			$params += $options;
			$response = $this->wc_client->get(
				"leads",
				[
					'query' => http_build_query($params)
				]
			);
			$result = $this->parseResponse($response);
			$totalPages = $result->total_pages;
			$leads = $result->leads;
			$pageNumber++;
			while($pageNumber <= $totalPages)
			{
				$params = [
					'page_number' => $pageNumber,
					'leads_per_page' => $leadsPerPageDesired
				];
				$params += $options;
				$response = $this->wc_client->get(
					"leads", 
					[
						'query' => http_build_query($params)
					]
				);
				$result = $this->parseResponse($response);
				$leads = array_merge($leads, $result->leads);
				$pageNumber++;
			}
			return $leads;
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
     * Get detail for a single lead
     * @param $lead_id
     * @return mixed
     * @throws WhatConvertsApiException
     * @throws WhatConvertsClientException
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
			throw new WhatConvertsClientException(
				$e->getMessage(),
				$e->getCode(),
				$e
			);
		}
	}

    /**
     * Create a new lead
     * @param $profile_id
     * @param $lead_type
     * @param array $attributes
     * @return mixed
     * @throws WhatConvertsApiException
     * @throws WhatConvertsClientException
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
			$params += $attributes;
			$response = $this->wc_client->post(
				"leads", 
				[
					'form_params' => $params
				]
			);
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
     * Edit a lead
     * @param $lead_id
     * @param array $attributes
     * @return mixed
     * @throws WhatConvertsApiException
     * @throws WhatConvertsClientException
     */
    public function editLead($lead_id, array $attributes = [])
	{
		try 
		{
			$params = $attributes;
			$response = $this->wc_client->post(
				"leads/$lead_id",
				[
					'form_params' => $params
				]
			);
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