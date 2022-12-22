<?php

namespace Schranz\Search\Pucene\Storage;

use Doctrine\DBAL\Connection;
use Schranz\Search\Pucene\Model\Field;
use Schranz\Search\SEAL\Schema\FieldType;
use Schranz\Search\SEAL\Schema\Index;

/**
 * @internal
 */
class DocumentPersister
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @param Field[] $fields
     */
    public function persist(Index $index, string $id, array $document, array $fields): void
    {
        $schema = new PuceneSchema($index);

        $this->insertDocument($schema, $index, $id, $document);

        foreach ($fields as $field) {
            $this->insertField(
                $schema,
                $id,
                $field->name,
                $field->numberOfTerms,
            );

            $this->insertValue($schema, $id, $field);
            $this->insertTokens($schema, $id, $field);
        }
    }

    protected function insertDocument(PuceneSchema $schema, Index $index, string $id, array $document): void
    {
        $this->connection->insert(
            $schema->getDocumentsTableName(),
            [
                'id' => $id,
                'type' => $index->name,
                'document' => json_encode($document),
                'indexed_at' => new \DateTime(),
            ],
            [
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                'datetime',
            ]
        );
    }

    protected function insertField(PuceneSchema $schema, string $documentId, string $fieldName, int $fieldLength): void
    {
        $this->connection->insert(
            $schema->getFieldsTableName(),
            [
                'document_id' => $documentId,
                'field_name' => $fieldName,
                'field_length' => $fieldLength,
            ]
        );
    }

    protected function insertValue(PuceneSchema $schema, string $documentId, Field $field): void
    {
        $value = $field->value;
        if (!is_array($value)) {
            $value = [$value];
        }

        foreach ($value as $item) {
            if (FieldType::DATETIME === $field->type) {
                $date = new \DateTime($item);
                $item = $date ? $date->format('Y-m-d H:i:s') : null;
            } elseif (FieldType::BOOLEAN === $field->type) {
                $item = $item ? 1 : 0;
            }

            $tableName = $schema->getFieldTableName($field->type);
            if(!$tableName)continue;
            $this->connection->insert(
                $tableName,
                [
                    'document_id' => $documentId,
                    'field_name' => $field->name,
                    'value' => $item,
                ],
                [
                    \PDO::PARAM_STR,
                    \PDO::PARAM_STR,
                    \PDO::PARAM_STR,
                    $schema->getColumnType($field->type),
                ]
            );
        }
    }

    protected function insertTokens(PuceneSchema $schema, string $documentId, Field $field): void
    {
        $fieldTerms = [];
        foreach ($field->tokens as $token) {
            if (!array_key_exists($token->getEncodedTerm(), $fieldTerms)) {
                $fieldTerms[$token->getEncodedTerm()] = 0;
            }

            ++$fieldTerms[$token->getEncodedTerm()];

            if ($fieldTerms[$token->getEncodedTerm()] > 1) {
                continue;
            }

            $this->insertTerm($schema, $token->getEncodedTerm());
            $this->insertToken(
                $schema,
                $documentId,
                $field->name,
                $token->getEncodedTerm(),
                $field->numberOfTerms
            );
        }

        // update term frequency
        foreach ($fieldTerms as $term => $frequency) {
            $this->connection->update(
                $schema->getDocumentTermsTableName(),
                [
                    'term_frequency' => $frequency,
                ],
                [
                    'document_id' => $documentId,
                    'field_name' => $field->name,
                    'term' => $term,
                ]
            );
        }
    }

    protected function insertToken(PuceneSchema $schema, string $documentId, string $fieldName, string $term, int $fieldLength): void
    {
        $this->connection->insert(
            $schema->getDocumentTermsTableName(),
            [
                'document_id' => $documentId,
                'field_name' => $fieldName,
                'term' => $term,
                'field_length' => $fieldLength,
            ]
        );
    }

    protected function insertTerm(PuceneSchema $schema, string $term): void
    {
        $result = $this->connection->createQueryBuilder()
            ->select('term.term')
            ->from($schema->getTermsTableName(), 'term')
            ->where('term.term = :term')
            ->setParameter('term', $term)
            ->execute();

        if ($result->fetch()) {
            return;
        }

        $this->connection->insert(
            $schema->getTermsTableName(),
            [
                'term' => $term,
            ]
        );
    }
}
