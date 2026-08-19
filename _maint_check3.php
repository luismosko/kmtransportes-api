<?php
if (($_GET['k'] ?? '') !== 'km9f3x2v1q') { http_response_code(404); exit; }
clearstatcache(true);
header('Content-Type: application/json');
$res = [];
foreach (glob(__DIR__ . '/*.php') as $f) {
    $c = (string)@file_get_contents($f);
    if (strpos($c, 'alert-warning mt-2') !== false) {
        $res['tem_amarelo'][] = ['file' => $f, 'mtime' => date('c', filemtime($f)), 'size' => filesize($f)];
    }
}
$res['coleta_md5'] = md5((string)@file_get_contents(__DIR__ . '/coleta.php'));
$res['docroot'] = $_SERVER['DOCUMENT_ROOT'] ?? null;
$res['script'] = $_SERVER['SCRIPT_FILENAME'] ?? null;
$res['self_dir'] = __DIR__;
$res['ls'] = array_map('basename', glob(__DIR__ . '/*'));
echo json_encode($res, JSON_PRETTY_PRINT);
