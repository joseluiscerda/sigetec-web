<?php
// Copie este archivo como config.php (no se versiona) y complete los valores.
return [
  'destino' => 'contacto@sigetec.cl',
  'remitente' => 'web@sigetec.cl',     // debe existir como cuenta o alias en cPanel para que mail() no se marque como spam
  'brevo_api_key' => '',               // opcional: clave de API de Brevo; si está vacía se usa mail() de PHP
];
