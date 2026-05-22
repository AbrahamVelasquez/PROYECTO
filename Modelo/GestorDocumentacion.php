<?php

/**
 * Modelo/GestorDocumentacion.php — Gestión de archivos PDF en disco
 *
 * Abstrae todas las operaciones de sistema de ficheros para el Paso 4 (Seguimiento).
 * Ningún helper de Seguimiento toca el disco directamente — todo pasa por aquí.
 *
 * La estructura de carpetas que gestiona es:
 *   /Documentacion/{ciclo}/{tipo}/
 *   Donde {ciclo} es el código del ciclo (ej: "1DAW") y {tipo} puede ser:
 *     - Plan_Formativo
 *     - Fichas
 *     - Valoraciones
 *
 * Los nombres de archivos se sanean para eliminar caracteres peligrosos,
 * y si ya existe un archivo con el mismo nombre se añade un sufijo de timestamp
 * para no sobreescribir el original.
 *
 * MVC: Actúa como Modelo de sistema de ficheros, análogo a un Modelo de BD
 * pero para archivos. Los helpers de Seguimiento son sus "controladores".
 */

class GestorDocumentacion {

    /**
     * Construye la ruta física absoluta hacia la carpeta del ciclo y tipo indicados.
     * El nombre del ciclo se sanea (solo alfanumérico) para evitar path traversal.
     */
    private static function getRutaCarpeta(string $ciclo, string $tipo): string {
        $cicloSaneado = preg_replace('/[^A-Za-z0-9]/', '', $ciclo);

        if ($tipo === 'plan_formativo') {
            $subcarpeta = 'Plan_Formativo';
        } elseif ($tipo === 'fichas') {
            $subcarpeta = 'Fichas';
        } else {
            $subcarpeta = 'Valoraciones';
        }

        $raizProyecto = realpath(__DIR__ . '/..');

        return $raizProyecto . '/Documentacion/' . $cicloSaneado . '/' . $subcarpeta . '/';
    }

    /**
     * Lista los ficheros de una carpeta filtrados por el prefijo del alumno
     */
    public static function listarFicheros(string $ciclo, string $tipo, string $prefijoAlumno): array {
        $ruta = self::getRutaCarpeta($ciclo, $tipo);
        if (!is_dir($ruta)) {
            return [];
        }

        $ficheros = scandir($ruta);
        $resultado = [];

        foreach ($ficheros as $f) {
            if ($f === '.' || $f === '..') continue;

            if (is_file($ruta . $f) && stripos($f, $prefijoAlumno) === 0) {
                $resultado[] = $f;
            }
        }

        return $resultado;
    }

    /**
     * Elimina un fichero físico del disco duro de forma segura
     */
    public static function eliminarFichero(string $ciclo, string $tipo, string $nombreArchivo): bool {
        $rutaBase = self::getRutaCarpeta($ciclo, $tipo);
        $nombreSeguro = basename($nombreArchivo); // Evita Path Traversal
        $rutaCompleta = $rutaBase . $nombreSeguro;

        if (file_exists($rutaCompleta) && is_file($rutaCompleta)) {
            return unlink($rutaCompleta);
        }
        return false;
    }

    /**
     * Obtiene la ruta completa de un fichero para su descarga (y verifica que exista)
     */
    public static function obtenerRutaDescarga(string $ciclo, string $tipo, string $nombreArchivo): ?string {
        $rutaBase = self::getRutaCarpeta($ciclo, $tipo);
        $nombreSeguro = basename($nombreArchivo);
        $rutaCompleta = $rutaBase . $nombreSeguro;

        if (file_exists($rutaCompleta) && is_file($rutaCompleta)) {
            return $rutaCompleta;
        }
        return null;
    }

    /**
     * Procesa la subida de un fichero mitigando duplicados de nombre
     */
    public static function guardarFichero(string $ciclo, string $tipo, array $fileUpload): array {
        $rutaDestino = self::getRutaCarpeta($ciclo, $tipo);

        // Crear la estructura de carpetas si no existe
        if (!is_dir($rutaDestino)) {
            if (!mkdir($rutaDestino, 0755, true)) {
                return ['success' => false, 'error' => 'No se pudo crear la carpeta destino en el servidor.'];
            }
        }

        // Sanear el nombre del archivo original
        $nombreOriginal = basename($fileUpload['name']);
        $nombreSeguro   = preg_replace('/[^A-Za-z0-9._\-]/', '_', $nombreOriginal);
        if (empty($nombreSeguro)) {
            $nombreSeguro = 'documento_' . time();
        }

        $destinoCompleto = $rutaDestino . $nombreSeguro;

        // Si ya existe, le metemos un sufijo de tiempo para no machacarlo
        if (file_exists($destinoCompleto)) {
            $info = pathinfo($nombreSeguro);
            $ext  = isset($info['extension']) ? '.' . $info['extension'] : '';
            $nombreSeguro = $info['filename'] . '_' . time() . $ext;
            $destinoCompleto = $rutaDestino . $nombreSeguro;
        }

        // Mover archivo temporal al destino definitivo
        if (move_uploaded_file($fileUpload['tmp_name'], $destinoCompleto)) {
            return ['success' => true, 'nombre' => $nombreSeguro];
        }

        return ['success' => false, 'error' => 'Error interno al mover el fichero al destino.'];
    }
}