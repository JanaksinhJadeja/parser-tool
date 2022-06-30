<?php

declare(strict_types=1);

namespace App\Util;

use App\Converter\Exception\FileNotFoundOnUrlException;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Primary purpose of Util class to provide general utility methods.
 */
class Util
{
    /**
     * Make multi dimension array to key => value format.
     *
     * @param array $array
     * @param string $prefix
     * @return array
     */
    public static function makeArrayFlat(array $array, string $prefix=''): array
    {
        $result = array();
        foreach ($array as $key => $value) {
            $newKey = $prefix . (empty($prefix) ? '' : '.') . $key;
            if (is_array($value) && count($value)) {
                $result = array_merge($result, self::makeArrayFlat($value, $newKey));
            } elseif (is_array($value) && count($value) == 0) {
                $result[$newKey] = '';
            } else {
                $result[$newKey] = $value;
            }
        }
        return $result;
    }

    /**
     * Write data from URL.
     *
     * @param $url
     * @return string
     * @throws TransportExceptionInterface
     */
    public static function writeDataFromURL($url): string
    {
        $client = new CurlHttpClient();
        $response = $client->request('GET', $url);
        if (200 !== $response->getStatusCode()) {
            throw new FileNotFoundOnUrlException();
        }

        $random = substr(base64_encode($url), 1, 5);
        $fileName = '/var/www/data/temp/'.$random.'.xml';

        $fileHandler = fopen($fileName, 'w');
        foreach ($client->stream($response) as $chunk) {
            fwrite($fileHandler, $chunk->getContent());
        }

        return $fileName;
    }
}
