<?php declare(strict_types=1);

namespace Ellipse\Handlers\Exceptions;

use TypeError;

use Ellipse\Handlers\ADR\ResponderInterface;

use Ellipse\Exceptions\ContainerEntryTypeErrorMessage;

class ContainedResponderTypeException extends TypeError implements ADRRequestHandlerExceptionInterface
{
    public function __construct(string $id, $value)
    {
        $msg = new ContainerEntryTypeErrorMessage($id, $value, ResponderInterface::class);

        parent::__construct((string) $msg);
    }
}
