<?php declare(strict_types=1);

namespace Ellipse\Handlers\Exceptions;

use TypeError;

use Ellipse\Handlers\ADR\DomainInterface;

use Ellipse\Exceptions\ContainerEntryTypeErrorMessage;

class ContainedDomainTypeException extends TypeError implements ADRRequestHandlerExceptionInterface
{
    public function __construct(string $id, $value)
    {
        $msg = new ContainerEntryTypeErrorMessage($id, $value, DomainInterface::class);

        parent::__construct((string) $msg);
    }
}
