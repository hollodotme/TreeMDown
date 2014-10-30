<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\TreeMDown;

error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

use hollodotme\TreeMDown\FileSystem\Search;
use hollodotme\TreeMDown\Misc\DefaultOptions;
use hollodotme\TreeMDown\Misc\Opt;
use hollodotme\TreeMDown\Utilities\Linker;

require_once __DIR__ . '/../../vendor/autoload.php';

$options = new DefaultOptions();
$options->set(Opt::DIR_ROOT, __DIR__ . '/../../test/Unit/Fixures/LinkerTest');
$options->set(Opt::SEARCH_TERM, 'Suche was');
$linker = new Linker( new Search($options) );

$doc = new \DOMDocument( '1.0', 'UTF-8' );
$doc->loadHTMLFile( __DIR__ . '/../../test/Unit/Fixures/InternalLink_1.html' );

foreach ($linker->getInternalLinks( $doc ) as $link )
{
	$linker->modifyInternalLink($link);
	echo $link->getAttribute('href'), "\n";
}