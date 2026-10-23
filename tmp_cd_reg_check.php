<?php
require_once __DIR__ . '/application/Core/Autoloader.php';

use App\Classes\Database;
use App\Core\Autoloader;
use App\Services\ProductService;

Autoloader::register();

$config = require __DIR__ . '/application/config/config.php';
$remote = require __DIR__ . '/application/config/config.db-test.local.php';
$pdo = new PDO(
    'mysql:host=' . $remote['db_host'] . ';port=' . (int)$remote['db_port'] . ';dbname=' . $config['db_name'] . ';charset=utf8',
    $config['db_user'],
    $config['db_pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$databaseRef = new ReflectionClass(Database::class);
$database = $databaseRef->newInstanceWithoutConstructor();
$connProp = $databaseRef->getProperty('conn');
$connProp->setAccessible(true);
$connProp->setValue($database, $pdo);
$instanceProp = $databaseRef->getProperty('instance');
$instanceProp->setAccessible(true);
$instanceProp->setValue(null, $database);

$service = new ProductService();
$result = $service->backfillNullProductCdRegFromActionLog();

$row = $pdo->query("SELECT CD_IDX, cd_reg FROM COMPARISON_DB WHERE CD_IDX = 7841")->fetch(PDO::FETCH_ASSOC);
$decoded = json_decode((string)($row['cd_reg'] ?? ''), true);
echo json_encode([
    'result' => $result,
    'cd_idx' => (int)($row['CD_IDX'] ?? 0),
    'reg_name' => $decoded['reg']['info']['name'] ?? '',
    'reg_date' => $decoded['reg']['info']['date'] ?? '',
    'has_modify' => isset($decoded['modify']),
], JSON_UNESCAPED_UNICODE), PHP_EOL;
