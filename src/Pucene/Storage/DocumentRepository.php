<?php

namespace Schranz\Search\Pucene\Storage;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Schranz\Search\Pucene\Model\Document;
use Schranz\Search\SEAL\Schema\FieldType;
use Schranz\Search\SEAL\Schema\Index;

class DocumentRepository
{
    private DocumentPersister $documentPersister;

    public function __construct(
        private Connection $connection,
    ) {
        $this->documentPersister = new DocumentPersister($this->connection);
    }

    public function persist(Document $document): void
    {
        $this->remove($document->index, $document->id);
        $this->documentPersister->persist($document->index, $document->id, $document->source, $document->fields);
    }

    public function remove(Index $index, string $identifier): void
    {
        $schema = new PuceneSchema($index);
        $this->connection->delete($schema->getDocumentsTableName(), ['id' => $identifier]);
    }

    public function search(Index $index, QueryBuilder $queryBuilder): \Generator
    {
        $schema = new PuceneSchema($index);
        $queryBuilder->from($schema->getDocumentsTableName(), 'document');
        $queryBuilder->leftJoin('document', $schema->getFieldTableName(FieldType::TEXT), 'text', 'text.id = document.id');
        $result = $queryBuilder->executeQuery();

        while ($row = $result->fetchAssociative()){
            yield json_decode($row['document'], true);
        }
    }

    public function count(Index $index, QueryBuilder $queryBuilder): int
    {
        $schema = new PuceneSchema($index);
        $queryBuilder->select('count(document.id)');
        // TODO make the join for type tables dynamic
        $queryBuilder->leftJoin('document', $schema->getFieldTableName(FieldType::TEXT), 'text', 'text.id = document.id');
        $queryBuilder->from($schema->getDocumentsTableName(), 'document');

        return $queryBuilder->fetchOne();
    }
}
