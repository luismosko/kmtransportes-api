<?php
if (($_GET['k'] ?? '') !== 'km9f3x2v1q') { http_response_code(404); exit; }
header('X-LiteSpeed-Purge: *');
header('Content-Type: text/plain');
echo "purge enviado\n";
