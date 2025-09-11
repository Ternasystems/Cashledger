<?php

declare(strict_types=1);

namespace TS_Database\Enums;

enum WhereType: string
{
    case AND = 'AND';
    case OR = 'OR';
}