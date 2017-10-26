<?php

namespace Keesschepers\DhlParcelApi;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Exception\BadResponseException;
use kamermans\OAuth2\GrantType\ClientCredentials;
use kamermans\OAuth2\OAuth2Middleware;
use Keesschepers\DhlParcelApi\DhlApiException;

class DhlClient
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
    }

    private function setupClient()
    {
        if (null !== $this->httpClient) {
            return;
        }

        $oAuthClient = new Client(['base_uri' => sprintf('%s/%s', self::ENDPOINT, 'authenticate/api-key')]);
        $grantType = new OAuthDhlGrantType($oAuthClient, ['client_id' => $this->apiUserId, 'client_secret' => $this->apiKey]);
        $oauth = new OAuth2Middleware($grantType);

        $stack = HandlerStack::create();
        $stack->push($oauth);

        $this->httpClient = new Client(
            [
                'base_uri' => self::ENDPOINT,
                'handler' => $stack,
                'auth' => 'oauth',
            ]
        );
    }

    public function timeWindows($countryCode, $postalCode)
    {
        $this->setupClient();

        $response = $this->httpClient->get(
            '/time-windows',
            [
                'timeout' => ($this->apiTimeout / 1000),
                'query' => ['countryCode' => $countryCode, 'postalCode' => $postalCode],
            ]
        );

        if (200 !== $response->getStatusCode()) {
            throw new DhlApiException('Could not retrieve time window information due to API server error.');
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    public function createLabel(array $parameters)
    {
        $this->setupClient();

        try {
            $response = $this->httpClient->post(
                '/labels',
                [
                    'timeout' => ($this->apiTimeout / 1000),
                    'json' => $parameters,
                ]
            );
        } catch (BadResponseException $e) {
            throw new DhlApiException(sprintf('Could not could not create a label due to API server error: %s', $e->getResponse()->getBody(true)));
        }

        return new DhlParcel(json_decode($response->getBody()->getContents(), true));
    }
}
