<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\IndexBreweryRequest;
use App\Http\Resources\V1\BreweryResource;
use App\Traits\ApiJsonResponsesTrait;
use App\Traits\GuzzleGetTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class BreweryController extends Controller
{
    use ApiJsonResponsesTrait, GuzzleGetTrait;

    public function index(IndexBreweryRequest $request)
    {
        $request->validated();

        $response = $this->get(config('services.api_open_brewery_db.url'), [
            'per_page' => $request->input('per_page', 10),
            'page' => $request->input('page', 1)
        ]);

        if(!$response->successful())
            return $this->error('Failed.', $response->status());

        $responseMeta = $this->get(config('services.api_open_brewery_db.url') . '/meta');

        if(!$responseMeta->successful())
            return $this->error('Failed.', $responseMeta->status());

        $results = new LengthAwarePaginator(
            $response->json(),
            $responseMeta->json()['total'],
            $request->input('per_page', 10),
            $request->input('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return BreweryResource::collection($results);
    }
}
