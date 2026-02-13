<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Helpers;

class WorkReportCodeGenerator
{
    private const PREFIX = '93';
    private const UID_FILE = 'wr_uid_sequence.json';   // Archivo para el UID
    private const CODE_FILE = 'wr_code_sequence.json'; // Archivo para el CODE
    private const INT32_MAX = 2147483647;              // Valor máximo de int32
    private const UID_START = 11300;                   // Valor inicial para UID
    private const CODE_START = 9321300;               // Valor inicial para CODE

    /**
     * Genera un CODE consecutivo que solo avanza cuando el reporte es correcto.
     *
     * @return string
     */
    public static function generateCode(): string
    {
        $code = self::getLastCode();
        $newCode = $code + 1;

        // Verificar que el nuevo CODE no supere int32
        $codeWithPrefix = (int)(self::PREFIX . $newCode);
        if ($codeWithPrefix > self::INT32_MAX) {
            // Reiniciar el contador si se supera int32
            $newCode = self::CODE_START;
            $codeWithPrefix = (int)(self::PREFIX . $newCode);
        }

        // Guardar el nuevo CODE
        self::storeLastCode($newCode);

        return (string)$codeWithPrefix;
    }

    /**
     * Genera un UID consecutivo que siempre avanza.
     *
     * @return string
     */
    public static function generateUid(): string
    {
        $uid = self::getLastUid();
        $newUid = $uid + 1;

        // Verificar que el nuevo UID no supere int32
        $uidWithPrefix = (int)(self::PREFIX . $newUid);
        if ($uidWithPrefix > self::INT32_MAX) {
            // Reiniciar el contador si se supera int32
            $newUid = self::UID_START;
            $uidWithPrefix = (int)(self::PREFIX . $newUid);
        }

        // Guardar el nuevo UID
        self::storeLastUid($newUid);

        return (string)$uidWithPrefix;
    }

    /**
     * Obtiene el último CODE generado.
     *
     * @return int
     */
    private static function getLastCode(): int
    {
        if (!file_exists(storage_path(self::CODE_FILE))) {
            return self::CODE_START;
        }

        $content = file_get_contents(storage_path(self::CODE_FILE));
        $data = json_decode($content, true);

        return $data['last_code'] ?? self::CODE_START;
    }

    /**
     * Guarda el último CODE generado en el archivo.
     *
     * @param int $code
     */
    private static function storeLastCode(int $code): void
    {
        $data = ['last_code' => $code];
        file_put_contents(storage_path(self::CODE_FILE), json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Obtiene el último UID generado.
     *
     * @return int
     */
    private static function getLastUid(): int
    {
        if (!file_exists(storage_path(self::UID_FILE))) {
            return self::UID_START;
        }

        $content = file_get_contents(storage_path(self::UID_FILE));
        $data = json_decode($content, true);

        return $data['last_uid'] ?? self::UID_START;
    }

    /**
     * Guarda el último UID generado en el archivo.
     *
     * @param int $uid
     */
    private static function storeLastUid(int $uid): void
    {
        $data = ['last_uid' => $uid];
        file_put_contents(storage_path(self::UID_FILE), json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Restablece el CODE para pruebas o en caso de errores.
     *
     * @param int $newCode
     */
    public static function resetCode(int $newCode): void
    {
        self::storeLastCode($newCode);
    }

    /**
     * Restablece el UID para pruebas o en caso de errores.
     *
     * @param int $newUid
     */
    public static function resetUid(int $newUid): void
    {
        self::storeLastUid($newUid);
    }
}
