<?php

namespace Voidpointers\Etsy;

use Gentor\OAuth1Etsy\Client\Server\Etsy;
use Illuminate\Support\Facades\Cache;
use League\OAuth1\Client\Credentials\TokenCredentials;

class Server
{
    /** @var Etsy $server */
    private $server;

    /** @var TokenCredentials $tokenCredentials */
    private $tokenCredentials;

    /**
     * EtsyService constructor.
     * 
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->server = new Etsy([
            'identifier' => $config['consumer_key'],
            'secret' => $config['consumer_secret'],
            'scope' => !empty($config['scope']) ? $config['scope'] : '',
            'callback_uri' => ''
        ]);

        if (!empty($config['access_token']) && !empty($config['access_token_secret'])) {
            $tokenCredentials = new TokenCredentials();
            $tokenCredentials->setIdentifier($config['access_token']);
            $tokenCredentials->setSecret($config['access_token_secret']);

            $this->tokenCredentials = $tokenCredentials;
        }
    }

    /**
     * @param $callbackUri
     * @return string
     */
    public function authorize($callbackUri)
    {
        $this->server->getClientCredentials()->setCallbackUri($callbackUri);

        // Retrieve temporary credentials
        $temporaryCredentials = $this->server->getTemporaryCredentials();

        // Store credentials in the session, we'll need them later
        Cache::store('file')->put('temporary_credentials', serialize($temporaryCredentials), 3600);

        return $this->server->getAuthorizationUrl($temporaryCredentials);
    }

    /**
     * @param $token
     * @param $verifier
     * @return \League\OAuth1\Client\Credentials\TokenCredentials
     */
    public function approve($token, $verifier)
    {
        // Retrieve the temporary credentials we saved before
        $temporaryCredentials = unserialize(Cache::store('file')->get('temporary_credentials'));

        $this->tokenCredentials = $this->server->getTokenCredentials($temporaryCredentials, $token, $verifier);
        return $this->tokenCredentials;
    }

    /**
     * Get the user's unique identifier (primary key).
     *
     * @param bool $force
     *
     * @return string|int
     */
    public function getUserUid($force = false)
    {
        $userDetails = $this->getUserDetails($force);

        return $userDetails->uid;
    }

    /**
     * Get user details by providing valid token credentials.
     *
     * @param bool $force
     *
     * @return \League\OAuth1\Client\Server\User
     */
    public function getUserDetails($force = false)
    {
        return $this->server->getUserDetails($this->tokenCredentials, $force);
    }

    /**
     * @param $method
     * @param array $args
     * @return array
     */
    public function __call($method, array $args)
    {
        $api = new Request($this->server, $this->tokenCredentials);

        return call_user_func_array([$api, $method], $args);
    }
}
