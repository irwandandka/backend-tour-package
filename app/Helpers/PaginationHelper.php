<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class PaginationHelper
{
    /**
     * Paginate a collection manually.
     *
     * @param  \Illuminate\Support\Collection|array  $items
     * @param  int  $perPage
     * @param  int|null  $page
     * @param  array  $options
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public static function paginate($items, int $perPage = 10, int $page = null, array $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : collect($items);

        $results = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $results,
            $items->count(),
            $perPage,
            $page,
            $options
        );
    }
}
