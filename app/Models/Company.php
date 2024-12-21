<?php

namespace App\Models;

use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'logo_disk',
        'logo_path',
        'website',
    ];

    protected $appends = [
        'logo_url',
    ];

    protected function logoUrl(): Attribute
    {
        return Attribute::get(
            fn ($value, array $attributes) => Storage::disk($attributes['logo_disk'])->url($attributes['logo_path'])
        );
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
