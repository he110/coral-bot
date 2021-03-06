<?php
/**
 * Created by PhpStorm.
 * User: he110
 * Date: 2020-01-21
 * Time: 15:03
 */

namespace He110\Coral\Bot\Service;


use GuzzleHttp\Client;

class CoralRestClient
{
    public static function get(string $country, string $base, string $endpoint): ?array
    {
        try {
            $country = strtolower($country);
            $url = "https://{$country}.{$base}/restApi/{$endpoint}";
            return static::request($url);
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function post(string $country, string $base, string $endpoint, array $data): ?array
    {
        $country = strtolower($country);
        $url = "https://{$country}.{$base}/restApi/{$endpoint}";
        return static::request($url, $data, 'POST');
    }

    private static function request(string $url, array $data = array(), string $method = 'GET'): ?array
    {
        $client = new Client();
        if (stripos($url, '?') === false)
            $url = $url."?platform=tg";
        else
            $url = $url."&platform=tg";

        $response = $client->request($method, $url);
        if ($response->getStatusCode() < 300)
            return json_decode($response->getBody()->getContents(), true);
        return null;
    }
}