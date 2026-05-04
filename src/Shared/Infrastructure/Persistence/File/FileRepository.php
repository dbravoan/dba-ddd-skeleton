<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Persistence\File;

use DOMDocument;
use DOMElement;
use RuntimeException;

abstract class FileRepository
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /** @return array<string, mixed>|null */
    public function find(mixed $id): ?array
    {
        $idString = is_scalar($id) ? (string) $id : '';
        $filename = "{$this->path}/{$idString}.{$this->extension()}";

        if (! file_exists($filename)) {
            return null;
        }

        $content = file_get_contents($filename);

        return $this->fromFile(is_string($content) ? $content : '');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): void
    {
        $id = $attributes['id'] ?? null;
        $idString = is_scalar($id) ? (string) $id : '';
        $this->saveToFile($idString, $attributes);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(mixed $id, array $attributes): void
    {
        $idString = is_scalar($id) ? (string) $id : '';
        $this->saveToFile($idString, $attributes);
    }

    public function delete(mixed $id): void
    {
        $idString = is_scalar($id) ? (string) $id : '';
        $filename = "{$this->path}/{$idString}.{$this->extension()}";

        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    /**
     * @param  array<string, mixed>  $criteria
     * @return array<int, array<string, mixed>>
     */
    public function matching(array $criteria): array
    {
        $files = glob("{$this->path}/*.{$this->extension()}");

        if ($files === false) {
            return [];
        }

        return array_map(function ($file) {
            $content = file_get_contents($file);

            return $this->fromFile(is_string($content) ? $content : '');
        }, $files);
    }

    abstract protected function extension(): string;

    /** @return array<string, mixed> */
    protected function fromFile(string $content): array
    {
        return match ($this->extension()) {
            'json' => $this->fromJson($content),
            'xml' => $this->fromXml($content),
            'csv' => $this->fromCsv($content),
            default => throw new RuntimeException('Unsupported extension'),
        };
    }

    /** @param array<string, mixed> $attributes */
    protected function saveToFile(string $id, array $attributes): void
    {
        $filename = "{$this->path}/{$id}.{$this->extension()}";
        $content = match ($this->extension()) {
            'json' => $this->toJson($attributes),
            'xml' => $this->toXml($attributes),
            'csv' => $this->toCsv($attributes),
            default => throw new RuntimeException('Unsupported extension'),
        };

        file_put_contents($filename, $content);
    }

    /** @param array<string, mixed> $attributes */
    protected function toJson(array $attributes): string
    {
        $json = json_encode($attributes);
        if ($json === false) {
            throw new RuntimeException('Unable to encode JSON');
        }

        return $json;
    }

    /** @param array<string, mixed> $attributes */
    protected function toXml(array $attributes): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $root = $dom->createElement('record');
        $dom->appendChild($root);

        $this->appendXmlNodes($dom, $root, $attributes);

        $xml = $dom->saveXML();
        if ($xml === false) {
            throw new RuntimeException('Unable to encode XML');
        }

        return $xml;
    }

    /**
     * @param  array<mixed>  $data
     */
    private function appendXmlNodes(DOMDocument $dom, DOMElement $parent, array $data): void
    {
        foreach ($data as $key => $value) {
            $tagName = preg_replace('/[^a-zA-Z0-9_\-.]/', '_', (string) $key) ?? (string) $key;
            $element = $dom->createElement($tagName);

            if (is_array($value)) {
                $this->appendXmlNodes($dom, $element, $value);
            } else {
                $scalar = is_scalar($value) || $value === null ? (string) $value : '';
                $element->appendChild($dom->createTextNode($scalar));
            }

            $parent->appendChild($element);
        }
    }

    /** @param array<string, mixed> $attributes */
    protected function toCsv(array $attributes): string
    {
        $stream = fopen('php://memory', 'r+');
        if ($stream === false) {
            throw new RuntimeException('Unable to open memory stream');
        }

        $keys = array_keys($attributes);
        /** @var array<int|string, bool|float|int|string|null> $keysStrings */
        $keysStrings = array_map(fn ($k) => (string) $k, $keys);
        fputcsv($stream, $keysStrings, separator: ',', enclosure: '"', escape: '\\');

        $values = array_values($attributes);
        /** @var array<int|string, bool|float|int|string|null> $valuesStrings */
        $valuesStrings = array_map(fn ($v) => is_scalar($v) ? (string) $v : null, $values);
        fputcsv($stream, $valuesStrings, separator: ',', enclosure: '"', escape: '\\');

        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        return is_string($csv) ? $csv : '';
    }

    /** @return array<string, mixed> */
    protected function fromJson(string $content): array
    {
        $data = json_decode($content, true);

        return is_array($data) ? $data : [];
    }

    /** @return array<string, mixed> */
    protected function fromXml(string $content): array
    {
        $xml = simplexml_load_string($content);
        if ($xml === false) {
            return [];
        }

        $data = json_decode((string) json_encode($xml), true);

        return is_array($data) ? $data : [];
    }

    /** @return array<string, mixed> */
    protected function fromCsv(string $content): array
    {
        $stream = fopen('php://memory', 'r+');
        if ($stream === false) {
            throw new RuntimeException('Unable to open memory stream');
        }
        fwrite($stream, $content);
        rewind($stream);
        $keys = fgetcsv($stream, length: 0, separator: ',', enclosure: '"', escape: '\\');
        $values = fgetcsv($stream, length: 0, separator: ',', enclosure: '"', escape: '\\');
        fclose($stream);

        if (! is_array($keys) || ! is_array($values)) {
            return [];
        }

        $stringKeys = array_map(fn ($k) => (string) $k, $keys);

        return array_combine($stringKeys, $values);
    }
}
