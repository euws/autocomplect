<?php

namespace Application\Service;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class RequestService
{
    const API_URL = 'http://86.57.152.180:5515/1C_UT_Test/hs/sapi/';
    const API_USER = 'obmen';
    const API_PASSWORD = 'obmen@p!5';

    protected $client;

    public function __construct(ClientInterface $client)
    {
       $this->client = $client;
    }

    public function makeRequest(array $query, string $body): ResponseInterface
    {
        $parameters = [
            'auth' => [self::API_USER, self::API_PASSWORD],
            'Content-type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'query' => $query,
            'body' => $body
        ];

        $response = $this->client->request('POST', self::API_URL, $parameters);

        return $response;
    }
}