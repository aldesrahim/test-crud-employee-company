<?php

namespace App\Models;

use Database\Factories\EmployeeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    /** @use HasFactory<EmployeeFactory> */
    use HasFactory;

    protected $fillable = [
        'company_id',
        'first_name',
        'last_name',
        'full_name',
        'email',
        'phone',
        'phone_country',
        'phone_normalized',
        'phone_national',
    ];

    protected static function booted(): void
    {
        static::saving(function (Employee $employee) {
            if ($employee->isDirty('phone')) {
                $employee->phone_country = null;
                $employee->phone_normalized = null;
                $employee->phone_national = null;

                if ($employee->phone) {
                    $phone = phone($employee->phone);
                    $employee->phone_country = $phone->getCountry();
                    $employee->phone_normalized = preg_replace('/[^0-9]/', '', $employee->phone);
                    $employee->phone_national = preg_replace('/[^0-9]/', '', $phone->formatNational());
                }
            }

            if ($employee->isDirty(['first_name', 'last_name'])) {
                $employee->full_name = sprintf(
                    '%s %s',
                    $employee->first_name,
                    $employee->last_name,
                );
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
