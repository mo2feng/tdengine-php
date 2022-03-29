<?php

/*
 * This file is part of the mofengme/tdengine.
 *
 * (c) mofeng <maxadjsky@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mofengme\Tdengine;

use Symfony\Component\HttpClient\HttpClient;

class TdEngine
{
    protected string $host = '127.0.0.1';
    protected string $port = '6041';
    protected string $user = 'root';
    protected string $password = 'taosdata';
    protected string $database = 'log';

    public const FETCH_SUCCESS = 'succ';

    protected array $httpOptions = [
    ];

    public function __construct($database, $host = '127.0.0.1', $port = '6041', $user = 'root', $password = 'taosdata')
    {
        $this->host     = $host;
        $this->port     = $port;
        $this->user     = $user;
        $this->password = $password;
        $this->database = $database;
    }

    public function getHttpClient(): \Symfony\Contracts\HttpClient\HttpClientInterface
    {
        return HttpClient::createForBaseUri($this->buildUri(), $this->buildHttpOptions());
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Mofengme\Tdengine\TdEngineException
     */
    public function raw_query($client, string $sql): array
    {
        $url          = match (empty($this->database)) {
            true => '/rest/sql',
            false => '/rest/sql/' . $this->database,
        };
        $response     = $client->request('POST', $url, [
            'body' => $sql,
        ]);
        $jsonResponse = json_decode($response->getContent());
        if (self::FETCH_SUCCESS == $jsonResponse?->status) {
            return $this->buildResponseData($jsonResponse);
        } else {
            throw new TdEngineException($jsonResponse?->desc, $jsonResponse?->code);
        }
    }

    protected function buildUri(): string
    {
        return 'http://' . $this->host . ':' . $this->port;
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface|
     * @throws \Mofengme\Tdengine\TdEngineException
     */
    public function execute($sql): array
    {
        return $this->raw_query($this->getHttpClient(), $sql);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Mofengme\Tdengine\TdEngineException
     */
    public function query($sql): array
    {
        return $this->raw_query($this->getHttpClient(), $sql);
    }

    protected function buildHttpOptions(): array
    {
        $auth = [
            'auth_basic' => [
                $this->user,
                $this->password,
            ],
        ];

        return array_merge($this->httpOptions, $auth);
    }

    private function buildResponseData(mixed $jsonResponse): array
    {
        $head   = $jsonResponse->head;
        $data   = $jsonResponse->data;
        $result = [];
        foreach ($data as $key => $value) {
            foreach ($value as $k => $v) {
                $result[$key][$head[$k]] = $v;
            }
        }

        return $result;
    }
}
