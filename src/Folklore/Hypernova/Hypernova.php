<?php

namespace Folklore\Hypernova;

use GuzzleHttp\Client as HttpClient;

class Hypernova
{
    protected $host = 'locahost';
    protected $port = 3030;
    protected $client;

    public function __construct($config = [])
    {
        if (isset($config['host'])) {
            $this->host = $config['host'];
        }

        if (isset($config['port'])) {
            $this->port = $config['port'];
        }

        $this->client = $this->createClient();
    }

    protected function createClient()
    {
        $uri = 'http://'.$this->host.':'.$this->port;
        $client = new HttpClient([
            'base_uri' => $uri,
            'timeout'  => 2
        ]);

        return $client;
    }

    public function render($component, $data = [])
    {
        $name = 'component';

        $jobs = [];
        $jobs[$name] = [
            'name' => $component,
            'data' => $data
        ];

        $html = $this->renderBatch($jobs);

        return isset($html[$name]) ? $html[$name]:'';
    }

    public function renderBatch($jobs)
    {
        $response = $this->request($jobs);

        $html = [];

        if (!isset($response['results'])) {
            return $html;
        }

        foreach ($response['results'] as $key => $result) {
            $html[$key] = $result['html'];
        }

        return $html;
    }

    protected function request($jobs)
    {
        try {
            $response = $this->client->request('POST', '/batch', [
                'json' => $jobs
            ]);

            $body = json_decode($response->getBody(), true);

            return $body;
        } catch (\Exception $e) {
            return null;
        }
    }
}
