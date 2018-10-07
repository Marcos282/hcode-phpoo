<?php

require_once ("./vendor/autoload.php");

error_reporting(E_ALL & ~E_NOTICE);

// namespace
use Rain\Tpl;


// config
$config = array(
    "base_url"      => null,
    "tpl_dir"       => "tpl/",
    "cache_dir"     => "cache/",
    "remove_comments" => true,
    "debug"         => true, // set to false to improve the speed
);

Tpl::configure( $config );


// set variables
$var = array(
    "variable"  => "Hello World!",
    "bad_variable"  => "<script>alert('evil javascript here');</script>",
    "safe_variable"  => "<script>console.log('this is safe')</script>",
    "version"   => "3.0 Alpha",
    "menu"		=> array(
        array("name" => "Home", "link" => "index.php", "selected" => true ),
        array("name" => "FAQ", "link" => "index.php/FAQ/", "selected" => null ),
        array("name" => "Documentation", "link" => "index.php/doc/", "selected" => null )
    ),
    "week"		=> array( "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday" ),
    "user"		=> (object) array("name"=>"Rain", "citizen" => "Earth", "race" => "Human" ),
    "numbers"	=> array( 3, 2, 1 ),
    "bad_text"	=> 'Hey this is a malicious XSS <script>alert("auto_escape is always enabled");</script>',
    "table"		=> array( array( "Apple", "1996" ), array( "PC", "1997" ) ),
    "title"		=> "Rain TPL 3 - Easy and Fast template engine",
    "copyright" => "Copyright 2006 - 2012 Rain TPL<br>Project By Rain Team",

);

// draw
$tpl = new Tpl;
$tpl->assign( $var );
$tpl->assign( nome, "Marcos Sousa" );
$tpl->assign( versao, PHP_VERSION );


echo $tpl->draw( "index" );



class Test{
    static public function method( $variable ){
        echo "Hi I am a static method, and this is the parameter passed to me: $variable!";
    }
}

// end

?>