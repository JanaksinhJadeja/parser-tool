<?php

declare(strict_types=1);

namespace App\Services;

use App\Exception\FileNotFoundException;
use App\Exception\FileNotFoundOnUrlException;
use Symfony\Component\HttpClient\CurlHttpClient;


/**
 * Primary purpose of Class is to handle file.
 */
class FileService
{
    public function getFile(string $source, string $path): string
    {
        return match ($source) {
            'local' => $this->getLocalFile($path),
            'remote' => $this->getRemoteFile($path),
            default => throw new \RuntimeException('Not valid type'),
        };
    }

    private function getRemoteFile(string $path): string
    {
        $client = new CurlHttpClient();
        $response = $client->request('GET', $path);
        if (200 !== $response->getStatusCode()) {
            throw new FileNotFoundOnUrlException();
        }

        $random = substr(base64_encode($path), 1, 5);
        $fileName = '/var/www/data/temp/'.$random.'.xml';

        $fileHandler = fopen($fileName, 'w');
        foreach ($client->stream($response) as $chunk) {
            fwrite($fileHandler, $chunk->getContent());
        }

        return $fileName;
    }

    private function getLocalFile(string $path): string
    {
        if (file_exists($path)) {
            return $path;
        } else {
            throw new FileNotFoundException();
        }
    }
}
