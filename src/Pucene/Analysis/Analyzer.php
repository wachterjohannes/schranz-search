<?php

namespace Schranz\Search\Pucene\Analysis;

use Schranz\Search\Pucene\Analysis\CharacterFilter\CharacterFilterInterface;
use Schranz\Search\Pucene\Analysis\TokenFilter\TokenFilterInterface;
use Schranz\Search\Pucene\Analysis\Tokenizer\TokenizerInterface;

class Analyzer implements AnalyzerInterface
{
    public function __construct(
        protected CharacterFilterInterface $characterFilter,
        protected TokenizerInterface $tokenizer,
        protected TokenFilterInterface $tokenFilter
    ) {
    }

    public function analyze(string $fieldContent): array
    {
        $input = $this->characterFilter->filter($fieldContent);
        $tokens = $this->tokenizer->tokenize($input);

        $result = [];
        foreach ($tokens as $token) {
            $result = array_merge($result, $this->tokenFilter->filter($token));
        }

        return $result;
    }
}
