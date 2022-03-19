<?php

namespace App\Service;

use Doctrine\ORM\QueryBuilder;
use LogicException;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class PaginationHelper
{
    public function paginate(QueryBuilder $qb, int $limit = 10, int $offset = 0): Pagerfanta
    {
        if (0 >= $limit) {
            throw new LogicException('Limit must be greater than 0');
        }
        if (0 > $offset) {
            throw new LogicException('Offset must be greater than or equal to 0');
        }

        $pager = new Pagerfanta(new QueryAdapter($qb));
        $currentPage = ceil(($offset + 1) / $limit);
        $pager->setCurrentPage($currentPage);
        $pager->setMaxPerPage($limit);

        return $pager;
    }
}
