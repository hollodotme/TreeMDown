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
	/**
	 * Constructor
	 *
	 * @param Search      $search   Search
	 * @param null|string $filepath Filepath
	 */
	public function __construct( Search $search, ?string $filepath = null )
	{
		parent::__construct( $search, $filepath );
		$this->setLeafObjectClass( __NAMESPACE__ . '\\HTMLLeaf' );
	}

	public function getOutput() : \DOMElement
	{
		$dom = new \DOMDocument( '1.0', 'UTF-8' );

		$div = $dom->createElement( 'div' );
		$div->setAttribute( 'class', 'tmd-tree' );

		$subtreeId = md5( $this->getFilePath( true ) );

		$occurences = $this->getOccurencesInSearch();
		$isActive   = ($this->isActive() || $occurences > 0);

		$a = $dom->createElement( 'a' );
		$a->setAttribute( 'href', 'javascript:void(0);' );
		$a->setAttribute( 'data-filepath', $this->getFilePath( true ) );
		$a->setAttribute( 'data-level', $this->_nesting_level );
		$a->setAttribute( 'data-filename', $this->_filename );
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

		if ( $this->_search->isActive() )
		{
			// Badge
			$badge = $dom->createElement( 'span', $occurences );
			$badge->setAttribute( 'class', 'badge pull-right' . ($occurences > 0 ? ' active' : '') );
			$a->appendChild( $badge );
		}

		$hideSubEntries = (($this->_nesting_level > 0) && !$isActive);

		$ul = $dom->createElement( 'ul' );
		$ul->setAttribute( 'class', 'tmd-tree-list' );
		$ul->setAttribute( 'style', 'display: ' . ($hideSubEntries ? 'none' : 'block') );
		$ul->setAttribute( 'id', $subtreeId );

		foreach ( $this->_entries as $entry )
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
