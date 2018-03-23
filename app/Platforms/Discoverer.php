<?php

namespace App\Platforms;


use App\Platforms\Clients\Client;
use App\Platforms\Exceptions\BlogNameNotFoundException;
use App\Platforms\Exceptions\UnknownPlatformException;

class Discoverer
{
    protected $clients;

    public function __construct()
    {
        /** @var ProductionClientsProvider clients */
        $this->clients = resolve(ClientsProvider::class)->getClients();
    }

    public function discoverClientForBlog($blog): Client
    {
        foreach ($this->clients as $client) {

            try {
                /** @var Client $client */
                $client->findBlogName($blog);

                return $client;

            } catch (BlogNameNotFoundException $exception) {
                // continue...
            }
        }

        throw new UnknownPlatformException();
    }
}