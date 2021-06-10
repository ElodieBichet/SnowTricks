<?php

namespace App\Service;

class PaginationService
{
    public function isLastPage($page, $lastPageNumber): bool
    {
        if ($page >= $lastPageNumber) {
            return true;
        }

        return false;
    }

    public function getRenderOptions(string $itemsName, $queryBuilder, int $limit = 10, int $page = 1)
    {
        $offset = (int) ($page - 1) * $limit;

        $nbItems = count($queryBuilder->getQuery()->getResult());
        $lastPageNumber = ceil($nbItems / $limit);

        $queryBuilder
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $data = $queryBuilder->getQuery()->getResult();

        $isLastPage = $this->isLastPage($page, $lastPageNumber);

        $options = [
            $itemsName => $data,
            'page' => $page,
            'limit' => $limit,
            'isLastPage' => $isLastPage
        ];

        return $options;
    }
}
