<?php

declare(strict_types=1);

namespace Solcik\Tester;

use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use function Laminas\Diactoros\marshalHeadersFromSapi;
use function Laminas\Diactoros\normalizeServer;
use function Laminas\Diactoros\normalizeUploadedFiles;

final class RequestFactory
{
    private string $baseUri = '';

    public function __construct(string $baseUri)
    {
        $this->baseUri = $baseUri;
    }

    public function get(string $uri, array $query = [], array $headers = []): ServerRequestInterface
    {
        return $this->prepareRequest('GET', $uri, $query, [], [], [], $headers);
    }

    public function post(string $uri, array $body = [], array $headers = []): ServerRequestInterface
    {
        return $this->prepareRequest('POST', $uri, [], $body, [], [], $headers);
    }

    public function put(string $uri, array $body = [], array $headers = []): ServerRequestInterface
    {
        return $this->prepareRequest('PUT', $uri, [], $body, [], [], $headers);
    }

    public function patch(string $uri, array $body = [], array $headers = []): ServerRequestInterface
    {
        return $this->prepareRequest('PATCH', $uri, [], $body, [], [], $headers);
    }

    public function delete(string $uri, array $body = [], array $headers = []): ServerRequestInterface
    {
        return $this->prepareRequest('DELETE', $uri, [], $body, [], [], $headers);
    }

    public function json(string $method, string $uri, array $body = [], array $headers = []): ServerRequestInterface
    {
        $content = json_encode($body);

        $headers = array_merge([
            'Content-Length' => mb_strlen($content, '8bit'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ], $headers);

        return $this->prepareRequest($method, $uri, [], $content, [], [], $headers);
    }

    public function withAuth(RequestInterface $request, string $value): RequestInterface
    {
        return $request->withHeader('Authorization', $value);
    }

    public function withBearerToken(RequestInterface $request, string $token): RequestInterface
    {
        return $this->withAuth($request, "Bearer ${token}");
    }

    public function prepareRequest(
        string $method,
        string $uri,
        array $query = [],
        array $body = [],
        array $cookies = [],
        array $files = [],
        array $headers = [],
        array $server = []
    ): ServerRequestInterface {
        $serverFull = normalizeServer($server);
        $files = normalizeUploadedFiles($files);
        $headersFinal = array_merge($headers, marshalHeadersFromSapi($server));

        $queryString = http_build_query($query, '', '&', PHP_QUERY_RFC3986);

        $uriFinal = (new Uri($this->baseUri))
            ->withPath($uri)
            ->withQuery($queryString)
            ->withPort(80);

        return new ServerRequest(
            $serverFull,
            $files,
            $uriFinal,
            $method,
            'php://input',
            $headersFinal,
            $cookies,
            $query,
            $body
        );
    }
}
