<?php

namespace App\Domain\Transfer\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Database\Factories\TransferStatusFactory;

class TransferStatus extends Model
{
    use HasFactory;

    protected $table = 'transfer_statuses';
    protected $primaryKey = 'id';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return TransferStatusFactory::new();
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'transfer_status_id');
    }
}
