<?php

namespace App\Platforms;

use App\Platforms\Clients\Client;
use App\Platforms\Clients\FakeWP;

class FakeClientsProvider implements ClientsProvider
{
    protected $clients = [];

    public function __construct()
    {
        $this->clients = [
            'WP_FAKE' => app()->make(FakeWP::class),
        ];
    }

    public function getClientKeyByClass(Client $client): string
    {
        return array_search($client, $this->clients);
    }

    public function getClientInstanceByKey(string $key): Client
    {
        return $this->clients[$key];
    }

    /**
     * @return array
     */
    public function getClients(): array
    {
        return $this->clients;
    }
}