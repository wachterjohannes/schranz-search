<?php

namespace Schranz\Search\Pucene\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Schranz\Search\SEAL\Search\Condition\IdentifierCondition;
use Schranz\Search\SEAL\Search\Condition\TermCondition;
use Schranz\Search\SEAL\Search\Search;

class QueryFactory
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function create(Search $search): QueryBuilder
    {
        $queryBuilder = new QueryBuilder($this->connection);
        $queryBuilder->select('document.document');
        foreach ($search->filters as $key => $filter) {
            if ($filter instanceof IdentifierCondition) {
                $queryBuilder
                    ->andWhere('document.id = :identifier' . $key)
                    ->setParameter('identifier' . $key, $filter->identifier);
            } elseif ($filter instanceof TermCondition) {
                $queryBuilder
                    ->andWhere('text.field_name = :field' . $key)
                    ->andWhere('text.value = :value' . $key)
                    ->setParameter('field' . $key, $filter->field)
                    ->setParameter('value' . $key, $filter->term);
            }
        }

        if ($search->limit) {
            $queryBuilder->setMaxResults($search->limit);
        }

        if ($search->offset) {
            $queryBuilder->setFirstResult($search->offset);
        }

        return $queryBuilder;
    }
}
