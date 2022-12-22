<?php

namespace Schranz\Search\Pucene\Analysis\Tokenizer;

use Schranz\Search\Pucene\Analysis\Token;

interface TokenizerInterface
{
    /**
     * @return Token[]
     */
    public function tokenize(string $input): array;
}
