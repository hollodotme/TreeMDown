<?php
/**
 * Leaf class for HTML rendering
 *
 * @author h.woltersdorf
 */

namespace hollodotme\TreeMDown\Rendering;

use hollodotme\TreeMDown\FileSystem;

/**
 * Class HTMLLeaf
 *
 * @package hollodotme\TreeMDown\Rendering
 */
class HTMLLeaf extends FileSystem\Leaf
{
	/**
	 * @return \DOMElement
	 */
	public function getOutput()
	{
		$dom = new \DOMDocument( '1.0', 'UTF-8' );
		$a   = $dom->createElement( 'a', $this->error ?: $this->filename );
		$a->setAttribute( 'href', '#' );
		$a->setAttribute( 'data-filepath', $this->filepath );
		$a->setAttribute( 'data-level', $this->_nesting_level );
		$a->setAttribute( 'data-filename', $this->filename );
		$a->setAttribute( 'data-active', $this->active ? '1' : '0' );

		$dom->appendChild($a);

		return $dom->documentElement;
	}
}
