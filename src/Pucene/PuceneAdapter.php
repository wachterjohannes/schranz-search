<?php

namespace Schranz\Search\Pucene;

use Schranz\Search\Pucene\Connection\PuceneConnection;
use Schranz\Search\Pucene\Schema\PuceneSchemaManager;
use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;

class PuceneAdapter implements AdapterInterface
{
    public function __construct(
        private PuceneSchemaManager $schemaManager,
        private PuceneConnection $connection
    )
    {
    }

    public function getSchemaManager(): SchemaManagerInterface
    {
        return $this->schemaManager;
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }
}
