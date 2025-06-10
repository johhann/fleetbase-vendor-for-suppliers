<?php

namespace Fleetbase\VendorsForSuppliers\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorsForSupplierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'status' => $this->status,
            'qr_code_url' => $this->qr_code_url,
            'qr_code_data' => $this->qr_code_data,
            'scan_url' => url("/vendor-scan/{$this->uuid}"),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
