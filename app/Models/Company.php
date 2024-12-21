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
        return Attribute::get(function ($value, array $attributes) {
            $attributes['logo_disk'] ??= null;
            $attributes['logo_path'] ??= null;

            if ($attributes['logo_disk'] && $attributes['logo_path']) {
                return Storage::disk($attributes['logo_disk'])->url($attributes['logo_path']);
            }

            return 'https://ui-avatars.com/api/?background=random&name='.urlencode($attributes['name']);
        });
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
