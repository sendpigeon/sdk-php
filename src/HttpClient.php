<?php

declare(strict_types=1);

namespace SendPigeon;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use SendPigeon\Exceptions\SendPigeonException;

class HttpClient
{
    private Client $client;
    private string $apiKey;
    private int $maxRetries;

    private const DEFAULT_BASE_URL = 'https://api.sendpigeon.dev';
    private const DEFAULT_TIMEOUT = 30;
    private const DEFAULT_MAX_RETRIES = 2;
    private const MAX_RETRIES = 5;

    public function __construct(
        string $apiKey,
        ?string $baseUrl = null,
        ?int $timeout = null,
        ?int $maxRetries = null,
    ) {
        $this->apiKey = $apiKey;
        $this->maxRetries = min($maxRetries ?? self::DEFAULT_MAX_RETRIES, self::MAX_RETRIES);

        $this->client = new Client([
            'base_uri' => $baseUrl ?? self::DEFAULT_BASE_URL,
            'timeout' => $timeout ?? self::DEFAULT_TIMEOUT,
            'http_errors' => false,
        ]);
    }

    /**
     * @throws SendPigeonException
     */
    public function request(string $method, string $path, ?array $body = null, array $headers = []): array
    {
        $options = [
            RequestOptions::HEADERS => array_merge([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'User-Agent' => 'sendpigeon-php/1.0.0',
            ], $headers),
        ];

        if ($body !== null) {
            $options[RequestOptions::JSON] = $body;
        }

        $lastException = null;

        for ($attempt = 0; $attempt <= $this->maxRetries; $attempt++) {
            try {
                $response = $this->client->request($method, $path, $options);
                $statusCode = $response->getStatusCode();
                $responseBody = $response->getBody()->getContents();
                $data = json_decode($responseBody, true) ?? [];

                if ($statusCode >= 200 && $statusCode < 300) {
                    return $data;
                }

                $errorMessage = $data['error']['message'] ?? "HTTP {$statusCode}";
                $errorCode = $data['error']['code'] ?? null;

                $lastException = SendPigeonException::api($statusCode, $errorCode, $errorMessage);

                // Retry on 429 or 5xx
                if ($statusCode === 429 || $statusCode >= 500) {
                    if ($attempt < $this->maxRetries) {
                        $retryAfter = $this->parseRetryAfter($response->getHeader('Retry-After')[0] ?? null);
                        $this->sleep($attempt, $retryAfter);
                        continue;
                    }
                }

                throw $lastException;

            } catch (ConnectException $e) {
                $lastException = SendPigeonException::network($e->getMessage());
                if ($attempt < $this->maxRetries) {
                    $this->sleep($attempt, null);
                    continue;
                }
                throw $lastException;
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $statusCode = $response->getStatusCode();
                    $responseBody = $response->getBody()->getContents();
                    $data = json_decode($responseBody, true) ?? [];

                    $errorMessage = $data['error']['message'] ?? $e->getMessage();
                    $errorCode = $data['error']['code'] ?? null;

                    throw SendPigeonException::api($statusCode, $errorCode, $errorMessage);
                }
                throw SendPigeonException::network($e->getMessage());
            }
        }

        throw $lastException ?? SendPigeonException::network('Request failed');
    }

    public function get(string $path, array $headers = []): array
    {
        return $this->request('GET', $path, null, $headers);
    }

    public function post(string $path, ?array $body = null, array $headers = []): array
    {
        return $this->request('POST', $path, $body, $headers);
    }

    public function patch(string $path, ?array $body = null, array $headers = []): array
    {
        return $this->request('PATCH', $path, $body, $headers);
    }

    public function delete(string $path, array $headers = []): array
    {
        return $this->request('DELETE', $path, null, $headers);
    }

    private function sleep(int $attempt, ?int $retryAfter): void
    {
        if ($retryAfter !== null && $retryAfter > 0) {
            sleep($retryAfter);
            return;
        }

        $backoff = (int) pow(2, $attempt) * 100_000; // microseconds
        $maxBackoff = 10_000_000; // 10 seconds
        usleep(min($backoff, $maxBackoff));
    }

    private function parseRetryAfter(?string $value): ?int
    {
        if ($value === null) {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return null;
    }
}
