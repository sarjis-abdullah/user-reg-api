<?php


namespace App\Services\Ecommerce;

interface WoocomCommunication
{
    /**
     * check url status
     * @param string $url
     * @param array $data
     */
    public function checkStatus(string $url, array $data = []);

    /**
     * get all the routes resources
     * @param string $route
     * @param array $options
     */
    public function index(string $route, array $options = []);

    /**
     * save a ticket resource
     *
     * @param string $route
     * @param array $data
     */
    public function store(string $route, array $data = []);

    /**
     * find a ticket resource by id
     *
     * @param string $route
     * @param mixed $id
     */
    public function show(string $route, $id);

    /**
     * update a ticket resource
     *
     * @param string $route
     * @param array $data
     */
    public function update(string $route, array $data);

    /**
     * delete a ticket resource by id
     *
     * @param string $route
     * @param $id
     */
    public function delete(string $route, $id);
}
