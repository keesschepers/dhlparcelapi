<?php

namespace Keesschepers\DhlParcelApi;

use Keesschepers\DhlParcelApi\DhlApiException;
use GuzzleHttp\Client as GuzzleHttpClient;

class Client
{
    private $userId;
    private $key;
    private $httpClient;
    private $apiTimeout;

    const ENDPOINT = 'https://api-gw.dhlparcel.nl';

    public function __construct(String $userId = null, String $key = null, Float $apiTimeout = 0.5)
    {
        $this->apiUserId = $userId;
        $this->apiKey = $key;
        $this->apiTimeout = $apiTimeout;
        $this->httpClient = new GuzzleHttpClient(['base_uri' => self::ENDPOINT]);
    }

    public function timeWindows($countryCode, $postalCode)
    {
        $response = $this->httpClient->get(
            '/time-windows',
            [
                'timeout' => ($this->apiTimeout / 1000),
            ],
            [
                'query' => ['countryCode' => $countryCode, 'postalCode' => $postalCode]
            ]
        );

        if (!$response->isSuccesfull()) {
            throw new DhlApiException('Could not retrieve time window information due to API server error.');
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
