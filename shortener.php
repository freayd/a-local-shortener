<?php

echo "<h1>Shortener: {$_SERVER['REQUEST_URI']}</h1>";

$ini_array = parse_ini_file(dirname(__FILE__).'/shortener.ini', true);

echo '<pre>';
var_dump($ini_array);
echo '</pre>';
