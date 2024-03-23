<?php

namespace App\Repositories;


trait EloquentEagerLoadTrait
{
    /**
     * apply eager load in query builder
     *
     * @param $queryBuilder
     * @param $searchCriteria
     * @return mixed
     */
    public function applyEagerLoad($queryBuilder, $searchCriteria)
    {
        if (isset($searchCriteria['eagerLoad'])) {
            if(isset($searchCriteria['include'])) {
                $includedRelationships = $this->eagerLoadWithIncludeParam($searchCriteria['include'], $searchCriteria['eagerLoad']);
                $queryBuilder = $queryBuilder->with($includedRelationships);
            }
        }
        return $queryBuilder;
    }

    /**
     * get eager loaded relationships
     *
     * @param string $includeString
     * @param array $eagerLoads
     * @return array
     */
    public function eagerLoadWithIncludeParam(string $includeString, array $eagerLoads)
    {
        $requestedRelationships = explode(',', $includeString);

        $shouldLoadRelationships = [];
        foreach ($requestedRelationships  as $relationship) {
            if (isset($eagerLoads[$relationship])) {
                $shouldLoadRelationships[] = $eagerLoads[$relationship];
            }
        }
        if (isset($eagerLoads['always'])) {
            $alwaysRelationships = explode(',', $eagerLoads['always']);
            $shouldLoadRelationships = array_merge($shouldLoadRelationships, $alwaysRelationships);
        }

        // always eagerload - needs to get user label
        if (in_array('user.userLabel', $requestedRelationships)) {
            $shouldLoadRelationships = array_merge($shouldLoadRelationships, ['userRoles']);
        }

        return array_unique($shouldLoadRelationships);
    }


}
