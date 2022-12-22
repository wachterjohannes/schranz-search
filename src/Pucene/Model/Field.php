<?php

namespace Schranz\Search\Pucene\Model;

use Schranz\Search\Pucene\Analysis\Token;
use Schranz\Search\SEAL\Schema\FieldType;

class Field
{
    public int $numberOfTerms;

    /**
     * @param Token[] $tokens
     */
    public function __construct(
        public string $name,
        public array $tokens,
        public mixed $value,
        public FieldType $type = FieldType::TEXT,
    ) {
        $this->numberOfTerms = \count($this->tokens);
    }
}
