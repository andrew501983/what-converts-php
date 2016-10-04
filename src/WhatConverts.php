<?php
namespace WhatConverts;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Response;
use WhatConverts\Exception\WhatConvertsApiException;

class WhatConverts
{

	use AccountsResources;
	use ProfilesResources;
	use LeadsResources;

	const WC_BASE_ENDPOINT = 'https://app.whatconverts.com/api/v1/';
	protected $wc_api_token;
	protected $wc_api_secret;
	protected $wc_client;

	/**
	 * WhatConverts constructor.
	 * @param $wc_api_token
	 * @param $wc_api_secret
     */
	public function __construct($wc_api_token, $wc_api_secret)
	{
		$this->wc_api_token = $wc_api_token;
		$this->wc_api_secret = $wc_api_secret;
		$this->wc_client = new HttpClient([
			// Base URI is used with relative requests
			'base_uri' => static::WC_BASE_ENDPOINT,
			// HTTP Basic Auth
			'auth' => [
				$this->wc_api_token,
				$this->wc_api_secret
			],
			// disable throwing exceptions on an HTTP protocol errors (i.e., 4xx and 5xx responses). Allows us to throw our own API exception when an error_message is encountered
			'http_errors' => false
		]);
	}

	/**
	 * Parse Psr7 Response into JSON
	 * @param Response $response
	 * @return mixed
	 * @throws WhatConvertsApiException
     */
	protected function parseResponse(Response $response)
	{
		$result = json_decode(
			$response
				->getBody()
				->getContents()
		);
		if (isset($result->error_message))
		{
			throw new WhatConvertsApiException($result->error_message, $response->getStatusCode());
		}
		return $result;
	}

}