<?php

namespace App\Domain\DocumentType\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Database\Factories\DocumentTypeFactory;
use App\Domain\User\Models\User;

class DocumentType extends Model
{
    use HasFactory;

    protected $table = 'document_types';
    protected $primaryKey = 'id';
    public $timestamps = true;

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
        return DocumentTypeFactory::new();
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
