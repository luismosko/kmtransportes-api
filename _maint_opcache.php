<?php
// TEMPORARIO - reset de OPcache pos-deploy (remover apos uso)
if (($_GET['k'] ?? '') !== 'km9f3x2v1q') { http_response_code(404); exit; }
$ok = function_exists('opcache_reset') ? opcache_reset() : null;
header('Content-Type: application/json');
echo json_encode(['reset' => $ok, 'coleta' => @filemtime(__DIR__.'/coleta.php')]);
