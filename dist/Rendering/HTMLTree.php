<?php
/**
 * Tree class for HTML rendering
 *
 * @author h.woltersdorf
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
	 * @param string $filepath
	 * @param int    $nesting_level
	 */
	public function __construct( $filepath, $nesting_level = 0 )
	{
		parent::__construct( $filepath, $nesting_level );
		$this->setLeafObjectClass( __NAMESPACE__ . '\\HTMLLeaf' );
	}

	/**
	 * Return a string representation of this instance
	 *
	 * @return string
	 */
	public function getOutput()
	{
		$dom = new \DOMDocument( '1.0', 'UTF-8' );

		$div = $dom->createElement( 'div' );

		$subtree_id = md5( $this->getFilePath( true ) );

		$a = $dom->createElement( 'a', $this->filename );
		$a->setAttribute( 'href', 'javascript:void(0);' );
		$a->setAttribute( 'data-filepath', $this->getFilePath( true ) );
		$a->setAttribute( 'data-level', $this->_nesting_level );
		$a->setAttribute( 'data-filename', $this->filename );
		$a->setAttribute( 'data-active', $this->active ? '1' : '0' );
		$a->setAttribute( 'onclick', "$('#{$subtree_id}').toggle();" );

		if ( $this->active )
		{
			$a->setAttribute( 'style', 'font-weight: bold;' );
		}

		$div->appendChild( $a );

		$hide_sub_entries = (($this->_nesting_level > 0) && !$this->active);

		$ul = $dom->createElement( 'ul' );
		$ul->setAttribute( 'class', 'tmd-tree' );
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
