<?php

namespace Schranz\Search\SEAL\Search\Condition;

class QueryCondition
{
    /**
     * @param string $query
     * @param string[]|null $field
     * @param array<'fuzzy', mixed> $options
     */
    public function __construct(
        public string $query,
        public ?array $field = null,
        public array $options = [],
    ) {
    }
}
