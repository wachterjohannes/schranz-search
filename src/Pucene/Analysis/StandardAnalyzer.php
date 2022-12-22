<?php

namespace Schranz\Search\Pucene\Analysis;

use Schranz\Search\Pucene\Analysis\CharacterFilter\ChainCharacterFilter;
use Schranz\Search\Pucene\Analysis\CharacterFilter\StandardCharacterFilter;
use Schranz\Search\Pucene\Analysis\TokenFilter\ChainTokenFilter;
use Schranz\Search\Pucene\Analysis\TokenFilter\LowercaseTokenFilter;
use Schranz\Search\Pucene\Analysis\TokenFilter\StandardTokenFilter;
use Schranz\Search\Pucene\Analysis\Tokenizer\StandardTokenizer;

class StandardAnalyzer extends Analyzer
{
    public function __construct()
    {
        parent::__construct(
            new ChainCharacterFilter(
                [
                    new StandardCharacterFilter(),
                ]
            ),
            $this->tokenizer = new StandardTokenizer(),
            new ChainTokenFilter(
                [
                    new StandardTokenFilter(),
                    new LowercaseTokenFilter(),
                ]
            )
        );
    }
}
