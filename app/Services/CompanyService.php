<?php

namespace App\Services;

use App\Models\Company;
use Spatie\QueryBuilder\QueryBuilder;

class CompanyService
{
    public function get()
    {
        return QueryBuilder::for(Company::class)
            ->allowedFilters($columns = [
                'name',
                'email',
                'website',
            ])
            ->allowedSorts($columns)
            ->jsonPaginate();
    }
}
