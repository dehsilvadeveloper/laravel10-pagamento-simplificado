<?php

namespace App\Domain\TransferAuthorization\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\Transfer\Models\Transfer;
use Database\Factories\TransferAuthorizationResponseFactory;

class TransferAuthorizationResponse extends Model
{
    use HasFactory;

    protected $table = 'transfer_authorization_responses';
    protected $primaryKey = 'id';
    protected $dateFormat = 'Y-m-d H:i:s';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transfer_id',
        'response'
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return TransferAuthorizationResponseFactory::new();
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class, 'transfer_authorization_response_id');
    }
}
