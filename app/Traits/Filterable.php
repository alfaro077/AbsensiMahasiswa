<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Filterable
{
    /**
     * Apply filters, search, sort, and pagination to a query.
     *
     * @param  Builder  $query
     * @param  Request  $request
     * @param  array    $filterableFields   — fields that can be filtered with exact match
     * @param  array    $searchableFields   — fields that can be searched with LIKE
     * @param  array    $sortableFields     — fields that can be sorted
     * @param  string   $defaultSort        — default sort field
     * @param  string   $defaultDirection   — default sort direction
     * @return array    Paginated result as array
     */
    protected function applyFilters(
        Builder $query,
        Request $request,
        array $filterableFields = [],
        array $searchableFields = [],
        array $sortableFields = [],
        string $defaultSort = 'id',
        string $defaultDirection = 'asc',
    ): array {
        // --- Exact match filters ---
        foreach ($filterableFields as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->input($field));
            }
        }

        // --- Search (LIKE) ---
        if ($request->filled('search') && !empty($searchableFields)) {
            $search = $request->input('search');
            $query->where(function (Builder $q) use ($search, $searchableFields) {
                foreach ($searchableFields as $field) {
                    $q->orWhere($field, 'LIKE', "%{$search}%");
                }
            });
        }

        // --- Sorting ---
        $sortField = $request->input('sort_by', $defaultSort);
        $sortDirection = $request->input('sort_dir', $defaultDirection);

        if (in_array($sortField, $sortableFields) || $sortField === $defaultSort) {
            $query->orderBy($sortField, in_array(strtolower($sortDirection), ['asc', 'desc']) ? $sortDirection : 'asc');
        }

        // --- Pagination ---
        $perPage = (int) $request->input('per_page', 15);
        $perPage = min(max($perPage, 1), 1000); // clamp 1–1000

        $paginated = $query->paginate($perPage);

        return [
            'data' => $paginated->items(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'from'         => $paginated->firstItem(),
                'to'           => $paginated->lastItem(),
            ],
            'links' => [
                'first' => $paginated->url(1),
                'last'  => $paginated->url($paginated->lastPage()),
                'prev'  => $paginated->previousPageUrl(),
                'next'  => $paginated->nextPageUrl(),
            ],
        ];
    }
}
