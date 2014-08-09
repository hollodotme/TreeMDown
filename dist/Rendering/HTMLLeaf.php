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

		if ( !empty($this->error) )
		{
			$a = $dom->createElement( 'span', $this->error );
			$a->setAttribute( 'class', 'small text-danger' );
		}
		else
		{
			$a = $dom->createElement( 'a', $this->filename );

			$url_query = http_build_query(
				array(
					's' => $this->_search_filter,
					'f' => $this->getFilePath( true ),
				)
			);

			$a->setAttribute( 'href', '?' . $url_query );
			$a->setAttribute( 'data-filepath', $this->getFilePath( true ) );
			$a->setAttribute( 'data-level', $this->_nesting_level );
			$a->setAttribute( 'data-filename', $this->filename );
			$a->setAttribute( 'data-active', $this->active ? '1' : '0' );

			if ( $this->active )
			{
				$a->setAttribute( 'style', 'font-weight: bold; font-style: italic' );
			}
		}

		$dom->appendChild( $a );

		return $dom->documentElement;
	}
}
