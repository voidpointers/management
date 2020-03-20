<?php

namespace Voidpointers\Etsy;

use Voidpointers\Etsy\Exceptions\RequestException as EtsyRequestException;
use Voidpointers\Etsy\Exceptions\ResponseException as EtsyResponseException;
use Voidpointers\Etsy\Helpers\RequestValidator;
use Gentor\OAuth1Etsy\Client\Server\Etsy;
use League\OAuth1\Client\Credentials\TokenCredentials;
use GuzzleHttp\Exception\BadResponseException;

class Request
{
    /**
     * @var Etsy $server
     */
    private $server;

    /**
     * @var array $methods
     */
    private $methods = [];

    /**
     * @var TokenCredentials $tokenCredentials
     */
    private $tokenCredentials;

    /**
     * @var \GuzzleHttp\Client $client
     */
    private $client;

    /**
     * EtsyApi constructor.
     * @param Etsy $server
     * @param TokenCredentials $tokenCredentials
     */
    public function __construct(
        Etsy $server, TokenCredentials $tokenCredentials = null)
    {
        $this->server = $server;
        $this->client = $this->server->createHttpClient();
        $this->tokenCredentials = $tokenCredentials;

        $methods_file = dirname(realpath(__FILE__)) . '/methods.json';
        $this->methods = json_decode(file_get_contents($methods_file), true);
    }

    /**
     * @param $arguments
     * @return mixed
     * @throws EtsyResponseException
     * @throws \Exception
     */
    protected function request($arguments)
    {
        $method = $this->methods[$arguments['method']];
        $args = $arguments['args'];
        $params = $this->prepareParameters($args['params']);
        $data = @$this->prepareData($args['data']);

        $uri = preg_replace_callback('@:(.+?)(\/|$)@', function ($matches) use ($args) {
            return $args["params"][$matches[1]] . $matches[2];
        }, $method['uri']);

        if (!empty($args['associations'])) {
            $params['includes'] = $this->prepareAssociations($args['associations']);
        }
        if (!empty($args['fields'])) {
            $params['fields'] = $this->prepareFields($args['fields']);
        }
        if (!empty($params)) {
            $uri .= "?" . http_build_query($params);
        }

        return $this->handleError($this->sendRequest($method['http_method'], $uri, $data));
    }

    /**
     * @param $method
     * @param $path
     * @param array $params
     * @return array
     * @throws \Exception
     */
    protected function sendRequest($method, $path, $params = [])
    {
        $url = $this->getEndpointUrl($path);
        if ($file = $this->prepareFile($params)) {
            $params = [];
        }

        if ($this->tokenCredentials) {
            $headers = $this->server->getHeaders($this->tokenCredentials, $method, $url, $params);
            $options = [
                'headers' => $headers,
            ];
        } else {
            $options = [
                'query' => ['api_key' => $this->server->getClientCredentials()->getIdentifier()],
            ];
        }

        if (in_array($method, ['POST', 'PUT'])) {
            if ($file) {
                $options['multipart'] = $file;
            } else {
                $options['form_params'] = $params;
            }
        }
        $options['proxy'] = env('HTTP_PROXY');

        try {
            $response = $this->client->request($method, $url, $options);
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
            $body = $response->getBody();
            $statusCode = $response->getStatusCode();
            throw new EtsyResponseException("Received error [$body] with status code [$statusCode]", $response);
        }

        return json_decode((string)$response->getBody(), true);
    }

    /**
     * @param $response
     * @return mixed
     * @throws EtsyResponseException
     */
    protected function handleError($response)
    {
        $errors = '';
        $results = !empty($response['results']) ? $response['results'] : [];
        foreach ($results as $result) {
            if (!empty($result['error_messages'])) {
                foreach ($result['error_messages'] as $error_message) {
                    $errors .= $error_message . '; ';
                }
            }
        }

        if (!empty($errors)) {
            throw new EtsyResponseException(rtrim($errors, '; '), $response);
        }

        return $response;
    }

    /**
     * @param $uri
     * @return string
     */
    private function getEndpointUrl($uri)
    {
        return Etsy::API_URL . ltrim($uri, '/');
    }

    /**
     * @param $data
     * @return array
     */
    private function prepareData($data)
    {
        $result = array();
        foreach ($data as $key => $value) {
            $type = gettype($value);
            if ($type !== 'boolean') {
                $result[$key] = $value;
                continue;
            }
            $result[$key] = $value ? 1 : 0;
        }
        return $result;
    }

    /**
     * @param $data
     * @return array|bool
     */
    private function prepareFile($data)
    {
        if (!isset($data['image']) && !isset($data['file'])) {
            return false;
        }
        $key = isset($data['image']) ? 'image' : 'file';
        return [[
            'name' => $key,
            'contents' => fopen($data[$key], 'r')
        ]];
    }

    /**
     * @param $params
     * @return array
     */
    private function prepareParameters($params)
    {
        $query_pairs = array();
        // $allowed = ["limit", "offset", "page", "sort_on", "sort_order", "include_private", "language"];
        if ($params) {
            foreach ($params as $key => $value) {
                // if (in_array($key, $allowed)) {
                $query_pairs[$key] = $value;
                // }
            }
        }
        return $query_pairs;
    }

    /**
     * @param $associations
     * @return mixed
     */
    private function prepareAssociations($associations)
    {
        $includes = array();
        foreach ($associations as $key => $value) {
            if (is_array($value)) {
                $includes[] = $this->buildAssociation($key, $value);
            } else {
                $includes[] = $value;
            }
        }
        return implode(',', $includes);
    }

    /**
     * @param $fields
     * @return mixed
     */
    private function prepareFields($fields)
    {
        return implode(',', $fields);
    }

    /**
     * @param $assoc
     * @param $conf
     * @return string
     */
    private function buildAssociation($assoc, $conf)
    {
        $association = $assoc;
        if (isset($conf['select'])) {
            $association .= "(" . implode(',', $conf['select']) . ")";
        }
        if (isset($conf['scope'])) {
            $association .= ':' . $conf['scope'];
        }
        if (isset($conf['limit'])) {
            $association .= ':' . $conf['limit'];
        }
        if (isset($conf['offset'])) {
            $association .= ':' . $conf['offset'];
        }
        if (isset($conf['associations'])) {
            $association .= '/' . $this->prepareAssociations($conf['associations']);
        }
        return $association;
    }

    /**
     *
     */
    private function generateMethodsDoc()
    {
        $doc = '';
        foreach ($this->methods as $name => $info) {
            $doc .= "* @method array $name(array \$argument = [])\n";
        }
        echo($doc);
        exit;
    }

    /**
     * array('params' => array(), 'data' => array())
     * :params for uri params
     * :data for "post fields"
     *
     * @param $method
     * @param $args
     * @return array
     * @throws \Exception
     */
    public function __call($method, $args)
    {
        if (isset($this->methods[$method])) {
            $validArguments = RequestValidator::validateParams(@$args[0], $this->methods[$method]);
            if (isset($validArguments['_invalid'])) {
                throw new EtsyRequestException('Invalid params for method "' . $method . '": ' . implode(', ', $validArguments['_invalid']) . ' - ' . json_encode($this->methods[$method]));
            }
            return call_user_func_array(array($this, 'request'), array(
                array(
                    'method' => $method,
                    'args' => array(
                        'data' => @$validArguments['_valid'],
                        'params' => @$args[0]['params'],
                        'associations' => @$args[0]['associations'],
                        'fields' => @$args[0]['fields']
                    )
                )));
        } else {
            throw new EtsyRequestException('Method "' . $method . '" not exists');
        }
    }
}

