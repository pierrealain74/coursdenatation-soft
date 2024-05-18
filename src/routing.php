<?php
use App\Controllers\HomeController;

$urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$homeController = new HomeController();


if ('/' === $urlPath) {

    $homeController->showRevenue();

}
 else {
    header('HTTP/1.1 404 Not Found');
}
