<?php

namespace Schranz\Search\Pucene\Model;

use Schranz\Search\SEAL\Schema\Index;

class Document
{
    /**
     * @param array <string, mixed> $source
     * @param Field[] $fields
     */
    public function __construct(
        public Index $index,
        public string $id,
        public array $source,
        public array $fields,
    ) {
    }
}
