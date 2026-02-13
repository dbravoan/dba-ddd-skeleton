<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure;

class XMLElement extends \SimpleXMLElement
{
    public function addCData($cdataText)
    {
        $node = dom_import_simplexml($this);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdataText));
    }
}
