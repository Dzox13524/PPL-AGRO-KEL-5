<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BackendApi
{
    public static function baseUrl(): string
    {
        return rtrim((string) config('services.backend.url', 'http://127.0.0.1:8000/api/v1'), '/');
    }

    public static function url(string $path = ''): string
    {
        $trimmed = ltrim($path, '/');

        return $trimmed === '' ? self::baseUrl() : self::baseUrl() . '/' . $trimmed;
    }

    public static function resolveToken(?Request $request = null, ?string $token = null): ?string
    {
        if (is_string($token) && trim($token) !== '') {
            return trim($token);
        }

        $req = $request ?? request();
        if (!$req instanceof Request) {
            return null;
        }

        $cookieOrSessionToken = $req->cookie('hidara_token')
            ?: ($req->hasSession() ? $req->session()->get('jwt_token') : null);

        return is_string($cookieOrSessionToken) && trim($cookieOrSessionToken) !== ''
            ? trim($cookieOrSessionToken)
            : null;
    }

    public static function client(?Request $request = null, ?string $token = null, int $timeout = 10): PendingRequest
    {
        $client = Http::baseUrl(self::baseUrl())
            ->timeout($timeout)
            ->acceptJson()
            ->asJson();

        $resolvedToken = self::resolveToken($request, $token);
        if ($resolvedToken !== null) {
            $client = $client->withToken($resolvedToken);
        }

        return $client;
    }

    public static function get(string $path, array $query = [], ?Request $request = null, ?string $token = null, int $timeout = 10): Response
    {
        return self::client($request, $token, $timeout)->get(ltrim($path, '/'), $query);
    }

    public static function post(string $path, array $data = [], ?Request $request = null, ?string $token = null, int $timeout = 10): Response
    {
        return self::client($request, $token, $timeout)->post(ltrim($path, '/'), $data);
    }

    public static function put(string $path, array $data = [], ?Request $request = null, ?string $token = null, int $timeout = 10): Response
    {
        return self::client($request, $token, $timeout)->put(ltrim($path, '/'), $data);
    }

    public static function patch(string $path, array $data = [], ?Request $request = null, ?string $token = null, int $timeout = 10): Response
    {
        return self::client($request, $token, $timeout)->patch(ltrim($path, '/'), $data);
    }

    public static function delete(string $path, array $data = [], ?Request $request = null, ?string $token = null, int $timeout = 10): Response
    {
        return self::client($request, $token, $timeout)->delete(ltrim($path, '/'), $data);
    }
}
