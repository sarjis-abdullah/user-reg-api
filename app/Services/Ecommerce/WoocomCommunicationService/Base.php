<?php

namespace App\Services\Ecommerce\WoocomCommunicationService;

use App\Models\AppSetting;
use App\Models\EcomIntegration;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use function config;

abstract class Base
{
    /**
     * @var Client
     */
    public static $httpClient;

    /**
     * Base constructor.
     */
    public function setClient()
    {
        self::$httpClient = new Client([
            'headers' => [
                'Content-Type'  => 'application/json',
                'Accept'  => 'application/json'
            ]
        ]);
    }

    /**
     * @param $url
     * @param $data
     * @return int|void
     * @throws GuzzleException
     */
    public function checkApiStatus($url, $data)
    {
        try {
            self::setClient();
            $response = self::$httpClient->get($url, [
                'auth' => [$data['consumer_key'], $data['consumer_secret']],
            ]);

            // Check the response status code. 200 means the API is accessible.
            return $response->getStatusCode();
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                // If an error response is available, get the status code from it.
                return $e->getResponse()->getStatusCode();
            } else {
                // If no response is available, return null or an appropriate error code.
                return 500;
            }
        }
    }

    /**
     * send http request to CertificateValidation Microservice API
     *
     * @param string $method
     * @param string $uri
     * @param array $data
     * @return mixed
     * @throws
     */
    public function requestToAPI(string $method, $uri, array $data = [])
    {
        $ecomIntegration = EcomIntegration::where('name', '=', EcomIntegration::NAME_WOOCOMMERCE)->first();

        if($ecomIntegration instanceof EcomIntegration ) {
            $url = $ecomIntegration->apiUrl . $uri;
            $consumer_key = $ecomIntegration->apiKey;
            $consumer_secret = $ecomIntegration->apiSecret;
        } else {
            $url = config('app.ecom_api_url') . $uri;
            $consumer_key = config('app.ecom_api_key');
            $consumer_secret = config('app.ecom_api_secret');
        }

        try {
            self::setClient();
            $response = self::$httpClient->request($method, $url, [
                'auth' => [$consumer_key, $consumer_secret],
                'json' => $data
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (RequestException $e) {
            return json_decode($e->getResponse()->getBody()->getContents());
        }
    }
}
