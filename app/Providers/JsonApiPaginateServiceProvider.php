<?php

namespace App\Providers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Query\Builder as BaseBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class JsonApiPaginateServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerMacro();
    }

    protected function registerMacro(): void
    {
        // extracted from spatie json-api-paginate
        $macro = function (?int $maxResults = null, ?int $defaultSize = null, ?int $totalResults = null) {
            $maxResults = $maxResults ?? config('json-api-paginate.max_results');
            $defaultSize = $defaultSize ?? config('json-api-paginate.default_size');
            $numberParameter = config('json-api-paginate.number_parameter');
            $sizeParameter = config('json-api-paginate.size_parameter');
            $paginationParameter = config('json-api-paginate.pagination_parameter');
            $paginationMethod = config('json-api-paginate.use_simple_pagination') ? 'simplePaginate' : 'paginate';

            $size = (int)request()->input($paginationParameter . '.' . $sizeParameter, $defaultSize);

            if ($size <= 0) {
                $size = $defaultSize;
            }

            if ($size > $maxResults) {
                $size = $maxResults;
            }

            /** @var LengthAwarePaginator|Paginator $paginator */
            $paginator = $this->{$paginationMethod}($size, ['*'], $paginationParameter . '.' . $numberParameter, null, $totalResults);

            $paginator
                ->setPageName($paginationParameter . '[' . $numberParameter . ']')
                ->appends(Arr::except(request()->input(), $paginationParameter . '.' . $numberParameter));

            if (!is_null(config('json-api-paginate.base_url'))) {
                $paginator->setPath(config('json-api-paginate.base_url'));
            }

            return $paginator;
        };

        EloquentBuilder::macro('jsonPaginate', $macro);
        BaseBuilder::macro('jsonPaginate', $macro);
        BelongsToMany::macro('jsonPaginate', $macro);
        HasManyThrough::macro('jsonPaginate', $macro);
    }
}
