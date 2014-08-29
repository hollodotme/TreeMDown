<?php
/**
 * Leaf class for HTML rendering
 *
 * @author hollodotme
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

		if ( !empty($this->_error) )
		{
			$a = $dom->createElement( 'span', $this->_error );
			$a->setAttribute( 'class', 'small text-danger' );
		}
		else
		{
			$a = $dom->createElement( 'a' );

			$url_query = http_build_query(
				array(
					'tmd_q' => $this->_search->getSearchTerm(),
					'tmd_f' => $this->getFilePath( true ),
				)
			);

			$a->setAttribute( 'href', '?' . $url_query );
			$a->setAttribute( 'data-filepath', $this->getFilePath( true ) );
			$a->setAttribute( 'data-level', $this->_nesting_level );
			$a->setAttribute( 'data-filename', $this->_filename );
			$a->setAttribute( 'data-active', $this->isActive() ? '1' : '0' );
			$a->setAttribute( 'class', 'tmd-tree-leaf' . ($this->isActive() ? ' active' : '') );

			$glyph = $dom->createElement( 'span' );
			$glyph->setAttribute( 'class', 'glyphicon glyphicon-file' );
			$a->appendChild( $glyph );

			$glyph_text = $dom->createTextNode( '' );
			$glyph->appendChild( $glyph_text );

			$link_text = $dom->createElement( 'span', $this->getDisplayFilename() );
			$a->appendChild( $link_text );

			if ( $this->_search->isActive() )
			{
				// Badge
				$occurences = $this->getOccurencesInSearch();
				$badge      = $dom->createElement( 'span', $occurences );
				$badge->setAttribute(
					'class',
					'badge pull-right' . ($occurences > 0 ? ' active' : '')
				);
				$a->appendChild( $badge );
			}
		}

		$dom->appendChild( $a );

		return $dom->documentElement;
	}
}
