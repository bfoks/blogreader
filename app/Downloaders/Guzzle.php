<?php

namespace App\Downloaders;

use GuzzleHttp\Client;

class Guzzle
{
    protected $connectionConfig = [
        // about timeout: http://stackoverflow.com/questions/20847633/limit-connecting-time-with-guzzle-http-php-client

        'allow_redirects' => [
            'max' => 5,
        ],
        'connect_timeout' => 15,

        /*  TODO
            w oparciu o debugMode (z configa stacji)
            przekazanego w downloadParams mozliwe byloby
            zapisywanie debugu pobierania do przekazanego uchwytu
        */
        // 'debug' => true,

        // about avoiding downloading cached files: http://stackoverflow.com/questions/49547/making-sure-a-web-page-is-not-cached-across-all-browsers
        'headers' => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 5.1; rv:46.0) Gecko/20100101 Firefox/46.0',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ],

        /* TODO
            Sprawdzajac rozmiar odpowiedzi w nagłówku (Content-Length),
            zabezpieczyć się przed pobieraniem zbyt dużych plików.
        */
        // 'on_headers' =>

        /*  TODO
            Używanie zewnętrznego proxy, aby zdradzać rzeczywistego IP serwera
        */
        // 'proxy' =>

        'verify' => false,
        'timeout' => 15
    ];

    /**
     * @param $method
     * @param $url
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function download($method, $url)
    {
        $client = new Client();

        try {
            return $client->request($method, $url, $this->connectionConfig);
        } catch (\Exception $exception) {
            throw $exception;
        }

    }

    public function downloadBody($method, $url)
    {
        return (string)$this->download($method, $url)->getBody();
    }

    public function downloadHeader($method, $url, $headerName)
    {
        return $this->download($method, $url)->getHeader($headerName);
    }

}