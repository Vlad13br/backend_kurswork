<?php
require '../core/Router.php';

$router = new Router();

$router->direct($_SERVER['REQUEST_URI']);
