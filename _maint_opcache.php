<?php
// TEMPORARIO - reset de OPcache pos-deploy. Removido apos uso.
if (($_GET['k'] ?? '') !== 'km_reset_9f3a2b71') { http_response_code(404); exit; }
header('Content-Type: application/json');
$out = ['has_status' => function_exists('opcache_get_status')];
if (function_exists('opcache_get_status')) {
    $st = @opcache_get_status(false);
    $out['enabled'] = is_array($st) ? ($st['opcache_enabled'] ?? null) : false;
    $out['validate_timestamps'] = @ini_get('opcache.validate_timestamps');
    $out['revalidate_freq'] = @ini_get('opcache.revalidate_freq');
}
$out['reset'] = function_exists('opcache_reset') ? opcache_reset() : 'no_func';
echo json_encode($out);
