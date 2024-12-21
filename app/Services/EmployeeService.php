<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class EmployeeService
{
    public function get()
    {
        return QueryBuilder::for(Employee::class)
            ->select([
                'employees.*',
                'companies.name as company_name',
            ])
            ->with('company')
            ->join('companies', 'companies.id', 'employees.company_id')
            ->allowedFilters($columns = [
                'full_name',
                'email',
                'company_name',
                'phone',
            ])
            ->allowedSorts($columns)
            ->defaultSorts('-employees.created_at')
            ->orderBy('employees.id')
            ->jsonPaginate();
    }

    public function findById(int $id): Employee
    {
        return Employee::query()
            ->select([
                'employees.*',
                'companies.name as company_name',
            ])
            ->with('company')
            ->join('companies', 'companies.id', 'employees.company_id')
            ->findOrFail($id);
    }

    public function create(array $validated): Employee
    {
        return DB::transaction(fn () => Employee::create($validated));
    }

    public function update(Employee|string $id, array $validated): Employee
    {
        $employee = $id instanceof Employee ? $id : $this->findById($id);

        return tap($employee, fn (Employee $employee) => DB::transaction(fn () => $employee->update($validated)));
    }

    public function delete(Employee|string $id): void
    {
        $employee = $id instanceof Employee ? $id : $this->findById($id);

        DB::transaction(function () use ($employee) {
            $employee->delete();
        });
    }
}
