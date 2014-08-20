<?php
/**
 * Tree class for HTML rendering
 *
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering;

use hollodotme\TreeMDown\FileSystem;

/**
 * Class HTMLTree
 *
 * @package hollodotme\TreeMDown\Rendering
 */
class HTMLTree extends FileSystem\Tree
{
	/**
	 * Constructor
	 *
	 * @param FileSystem\Search $search   Search
	 * @param string            $filepath Filepath
	 */
	public function __construct( FileSystem\Search $search, $filepath = null )
	{
		parent::__construct( $search, $filepath );
		$this->setLeafObjectClass( __NAMESPACE__ . '\\HTMLLeaf' );
	}

	/**
	 * Return a string representation of this instance
	 *
	 * @return \DOMElement
	 */
	public function getOutput()
	{
		$dom = new \DOMDocument( '1.0', 'UTF-8' );

		$div = $dom->createElement( 'div' );
		$div->setAttribute( 'class', 'tmd-tree' );

		$subtree_id = md5( $this->getFilePath( true ) );

		$occurences = $this->getOccurencesInSearch();
		$is_active  = ($this->isActive() || $occurences > 0);

		$a = $dom->createElement( 'a' );
		$a->setAttribute( 'href', 'javascript:void(0);' );
		$a->setAttribute( 'data-filepath', $this->getFilePath( true ) );
		$a->setAttribute( 'data-level', $this->_nesting_level );
		$a->setAttribute( 'data-filename', $this->_filename );
		$a->setAttribute( 'data-active', $this->isActive() ? '1' : '0' );
		$a->setAttribute( 'data-subtree-id', $subtree_id );
		$a->setAttribute( 'class', 'tmd-folder-link' . ($is_active ? ' active' : '') );
		$div->appendChild( $a );

		$glyph = $dom->createElement( 'span' );
		$glyph->setAttribute( 'class', 'glyphicon glyphicon-folder-' . ($is_active ? 'open' : 'close') );
		$a->appendChild( $glyph );

		$glyph_text = $dom->createTextNode( '' );
		$glyph->appendChild( $glyph_text );

		$link_text = $dom->createElement( 'span', $this->_filename );
		$a->appendChild( $link_text );

		if ( $this->_search->isActive() )
		{
			// Badge
			$badge = $dom->createElement( 'span', $occurences );
			$badge->setAttribute( 'class', 'badge pull-right' . ($occurences > 0 ? ' active' : '') );
			$a->appendChild( $badge );
		}

		$hide_sub_entries = (($this->_nesting_level > 0) && !$is_active);

		$ul = $dom->createElement( 'ul' );
		$ul->setAttribute( 'class', 'tmd-tree-list' );
		$ul->setAttribute( 'style', 'display: ' . ($hide_sub_entries ? 'none' : 'block') );
		$ul->setAttribute( 'id', $subtree_id );

		foreach ( $this->_entries as $entry )
		{
			$li_entry = $dom->createElement( 'li' );
			$li_entry->setAttribute( 'class', 'tmd-tree-entry' );

			/** @var HTMLLeaf $entry */
			$li_entry->appendChild( $dom->importNode( $entry->getOutput(), true ) );

			$ul->appendChild( $li_entry );
		}

		$div->appendChild( $ul );
		$dom->appendChild( $div );

		return $dom->documentElement;
	}
}
