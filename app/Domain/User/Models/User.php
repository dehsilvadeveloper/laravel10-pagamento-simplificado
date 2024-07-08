<?php

namespace App\Domain\User\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Database\Factories\UserFactory;
use App\Domain\DocumentType\Models\DocumentType;

class User extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $dateFormat = 'Y-m-d H:i:s';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_type_id',
        'name',
        'document_type_id',
        'document_number',
        'email',
        'password'
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }

    public function userType(): BelongsTo
    {
        return $this->belongsTo(UserType::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }
}