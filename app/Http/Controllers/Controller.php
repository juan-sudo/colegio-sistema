<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function perPage(Request $request, int $default = 20): int
    {
        $allowed = [10, 20, 50, 100];
        $requested = (int) $request->input('per_page', $default);

        return in_array($requested, $allowed, true) ? $requested : $default;
    }

    /**
     * Apply a whitelisted column sort from `sort_by`/`sort_dir` request params,
     * falling back to the given default ordering when none is requested.
     */
    protected function applySort(Builder $query, Request $request, array $allowed, ?string $defaultField = null, string $defaultDir = 'asc'): Builder
    {
        $field = $request->input('sort_by');
        $dir = $request->input('sort_dir') === 'desc' ? 'desc' : 'asc';

        if ($field && in_array($field, $allowed, true)) {
            return $query->orderBy($field, $dir);
        }

        if ($defaultField) {
            return $query->orderBy($defaultField, $defaultDir);
        }

        return $query;
    }
}
