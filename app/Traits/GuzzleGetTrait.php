<?php

namespace App\Traits;

use Illuminate\Http\Client\Response;
use Http;

trait GuzzleGetTrait
{
    public function get(string $url, array $parameters = []): Response
    {
        return Http::get($url, $parameters);
    }
}
