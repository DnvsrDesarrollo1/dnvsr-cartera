<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait AppTrait
{
    /**
    *
    *  En el MODELO, se debe definir:
        *  @var protected $allowIncluded
        *  @var protected $allowFilter
        *  @var protected $allowSort
    *
    *   protected allowIncluded = ['relationship1', 'relationship2'];
    */

    public function scopeIncluded(Builder $query)
    {
        if(empty($this->allowIncluded) || empty(request('included')))
        {
            return;
        }

        $relations = explode('|', request('included'));

        $allowIncluded = collect($this->allowIncluded);

        foreach($relations as $key => $relationship)
        {
            if (!$allowIncluded->contains($relationship)) {
                unset($relations[$key]);
            }
        }

        $query->with($relations);
    }

    public function scopeFilter(Builder $query)
    {
        if(empty($this->allowFilter) || empty(request('filter')))
        {
            return;
        }

        $filters = request('filter');
        $allowFilter = collect($this->allowFilter);

        foreach($filters as $filter => $value)
        {
            if ($allowFilter->contains($filter)) {
                $query->where($filter, 'like', $value.'%');
            }
        }
    }

    public function scopeSort(Builder $query)
    {
        if(empty($this->allowSort) || empty(request('sort')))
        {
            return;
        }

        $sortFields = explode('|', request('sort'));
        $allowSort = collect($this->allowedSort);

        foreach($sortFields as $sortField)
        {
            $direction = 'asc';

            if(substr($sortField, 0, 1) == '-') {
                $direction = 'desc';
                $sortField = substr($sortField, 1);
            }

            if ($allowSort->contains($sortField)) {
                $query->orderBy($sortField, $direction);
            }
        }
    }

    public function scopeGetOrPaginate(Builder $query)
    {
        if(request('perPage')) {
            $perPage = intval(request('perPage'));
            if ($perPage) {
                return $query->paginate($perPage);
            }
        }
        return $query->paginate(1000);
    }

}
