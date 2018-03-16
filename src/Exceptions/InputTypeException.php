<?php declare(strict_types=1);

namespace Ellipse\Handlers\Exceptions;

use TypeError;

class InputTypeException extends TypeError
{
    public function __construct($value)
    {
        $template = "The input returned by the parser has type %s - array expected";

        $type = is_object($value) ? get_class($value) : gettype($value);

        $msg = sprintf($template, $value, $type);

        parent::__construct($msg);
    }
}
