<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaginateResource extends JsonResource
{
    protected $resourceClass;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @param  string  $resourceClass
     * @return void
     */
    public function __construct($resource, $resourceClass)
    {
        parent::__construct($resource);
        $this->resourceClass = $resourceClass;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->resourceClass::collection($this->items()),
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'per_page' => $this->perPage(),
            'total' => $this->total(),
            'from' => $this->firstItem(),
            'to' => $this->lastItem(),
        ];
    }
}
