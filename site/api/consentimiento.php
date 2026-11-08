<?php
/**
 * sigetec.cl — registro de consentimientos de cookies (prueba del consentimiento, Ley 21.719).
 *
 * Recibe por POST el JSON que envía el sitio al guardar una elección en el banner o en «Preferencias de cookies»:
 *   { id, version, fecha, analitica, publicidad, origen, pagina }
 * y agrega una línea a /home/<usuario>/consentimientos/consentimientos-AAAA-MM.csv (fuera de public_html).
 *
 * No guarda la dirección IP, el navegador ni ningún dato que identifique a la persona: solo el identificador
 * aleatorio que su navegador conserva en la cookie «sigetec_consentimiento», la hora del servidor y la elección.
 * Se despliega con el sitio (site/api/consentimiento.php); no toca contacto.php ni el .htaccess de api/.
 * Para desactivarlo: en config/site-config.js, consentLogUrl: "".
 */
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function responder(int $codigo, bool $ok): void
{
    http_response_code($codigo);
    echo $ok ? '{"ok":true}' : '{"ok":false}';
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    responder(405, false);
}

// Solo desde el propio sitio (el navegador envía Origin en POST con sendBeacon/fetch).
$origen = $_SERVER['HTTP_ORIGIN'] ?? '';
$host = $_SERVER['HTTP_HOST'] ?? '';
if ($origen !== '' && parse_url($origen, PHP_URL_HOST) !== preg_replace('/:\d+$/', '', $host)) {
    responder(403, false);
}

$crudo = file_get_contents('php://input', false, null, 0, 2048);
$d = json_decode((string) $crudo, true);
if (!is_array($d)) {
    responder(400, false);
}

$id = (string) ($d['id'] ?? '');
$version = (string) ($d['version'] ?? '');
$fecha = (string) ($d['fecha'] ?? '');
if (!preg_match('/^[A-Za-z0-9-]{8,40}$/', $id) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $version)) {
    responder(400, false);
}
if (!preg_match('/^[0-9T:.\-+Z]{10,35}$/', $fecha)) {
    $fecha = '';
}
$analitica = !empty($d['analitica']) ? 1 : 0;
$publicidad = !empty($d['publicidad']) ? 1 : 0;
$via = in_array($d['origen'] ?? '', ['banner', 'panel'], true) ? $d['origen'] : 'otro';
$pagina = substr((string) preg_replace('#[^A-Za-z0-9/._-]#', '', (string) ($d['pagina'] ?? '')), 0, 200);

// public_html/api/ → /home/<usuario>/consentimientos
$dir = dirname(__DIR__, 2) . '/consentimientos';
if (!is_dir($dir) && !@mkdir($dir, 0700, true)) {
    responder(500, false);
}
$archivo = $dir . '/consentimientos-' . gmdate('Y-m') . '.csv';
$nuevo = !is_file($archivo);
$fh = @fopen($archivo, 'ab');
if ($fh === false) {
    responder(500, false);
}
if (flock($fh, LOCK_EX)) {
    if ($nuevo) {
        fputcsv($fh, ['recibido_utc', 'id', 'version_politica', 'fecha_navegador', 'analitica', 'publicidad', 'origen', 'pagina'], ',', '"', '');
    }
    fputcsv($fh, [gmdate('Y-m-d\TH:i:s\Z'), $id, $version, $fecha, $analitica, $publicidad, $via, $pagina], ',', '"', '');
    fflush($fh);
    flock($fh, LOCK_UN);
}
fclose($fh);
@chmod($archivo, 0600);

responder(200, true);
