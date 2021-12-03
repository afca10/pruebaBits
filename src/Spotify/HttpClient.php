<?php

declare(strict_types=1);

namespace App\Spotify;

use Symfony\Component\HttpClient\HttpClient as BaseClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Extend this class to use Symfony's Http Client
 */
abstract class HttpClient
{
    /**
     * @var HttpClientInterface
     */
    protected $client;

    public function __construct()
    {
        $this->client = BaseClient::create();
    }

    /**
     * @return HttpClientInterface
     */
    public function getClient(): HttpClientInterface
    {
        return $this->client;
    }
}