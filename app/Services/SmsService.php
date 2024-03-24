<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


class SmsService
{
    protected $API_URL = 'https://smsplus.sslwireless.com/api/v3/send-sms';
    /**
     * @var Client
     */
    private $client;
    /**
     * @var array|string[]
     */
    private $headers;

    public static function init(): self
    {
        return new self();
    }

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $API_URL = 'https://smsplus.sslwireless.com/api/v3/send-sms';
        $this->client = new Client(['base_uri' => $API_URL]);

        $this->headers = [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ];
    }
    /**
     * @param  array  $data
     * @return array
     * @throws Exception
     */
    public function sendSMS(array $data): array
    {
        $url               = $this->API_URL;
        $request           = ['headers' => $this->headers, 'json' => $data,];

        return $this->executeRequest('POST', $url, $request);
    }

    /**
     * @throws Exception
     */
    private function executeRequest(string $method, string $url, array $requestData): array
    {
        try
        {
            $response = $this->client->request($method, $url, $requestData);

            return [
                'data' => json_decode($response->getBody()->getContents(), true),
                'code' => $response->getStatusCode(),
            ];
        }
        catch (ServerException $e)
        {
            Log::error($e);
            Log::error('Request Data: '.print_r($requestData, true));

            throw new Exception($e);
        }
        catch (ClientException|RequestException $e)
        {
            return [
                'data' => json_decode($e->getResponse()->getBody()->getContents(), true),
                'code' => $e->getCode(),
            ];
        }
        catch (Exception|\Throwable $e)
        {
            if (!$e->getMessage() || !$e->getFile() || !$e->getLine())
                throw new Exception($e);

            return [
                'errorFile'    => $e->getFile(),
                'errorMessage' => $e->getMessage(),
                'lineError'    => $e->getLine(),
            ];
        }
    }
}
