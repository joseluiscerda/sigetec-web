<?php
/**
 * Receptor del formulario de contacto para hosting cPanel (PHP 8.x).
 * Recibe JSON por POST, valida, y envía el mensaje por correo (mail() o Brevo API si hay clave).
 * La URL pública es /api/contacto (ver .htaccess).
 */
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

$config = ['destino' => 'contacto@sigetec.cl', 'remitente' => 'web@sigetec.cl', 'brevo_api_key' => '', 'origen_permitido' => 'https://sigetec.cl'];
if (is_file(__DIR__ . '/config.php')) {
  $config = array_merge($config, require __DIR__ . '/config.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'error' => 'method']);
  exit;
}
$raw = file_get_contents('php://input');
$d = json_decode($raw ?: '', true);
if (!is_array($d)) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'json']);
  exit;
}
// Honeypot y validaciones mínimas
if (!empty($d['sitio_web'])) { echo json_encode(['ok' => true]); exit; }
$limpio = fn($k, $max = 500) => mb_substr(trim(strip_tags((string)($d[$k] ?? ''))), 0, $max);
$nombre = $limpio('nombre', 120);
$empresa = $limpio('empresa', 120);
$email = $limpio('email', 160);
$telefono = $limpio('telefono', 40);
$motivo = $limpio('motivo', 40);
$erp = $limpio('erp', 80);
$mensaje = $limpio('mensaje', 4000);
$detalle = $limpio('detalle', 600);
$origen = $limpio('origen', 300);
$canales = is_array($d['canales'] ?? null) ? implode(', ', array_map(fn($c) => mb_substr(strip_tags((string)$c), 0, 40), $d['canales'])) : '';
if ($nombre === '' || $empresa === '' || $mensaje === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(422);
  echo json_encode(['ok' => false, 'error' => 'campos']);
  exit;
}
// Límite simple de frecuencia por IP (10 por hora) usando el directorio temporal
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$f = sys_get_temp_dir() . '/sigetec_contacto_' . md5($ip);
$hits = is_file($f) ? array_filter(array_map('intval', file($f, FILE_IGNORE_NEW_LINES)), fn($t) => $t > time() - 3600) : [];
if (count($hits) >= 10) { http_response_code(429); echo json_encode(['ok' => false, 'error' => 'rate']); exit; }
$hits[] = time();
@file_put_contents($f, implode("\n", $hits));

$asunto = "Solicitud web: {$motivo} — {$empresa}";
$cuerpo = "Nombre: {$nombre}\nEmpresa: {$empresa}\nCorreo: {$email}\nTeléfono: {$telefono}\nMotivo: {$motivo}\nERP: {$erp}\nCanales: {$canales}\nDetalle cotización: {$detalle}\nOrigen: {$origen}\nIP: {$ip}\n\nMensaje:\n{$mensaje}\n";

$enviado = false;
if ($config['brevo_api_key'] !== '') {
  $payload = json_encode([
    'sender' => ['name' => 'Sitio sigetec.cl', 'email' => $config['remitente']],
    'to' => [['email' => $config['destino']]],
    'replyTo' => ['email' => $email, 'name' => $nombre],
    'subject' => $asunto,
    'textContent' => $cuerpo,
  ]);
  $ch = curl_init('https://api.brevo.com/v3/smtp/email');
  curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $payload, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10,
    CURLOPT_HTTPHEADER => ['accept: application/json', 'content-type: application/json', 'api-key: ' . $config['brevo_api_key']]]);
  curl_exec($ch);
  $enviado = ((int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE)) < 300;
  curl_close($ch);
}
if (!$enviado) {
  $headers = "From: Sitio sigetec.cl <{$config['remitente']}>\r\nReply-To: {$nombre} <{$email}>\r\nContent-Type: text/plain; charset=UTF-8\r\n";
  $enviado = @mail($config['destino'], '=?UTF-8?B?' . base64_encode($asunto) . '?=', $cuerpo, $headers);
}
// Respaldo en archivo por si el correo falla (fuera de public_html si es posible)
@file_put_contents(__DIR__ . '/../../contactos.log', date('c') . "\t" . json_encode($d, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);

if (!$enviado) { http_response_code(500); echo json_encode(['ok' => false, 'error' => 'envio']); exit; }
echo json_encode(['ok' => true]);
