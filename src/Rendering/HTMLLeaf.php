<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering;

use hollodotme\TreeMDown\FileSystem;
use hollodotme\TreeMDown\Misc\Opt;

/**
 * Class HTMLLeaf
 * @package hollodotme\TreeMDown\Rendering
 */
class HTMLLeaf extends FileSystem\Leaf
{
	public function getOutput() : \DOMElement
	{
		$dom = new \DOMDocument( '1.0', 'UTF-8' );

		if ( !empty( $this->_error ) )
		{
			$a = $dom->createElement( 'span', $this->_error );
			$a->setAttribute( 'class', 'small text-danger' );

			$dom->appendChild( $a );

			return $dom->documentElement;
		}

		$a = $dom->createElement( 'a' );

		$urlQuery = http_build_query(
			array_merge(
				$this->getOptions()->get( Opt::BASE_PARAMS ),
				['tmd_f' => $this->getFilePath( true )]
			)
		);

		$a->setAttribute( 'href', '?' . $urlQuery );
		$a->setAttribute( 'data-filepath', $this->getFilePath( true ) );
		$a->setAttribute( 'data-level', $this->_nesting_level );
		$a->setAttribute( 'data-filename', $this->_filename );
		$a->setAttribute( 'data-active', $this->isActive() ? '1' : '0' );
		$a->setAttribute( 'class', 'tmd-tree-leaf' . ($this->isActive() ? ' active' : '') );

		$glyph = $dom->createElement( 'span' );
		$glyph->setAttribute( 'class', 'glyphicon glyphicon-file' );
		$a->appendChild( $glyph );

		$glyphText = $dom->createTextNode( '' );
		$glyph->appendChild( $glyphText );

		$linkText = $dom->createElement( 'span', $this->getDisplayFilename() );
		$a->appendChild( $linkText );

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

		$dom->appendChild( $a );

		return $dom->documentElement;
	}
}
