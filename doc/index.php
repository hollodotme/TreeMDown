<?php
/**
 * Created by PhpStorm.
 * User: hollodotme
 * Date: 27/10/14
 * Time: 21:55
 */

error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

require_once __DIR__ . '/../vendor/autoload.php';

$tmd = new \hollodotme\TreeMDown\TreeMDown( __DIR__ . '/TreeMDown' );
$tmd->setDefaultFile( '01-What-Is-TreeMDown.md' );
$tmd->enablePrettyNames();
$tmd->hideFilenameSuffix();
$tmd->display();