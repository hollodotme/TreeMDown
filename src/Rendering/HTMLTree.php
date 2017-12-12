<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering;

use hollodotme\TreeMDown\FileSystem\Search;
use hollodotme\TreeMDown\FileSystem\Tree;

/**
 * Class HTMLTree
 * @package hollodotme\TreeMDown\Rendering
 */
class HTMLTree extends Tree
{
	public function __construct( Search $search, string $filepath = '' )
	{
		parent::__construct( $search, $filepath );
		$this->setLeafObjectClass( HTMLLeaf::class );
	}

	public function getOutput()
	{
		$dom = new \DOMDocument( '1.0', 'UTF-8' );

		$div = $dom->createElement( 'div' );
		$div->setAttribute( 'class', 'tmd-tree' );

		$subtreeId = md5( $this->getFilePath( true ) );

		$occurences = $this->getOccurrencesInSearch();
		$isActive   = ($this->isActive() || $occurences > 0);

		$a = $dom->createElement( 'a' );
		$a->setAttribute( 'href', 'javascript:void(0);' );
		$a->setAttribute( 'data-filepath', $this->getFilePath( true ) );
		$a->setAttribute( 'data-level', (string)$this->nestingLevel );
		$a->setAttribute( 'data-filename', $this->basename );
		$a->setAttribute( 'data-active', $this->isActive() ? '1' : '0' );
		$a->setAttribute( 'data-subtree-id', $subtreeId );
		$a->setAttribute( 'class', 'tmd-folder-link' . ($isActive ? ' active' : '') );
		$div->appendChild( $a );

		$glyph = $dom->createElement( 'span' );
		$glyph->setAttribute( 'class', 'glyphicon glyphicon-folder-' . ($isActive ? 'open' : 'close') );
		$a->appendChild( $glyph );

		$glyphText = $dom->createTextNode( '' );
		$glyph->appendChild( $glyphText );

		$linkText = $dom->createElement( 'span', $this->getDisplayFilename() );
		$a->appendChild( $linkText );

		if ( $this->search->isActive() )
		{
			// Badge
			$badge = $dom->createElement( 'span', (string)$occurences );
			$badge->setAttribute( 'class', 'badge pull-right' . ($occurences > 0 ? ' active' : '') );
			$a->appendChild( $badge );
		}

		$hideSubEntries = (($this->nestingLevel > 0) && !$isActive);

		$ul = $dom->createElement( 'ul' );
		$ul->setAttribute( 'class', 'tmd-tree-list' );
		$ul->setAttribute( 'style', 'display: ' . ($hideSubEntries ? 'none' : 'block') );
		$ul->setAttribute( 'id', $subtreeId );

		foreach ( $this->entries as $entry )
		{
			$liEntry = $dom->createElement( 'li' );
			$liEntry->setAttribute( 'class', 'tmd-tree-entry' );

			/** @var HTMLLeaf $entry */
			$liEntry->appendChild( $dom->importNode( $entry->getOutput(), true ) );

			$ul->appendChild( $liEntry );
		}

		$div->appendChild( $ul );
		$dom->appendChild( $div );

		return $dom->documentElement;
	}
}
