<?php declare(strict_types=1);

namespace Ellipse\Handlers\ADR;

use Psr\Http\Message\ServerRequestInterface;

class DefaultRequestParser implements RequestParserInterface
{
    /**
     * Return the given request attributes merged with its query params, parsed
     * body and uploaded files.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return array
     */
    public function input(ServerRequestInterface $request): array
    {
        return array_merge(
            $request->getAttributes(),
            $request->getQueryParams(),
            $request->getParsedBody(),
            $request->getUploadedFiles()
        );
    }
}
