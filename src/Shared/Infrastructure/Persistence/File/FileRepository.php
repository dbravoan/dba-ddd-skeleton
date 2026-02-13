<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Persistence\File;

use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Throwable;

abstract class FileRepository
{
    protected string $directory;
    protected string $format;

    /**
     * Constructor de FileRepository
     *
     * @param string $directory - Directorio donde se almacenan los archivos
     * @param string $format - Formato de archivo: 'json', 'xml', 'csv'
     */
    public function __construct(string $directory, string $format = 'json')
    {
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        if (!in_array($format, ['json', 'xml', 'csv'])) {
            throw new InvalidArgumentException("Invalid format. Only 'json', 'xml', and 'csv' are supported.");
        }

        $this->directory = $directory;
        $this->format = $format;
    }

    /**
     * Encuentra un archivo por su ID.
     *
     * @param mixed $id
     * @return array|null
     */
    public function find($id): ?array
    {
        $filePath = $this->getFilePath($id);

        if (!Storage::exists($filePath)) {
            return null;
        }

        $content = Storage::get($filePath);

        return $this->fromFile($content);
    }

    /**
     * Crea un archivo.
     *
     * @param array $attributes
     * @return array
     * @throws Throwable
     */
    public function create(array $attributes): array
    {
        $id = $attributes['id'] ?? uniqid();
        $filePath = $this->getFilePath($id);

        if (Storage::exists($filePath)) {
            throw new InvalidArgumentException("File with ID {$id} already exists.");
        }

        $this->saveToFile($filePath, $attributes);

        return $attributes;
    }

    /**
     * Actualiza un archivo.
     *
     * @param mixed $id
     * @param array $attributes
     * @return array
     * @throws Throwable
     */
    public function update($id, array $attributes): array
    {
        $filePath = $this->getFilePath($id);

        if (!Storage::exists($filePath)) {
            throw new InvalidArgumentException("File with ID {$id} not found.");
        }

        $this->saveToFile($filePath, $attributes);

        return $attributes;
    }

    /**
     * Elimina un archivo por su ID.
     *
     * @param mixed $id
     * @return void
     */
    public function delete($id): void
    {
        $filePath = $this->getFilePath($id);

        if (!Storage::exists($filePath)) {
            throw new InvalidArgumentException("File with ID {$id} not found.");
        }

        Storage::delete($filePath);
    }

    /**
     * Busca archivos que coincidan con los criterios.
     *
     * @param array $criteria
     * @return array
     */
    public function matching(array $criteria): array
    {
        $files = Storage::files($this->directory);

        $matchingFiles = array_filter($files, function ($file) use ($criteria) {
            $content = Storage::get($file);
            $data = $this->fromFile($content);

            foreach ($criteria as $key => $value) {
                if (!isset($data[$key]) || $data[$key] !== $value) {
                    return false;
                }
            }
            return true;
        });

        return array_map(function ($file) {
            return $this->fromFile(Storage::get($file));
        }, $matchingFiles);
    }

    /**
     * Convierte el contenido del archivo a un array dependiendo del formato.
     *
     * @param string $content
     * @return array
     */
    protected function fromFile(string $content): array
    {
        return match ($this->format) {
            'json' => $this->fromJson($content),
            'xml'  => $this->fromXml($content),
            'csv'  => $this->fromCsv($content),
        };
    }

    /**
     * Guarda los atributos en un archivo en el formato especificado.
     *
     * @param string $filePath
     * @param array $attributes
     * @return void
     * @throws Throwable
     */
    protected function saveToFile(string $filePath, array $attributes): void
    {
        $content = match ($this->format) {
            'json' => $this->toJson($attributes),
            'xml'  => $this->toXml($attributes),
            'csv'  => $this->toCsv($attributes),
        };

        Storage::put($filePath, $content);
    }

    /**
     * Convierte un array a JSON.
     *
     * @param array $attributes
     * @return string
     */
    protected function toJson(array $attributes): string
    {
        return json_encode($attributes, JSON_PRETTY_PRINT);
    }

    /**
     * Convierte un array a XML.
     *
     * @param array $attributes
     * @return string
     */
    protected function toXml(array $attributes): string
    {
        $xml = new \SimpleXMLElement('<root/>');

        array_walk_recursive($attributes, function ($value, $key) use ($xml) {
            $xml->addChild($key, htmlspecialchars((string) $value));
        });

        return $xml->asXML();
    }

    /**
     * Convierte un array a CSV.
     *
     * @param array $attributes
     * @return string
     */
    protected function toCsv(array $attributes): string
    {
        $handle = fopen('php://temp', 'r+');
        // Assuming each record has the same keys, using the keys of the first item as headers
        fputcsv($handle, array_keys($attributes));
        fputcsv($handle, array_values($attributes));

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    /**
     * Convierte un contenido JSON a un array.
     *
     * @param string $content
     * @return array
     */
    protected function fromJson(string $content): array
    {
        return json_decode($content, true);
    }

    /**
     * Convierte un contenido XML a un array.
     *
     * @param string $content
     * @return array
     */
    protected function fromXml(string $content): array
    {
        $xmlObject = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
        return json_decode(json_encode($xmlObject), true);
    }

    /**
     * Convierte un contenido CSV a un array.
     *
     * @param string $content
     * @return array
     */
    protected function fromCsv(string $content): array
    {
        $rows = [];
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $content);
        rewind($handle);

        $headers = fgetcsv($handle);
        $data = fgetcsv($handle);

        fclose($handle);

        if ($headers && $data) {
            $rows = array_combine($headers, $data);
        }

        return $rows;
    }

    /**
     * Obtiene la ruta completa de un archivo según el ID.
     *
     * @param mixed $id
     * @return string
     */
    protected function getFilePath($id): string
    {
        $extension = match ($this->format) {
            'json' => 'json',
            'xml'  => 'xml',
            'csv'  => 'csv',
        };
        return "{$this->directory}/{$id}.{$extension}";
    }
}
