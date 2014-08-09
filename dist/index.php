<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\TreeMDown;

use hollodotme\TreeMDown\Rendering\HTMLPage;

error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/FileSystem/Entry.php';
require_once __DIR__ . '/FileSystem/Tree.php';
require_once __DIR__ . '/FileSystem/Leaf.php';
require_once __DIR__ . '/Rendering/HTMLLeaf.php';
require_once __DIR__ . '/Rendering/HTMLTree.php';
require_once __DIR__ . '/Rendering/HTMLPage.php';

header( 'Content-type: text/html' );

$tree = new Rendering\HTMLTree( '/var/www/TreeMDown/doc' );
$tree->setCurrentFile( isset($_GET['f']) ? $_GET['f'] : 'index.md' );
$tree->setFileFilter( "#\.md$#" );
$tree->setSearchFilter( isset($_GET['q']) ? $_GET['q'] : '' );
$tree->buildTree();

// Page

$page = new HTMLPage( $tree );
$page->setCompany( 'hollodotme' );
$page->setProjectName( 'TreeMDown' );
$page->setShortDescription( "[triː <'em> daʊn]" );

echo $page->getOutput();
