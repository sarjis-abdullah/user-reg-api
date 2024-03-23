<?php

namespace App\Services\Ecommerce\WoocomCommunicationService;

use App\Services\Ecommerce\WoocomCommunication;
use GuzzleHttp\Exception\GuzzleException;

class WoocomCommunicationService extends Base implements WoocomCommunication
{
    /**
     * check url status
     * @param string $url
     * @param array $data
     * @return int|void
     * @throws GuzzleException
     */
    public function checkStatus(string $url, array $data = [])
    {
        return $this->checkApiStatus($url, $data);
    }

    /**
     * @inheritDoc
     */
    public function index(string $route, array $options = [])
    {
        return $this->requestToAPI('GET', $route, $options);
    }

    /**
     * @inheritDoc
     */
    public function store(string $route, array $data = [])
    {
        return $this->requestToAPI('POST', $route,  $data);
    }

    /**
     * @inheritDoc
     */
    public function show(string $route, $id)
    {
        return $this->requestToAPI('GET', $route.$id);
    }

    /**
     * @inheritDoc
     */
    public function update(string $route, array $data)
    {
        return $this->requestToAPI('PUT',  $route,  $data);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $route, $id)
    {
        return $this->requestToAPI('DELETE', $route.$id);
    }
}
