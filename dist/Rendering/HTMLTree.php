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
		$this->setLeafObjectClass( '\\hollodotme\\TreeMDown\\Rendering\\HTMLLeaf' );
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

		$a = $dom->createElement( 'a', $this->filename );
		$a->setAttribute( 'href', 'javascript:void(0);' );
		$a->setAttribute( 'data-filepath', $this->filepath );
		$a->setAttribute( 'data-level', $this->_nesting_level );
		$a->setAttribute( 'data-filename', $this->filename );
		$a->setAttribute( 'data-active', $this->active ? '1' : '0' );

		$div->appendChild( $a );

		$ul = $dom->createElement( 'ul' );
		$ul->setAttribute( 'class', 'tree' );

		foreach ( $this->_entries as $entry )
		{
			$li_entry = $dom->createElement( 'li' );
			$li_entry->setAttribute( 'class', 'tree-entry' );

			/** @var HTMLLeaf $entry */
			$li_entry->appendChild( $dom->importNode( $entry->getOutput(), true ) );

			$ul->appendChild( $li_entry );
		}

		$div->appendChild( $ul );
		$dom->appendChild( $div );

		return $dom->documentElement;
	}
}
