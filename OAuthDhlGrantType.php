<?php

namespace Keesschepers\DhlParcelApi;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use kamermans\OAuth2\Utils\Helper;
use kamermans\OAuth2\Utils\Collection;
use kamermans\OAuth2\Signer\ClientCredentials\SignerInterface;
use kamermans\OAuth2\GrantType\GrantTypeInterface;

/**
 * Special oAuth grant type that can deal with the DHL API.
 *
 * @link http://tools.ietf.org/html/rfc6749#section-4.4
 */
class OAuthDhlGrantType implements GrantTypeInterface
{
    /**
     * The token endpoint client.
     *
     * @var ClientInterface
     */
    private $client;

    /**
     * Configuration settings.
     *
     * @var Collection
     */
    private $config;

    /**
     * @param ClientInterface $client
     * @param array           $config
     */
    public function __construct(ClientInterface $client, array $config)
    {
        $this->client = $client;
        $this->config = Collection::fromConfig($config,
            // Defaults
            [
                'client_secret' => '',
                'scope' => '',
            ],
            // Required
            [
                'client_id',
            ]
        );
    }

    public function getRawData(SignerInterface $clientCredentialsSigner, $refreshToken = null)
    {
        $body = [
            'userId' => $this->config['client_id'],
            'key' => $this->config['client_secret'],
        ];

        $response = $this->client->post($this->client->getConfig()['base_uri'], ['json' => $body]);
        $data = json_decode($response->getBody(), true);

        return [
            'access_token' => $data['accessToken'],
            'refresh_token' => $data['refreshToken'],
            'expires_in' => $data['accessTokenExpiration'],
        ];
    }
}

