<?php

namespace App\Domain\Transfer\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domain\Transfer\Models\TransferStatus;
use App\Domain\TransferAuthorization\Models\TransferAuthorizationResponse;
use App\Domain\User\Models\User;
use Database\Factories\TransferFactory;

class Transfer extends Model
{
    use HasFactory;

    protected $table = 'transfers';
    protected $primaryKey = 'id';
    protected $dateFormat = 'Y-m-d H:i:s';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payer_id',
        'payee_id',
        'amount',
        'transfer_status_id',
        'authorized_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'authorized_at' => 'datetime'
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return TransferFactory::new();
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function payee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payee_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TransferStatus::class, 'transfer_status_id');
    }

    public function authorizationResponses(): HasMany
    {
        return $this->hasMany(TransferAuthorizationResponse::class, 'transfer_authorization_response_id');
    }
}
