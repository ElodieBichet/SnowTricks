<?php

namespace App\Service;

class PaginationService
{
    /**
     * Return true is the current page is the last one
     */
    public function isLastPage($page, $lastPageNumber): bool
    {
        if ($page >= $lastPageNumber) {
            return true;
        }

        return false;
    }

    /**
     * Return an array of options which can be used as arguments in a render function
     */
    public function getRenderOptions(string $itemsName, $queryBuilder, int $limit = 10, int $page = 1): array
    {
        $offset = (int) ($page - 1) * $limit;

        $nbItems = count($queryBuilder->getQuery()->getResult());
        $lastPageNumber = ceil($nbItems / $limit);

        // Add a limit and an offset to the query to filter query result
        $queryBuilder
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        // Get the items of the requested page only
        $data = $queryBuilder->getQuery()->getResult();

        $isLastPage = $this->isLastPage($page, $lastPageNumber);

        // Build the array of options for the render function
        $options = [
            $itemsName => $data,
            'page' => $page,
            'limit' => $limit,
            'isLastPage' => $isLastPage
        ];

        return $options;
    }
}
