<?php
$idx = $_GET['idx'] ?? ($_idx ?? '');
$query = $idx !== '' ? ('?idx=' . rawurlencode((string)$idx)) : '';
echo '<script>location.replace(' . json_encode('/admin/stock/stock_excel' . $query) . ');</script>';
exit;
