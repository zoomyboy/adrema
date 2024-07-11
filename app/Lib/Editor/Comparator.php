<?php

namespace App\Lib\Editor;

enum Comparator: string
{
    case EQUAL = 'isEqual';
    case NOTEQUAL = 'isNotEqual';
    case IN = 'isIn';
    case NOTIN = 'isNotIn';

}
