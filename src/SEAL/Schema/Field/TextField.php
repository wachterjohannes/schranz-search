<?php

namespace Schranz\Search\SEAL\Schema\Field;

use Schranz\Search\SEAL\Schema\FieldType;

class TextField extends StringField
{
    public function __construct(string $name, bool $multiple = false)
    {
        parent::__construct($name, FieldType::TEXT, $multiple);
    }
}
