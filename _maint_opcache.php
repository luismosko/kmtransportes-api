<?php
if (($_GET['k'] ?? '') !== 'km_reset_7c4e1a92') { http_response_code(404); exit; }
header('Content-Type: application/json');
echo json_encode(['reset' => function_exists('opcache_reset') ? opcache_reset() : 'no_func']);
