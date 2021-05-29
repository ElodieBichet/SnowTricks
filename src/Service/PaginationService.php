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

  public function getRenderOptions(string $itemsName, $repo, $criteria = [], $orderBy = [], $limit = 10, $page = 1)
  {
    $offset = ($page - 1) * $limit;
    $data = $repo->findBy($criteria, $orderBy, $limit, $offset);

    $nbItems = count($repo->findBy($criteria));

    $lastPageNumber = ceil($nbItems / $limit);
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
