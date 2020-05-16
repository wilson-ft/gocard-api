<?php

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

function sendAPI($method, $url, $postData)
{
    $client = new Client();

    try {
        $res = $client->request($method, $url, $postData);
        return [
            'status_code' => $res->getStatusCode(),
            'body'        => json_decode((string)$res->getBody())
        ];
    } catch (\Exception $e) {
        $response = $e->getResponse();

        if ($response === null) {
            return [
                'status_code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }

        return [
            'status_code' => $response->getStatusCode(),
            'body'        => json_decode((string)$response->getBody())
        ];
    }
}

function callMambu($config)
{
    $mambuUrl   = env('MAMBU_URL', '');
    $url        = isset($config['url']) ? $config['url'] : '' ;
    $method     = isset($config['method']) ? $config['method'] : 'GET' ;
    $json       = isset($config['json']) ? $config['json'] : null;

    $username   = env('MAMBU_USERNAME', '');
    $password   = env('MAMBU_PASSWORD', '');

    if($url === ''){
        return [
            'status_code'   => 500,
            'body'          => 'URL/DATA is missing'
        ];
    }

    if($mambuUrl === ''){
        return [
            'status_code'   => 500,
            'body'          => 'COMMS_URL is missing'
        ];
    }

    if($username === '' || $password === ''){
        return [
            'status_code'   => 500,
            'body'          => 'MAMBU credentials is missing'
        ];
    }

    $postData = [
        'json'      => $json,
        'headers'   => [
                        'Content-Type'  => 'application/json',
                        'Cache-Control' => 'no-cache',
                        'Accept'        => 'application/json'
                    ],
        'auth'      => [
                        $username,
                        $password
                    ]
    ];

    return sendAPI($method, $mambuUrl.$url, $postData);
}
