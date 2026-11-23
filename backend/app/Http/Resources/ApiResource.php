<?php

namespace App\Http\Resources;

use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Context;

class ApiResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'request_id' => Context::get('request_id')],
        ];
    }

    protected function formatDate(?CarbonInterface $date): ?string
    {
        if (! $date) {
            return null;
        }

        return $date->copy()->utc()->format('Y-m-d\TH:i:s.v\Z');
    }
}
