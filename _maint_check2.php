<?php
if (($_GET['k'] ?? '') !== 'km9f3x2v1q') { http_response_code(404); exit; }
clearstatcache(true);
$out = ['pid' => getmypid(), 'sapi' => PHP_SAPI, 'inval' => [], 'ini' => []];
foreach (['opcache.enable','opcache.validate_timestamps','opcache.revalidate_freq','opcache.file_cache'] as $k) {
    $out['ini'][$k] = ini_get($k);
}
foreach (glob(__DIR__ . '/*.php') as $f) {
    $out['inval'][basename($f)] = function_exists('opcache_invalidate') ? (int)opcache_invalidate($f, true) : null;
}
if (function_exists('opcache_get_status')) {
    $st = @opcache_get_status(true);
    $out['cached_coleta'] = isset($st['scripts'][__DIR__ . '/coleta.php'])
        ? ['timestamp' => date('c', $st['scripts'][__DIR__ . '/coleta.php']['timestamp'])] : 'nao cacheado';
    $out['num_scripts'] = $st['opcache_statistics']['num_cached_scripts'] ?? null;
}
header('Content-Type: application/json');
echo json_encode($out);
