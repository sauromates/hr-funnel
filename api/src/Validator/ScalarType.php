<?php

declare(strict_types=1);

namespace App\Validator;

enum ScalarType: string
{
    case String = 'string';
    case Number = 'number';
    case Boolean = 'boolean';
}
