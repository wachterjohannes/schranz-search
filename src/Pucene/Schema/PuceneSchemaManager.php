<?php

namespace Schranz\Search\Pucene\Schema;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Schranz\Search\Pucene\Storage\PuceneSchema;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Index;

class PuceneSchemaManager implements SchemaManagerInterface
{
    private Connection $connection;

    private AbstractSchemaManager $dbalSchemaManager;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->dbalSchemaManager = $this->connection->createSchemaManager();
    }

    public function existIndex(Index $index): bool
    {
        return $this->dbalSchemaManager->tablesExist([$this->getSchema($index)->getDocumentsTableName()]);
    }

    public function dropIndex(Index $index): void
    {
        foreach ($this->getSchema($index)->toDropSql($this->connection->getDatabasePlatform()) as $sql) {
            $this->connection->exec($sql);
        }
    }

    public function createIndex(Index $index): void
    {
        if ($this->dbalSchemaManager->tablesExist($this->getSchema($index)->getDocumentsTableName())) {
            return;
        }

        foreach ($this->getSchema($index)->toSql($this->connection->getDatabasePlatform()) as $sql) {
            $this->connection->exec($sql);
        }
    }

    private function getSchema(Index $index): PuceneSchema
    {
        return new PuceneSchema($index);
    }
}
