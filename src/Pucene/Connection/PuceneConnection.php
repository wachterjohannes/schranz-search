<?php

namespace Schranz\Search\Pucene\Connection;

use Schranz\Search\Pucene\Analysis\AnalyzerInterface;
use Schranz\Search\Pucene\Model\Document;
use Schranz\Search\Pucene\Model\Field;
use Schranz\Search\Pucene\Query\QueryFactory;
use Schranz\Search\Pucene\Storage\DocumentRepository;
use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Schema\Field\AbstractField;
use Schranz\Search\SEAL\Schema\FieldType;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;

class PuceneConnection implements ConnectionInterface
{
    public function __construct(
        private DocumentRepository $repository,
        private QueryFactory $queryFactory,
        private AnalyzerInterface $analyzer,
    ) {
    }

    public function save(Index $index, array $document): array
    {
        $fields = [];
        foreach ($document as $fieldName => $fieldContent) {
            /** @var AbstractField $indexField */
            $indexField = $index->fields[$fieldName] ?? null;
            if (!$indexField) {
                continue;
            }

            if (!is_array($fieldContent)) {
                $fieldContent = [$fieldContent];
            }

            $terms = [];
            if ($indexField->type === FieldType::TEXT) {
                foreach ($fieldContent as $content) {
                    if (is_string($content)) {
                        $terms[] = $this->analyzer->analyze($content);
                    }
                }
            }

            $fields[] = new Field(
                $fieldName,
                array_merge(...$terms),
                $fieldContent,
                $indexField->type,
            );
        }

        // TODO determine IdentifierField
        $id = $document['id'];

        $document = new Document($index, $id, $document, $fields);

        $this->repository->persist($document);

        return $document->source;
    }

    public function delete(Index $index, string $identifier): void
    {
        $this->repository->remove($index, $identifier);
    }

    public function search(Search $search): Result
    {
        $queryBuilder = $this->queryFactory->create($search);

        // TODO multiple indices
        $result = $this->repository->search(array_values($search->indexes)[0], clone $queryBuilder);
        $count = $this->repository->count(array_values($search->indexes)[0], clone $queryBuilder);

        return new Result($result, $count);
    }
}
