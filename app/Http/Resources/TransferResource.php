<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payer' => new PayerResource($this->payer),
            'payee' => new PayeeResource($this->payee),
            'amount' => $this->amount,
            'status' => new TransferStatusResource($this->status),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'authorized_at' => $this->authorized_at?->format('Y-m-d H:i:s')
        ];
    }
}
