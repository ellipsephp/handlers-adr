<?php declare(strict_types=1);

namespace Ellipse\Handlers\Exceptions;

use TypeError;

use Ellipse\Exceptions\ContainerEntryTypeErrorMessage;

class ContainedRequestParserTypeException extends TypeError implements ADRRequestHandlerExceptionInterface
{
    public function __construct(string $id, $value)
    {
        $msg = new ContainerEntryTypeErrorMessage($id, $value, 'callable');

        parent::__construct((string) $msg);
    }
}
