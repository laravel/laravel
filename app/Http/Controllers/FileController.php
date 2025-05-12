<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FileController extends Controller
{

    public static $AUTODESTROY_DIR = "/autodestroy";
    public static $TEMPFILE_DURATION_HOURS = 168;

    public static function getTempFileDurationText()
    {
        // Calcular días y horas restantes
        $days = floor(FileController::$TEMPFILE_DURATION_HOURS / 24);
        $hours = FileController::$TEMPFILE_DURATION_HOURS % 24;
        //dd($days, $hours);

        // Formatear el texto según la cantidad
        $daystext = $days == 1 ? 'día' : 'días';
        $hourstext = $hours == 1 ? 'hora' : 'horas';

        $text = "$days $daystext";
        if ($hours > 0)
            $text .= " y $hours $hourstext";
        return $text;
    }

    public function renderAndDestroy($format, $name)
    {
        $this->deleteOldTempFiles(public_path() . FileController::$AUTODESTROY_DIR);

        $report_path = public_path() . FileController::$AUTODESTROY_DIR . "/{$name}.{$format}";

        // Verificar si el archivo existe
        if (!file_exists($report_path)) {
            return response()->json([
                "code" => "404",
                "error" => "El reporte {$name} no existe: seguramente han pasado ya las " . FileController::$TEMPFILE_DURATION_HOURS . " desde que fue creado.",
            ], 404);
        }

        return response()->download($report_path); //->deleteFileAfterSend(true);
    }

    public function deleteOldTempFiles($dir)
    {
        // Obtener la lista de files en el dir
        $files = File::allFiles($dir);

        // Calcular el tiempo límite en segundos
        $limit = time() - (FileController::$TEMPFILE_DURATION_HOURS * 60 * 60);

        foreach ($files as $file) {
            // Obtener el nombre del file sin la ruta
            $filename = basename($file);

            // Extraer el timestamp del nombre del file
            $timestamp = (int) pathinfo($filename, PATHINFO_FILENAME);

            // Comparar el timestamp con el límite de tiempo
            if ($timestamp < $limit) {
                // Eliminar el file si es más antiguo que el límite de tiempo
                File::delete($file);
            }
        }
    }

    public function createFileByContent($extension, $contents, $folder)
    {
        // Ruta donde se almacenará el archivo en la carpeta public
        $path = public_path() . "/{$folder}/" . date("Y") . "/" . date("m") . "/" . date("d");
        // Si la carpeta no existe, crearla
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $name = time();
        if ($extension != "") {
            $name .= "." . $extension;
        }

        $full_path = $path . "/" . $name;
        file_put_contents($full_path, $contents);

        return $full_path;
    }

    public function readLog($type = false, $amount = false, $log = "laravel")
    {
        $logFile = storage_path("logs/{$log}.log");

        if (!file_exists($logFile)) {
            return response()->json(['error' => 'Log file not found'], 404);
        }

        $amount = $amount ?: 10;
        $pattern = '/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] local\.' . strtoupper(strtolower($type)) . ':/m';
        $results = [];

        // Leer todo el archivo y procesarlo
        $content = file_get_contents($logFile);
        $entries = explode('[20', $content); // Separar por nuevas entradas de log

        // Procesar cada entrada desde la más reciente
        $entries = array_reverse($entries);

        foreach ($entries as $entry) {
            if (empty(trim($entry)))
                continue;

            // Reconstruir la fecha que usamos para separar
            $entry = '[20' . $entry;

            // Verificar si es del tipo solicitado
            if ($pattern && !preg_match($pattern, $entry)) {
                continue;
            }

            // Extraer el stacktrace si existe
            $stacktrace = [];
            if (preg_match('/(\[stacktrace\]|Stack trace:)(.*?)(\n\n|\Z)/s', $entry, $matches)) {
                $stacktraceLines = explode("\n", trim($matches[2]));
                $stacktrace = array_slice(array_filter($stacktraceLines), 0, 5);
            }

            // Extraer la primera línea como mensaje principal
            $firstLine = strtok($entry, "\n");

            $results[] = [
                'main' => $firstLine,
                'stacktrace' => $stacktrace
            ];

            if (count($results) >= $amount) {
                break;
            }
        }

        dd($results);
    }

    public function clearLog($log = "laravel")
    {
        // Ruta del archivo de log
        $logFile = storage_path("logs/{$log}.log");

        // Verificar si el archivo existe
        if (!file_exists($logFile)) {
            return response()->json(["error" => "'{$log}' log file not found"], 404);
        }

        // Truncar el archivo para dejarlo vacío
        file_put_contents($logFile, '');

        die(date("Y-m-d H:i:s") . ": Logs limpiados correctamente!");
    }

    public function searchInLog($key, $searchValue, $log = "storage", $exactMatch = true)
    {
        $logFile = storage_path("logs/{$log}.log");

        if (!File::exists($logFile)) {
            return response()->json(['error' => 'Log file not found'], 404);
        }

        $results = [];
        $searchValue = preg_quote($searchValue, '/');
        $pattern = $exactMatch
            ? '/\[.*?\] local\.INFO: ' . preg_quote($key, '/') . ' \{.*?(?<!\w)' . $searchValue . '(?!\w).*?\}/i'
            : '/\[.*?\] local\.INFO: ' . preg_quote($key, '/') . ' \{.*?' . $searchValue . '.*?\}/i';

        foreach ($this->readLogFile($logFile) as $line) {
            if (preg_match($pattern, $line)) {
                try {
                    $jsonString = substr($line, strpos($line, '{'));
                    $data = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);
                    $results[] = $data;
                } catch (\JsonException $e) {
                    //Log::error("JSON decode error in log search: " . $e->getMessage());
                }
            }
        }

        return $results;
    }

    private function readLogFile($path)
    {
        $handle = fopen($path, 'r');

        try {
            while (($line = fgets($handle)) !== false) {
                yield $line;
            }
        } finally {
            fclose($handle);
        }
    }

}
