<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure;

use SimpleXMLElement;

final class XMLElement extends SimpleXMLElement
{
    public function addCData(string $cdataText): void
    {
        $node = dom_import_simplexml($this);
        $no   = $node->ownerDocument;

        if ($no !== null) {
            $node->appendChild($no->createCDATASection($cdataText));
        }
    }
}
