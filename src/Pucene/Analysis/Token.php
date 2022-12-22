<?php

namespace Schranz\Search\Pucene\Analysis;

class Token
{
    public function __construct(
        public string $term,
        public int $startOffset,
        public int $endOffset,
        public string $type,
        public int $position
    ) {
    }

    public function getEncodedTerm(): string
    {
        return utf8_encode($this->term);
    }
}
