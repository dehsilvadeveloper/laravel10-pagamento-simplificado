<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'user_type' => new UserTypeResource($this->userType),
            'document_type' => new DocumentTypeResource($this->documentType),
            'document_number' => $this->document_number,
            'email' => $this->email,
            'wallet' => new WalletResource($this->wallet),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->when(!empty($this->deleted_at), function() {
                return $this->deleted_at->format('Y-m-d H:i:s');
            })
        ];
    }
}
