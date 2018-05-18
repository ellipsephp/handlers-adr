<?php declare(strict_types=1);

namespace Ellipse\Handlers\Exceptions;

use TypeError;

use Ellipse\Exceptions\TypeErrorMessage;

class InputTypeException extends TypeError implements ADRRequestHandlerExceptionInterface
{
    public function __construct($value)
    {
        $msg = new TypeErrorMessage('domain input', $value, 'array');

        parent::__construct((string) $msg);
    }
}
