<?php declare(strict_types=1);
/**
 * Documentation of TreeMDown
 * @author hollodotme
 */

error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

require_once __DIR__ . '/../vendor/autoload.php';

$tmd = new \hollodotme\TreeMDown\TreeMDown( __DIR__ . '/TreeMDown' );
$tmd->setDefaultFile( '01-What-Is-TreeMDown.md' );
$tmd->enablePrettyNames();
$tmd->hideFilenameSuffix();
$tmd->display();
