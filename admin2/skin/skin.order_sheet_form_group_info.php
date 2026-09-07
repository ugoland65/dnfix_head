<?php

use App\Classes\Request;
use App\Controllers\Admin\OrderGroupController;
use App\Core\View;

$controller = new OrderGroupController();
$view = $controller->formGroupProductPage(new Request());
if ($view instanceof View) {
	echo $view->render();
	return;
}

if (is_object($view) && method_exists($view, 'render')) {
	echo $view->render();
}
