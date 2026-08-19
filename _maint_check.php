<?php
if (($_GET['k'] ?? '') !== 'km9f3x2v1q') { http_response_code(404); exit; }
clearstatcache(true);
$f = __DIR__ . '/coleta.php';
$c = @file_get_contents($f);
header('Content-Type: application/json');
echo json_encode([
  'dir' => __DIR__,
  'mtime' => date('c', @filemtime($f)),
  'size' => @filesize($f),
  'md5' => md5((string)$c),
  'tem_amarelo' => (int)(strpos((string)$c, 'alert-warning mt-2') !== false),
  'tem_form_text' => (int)(strpos((string)$c, 'form-text') !== false),
  'versao' => substr((string)$c, 0, 120),
  'opcache_reset' => function_exists('opcache_reset') ? opcache_reset() : null,
]);
