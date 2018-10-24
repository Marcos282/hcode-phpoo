<?php 

session_start();

require_once("vendor/autoload.php");
require_once("./config.php");
require_once("./helper.php");


use Hcode\DB\Sql;
use Hcode\Page;
use Hcode\PageAdmin;
use Hcode\Model\User;
use Hcode\Model\Category;


$app = new \Slim\Slim();

# REQUIRE DE ROTAS
require_once ("./route-site.php");
require_once ("./route-admin-user.php");
require_once ("./route-admin-category.php");
require_once ("./route-admin-products.php");

$app->config('debug', true); 



$app->run();

 ?>