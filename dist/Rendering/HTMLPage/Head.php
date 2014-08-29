<?php
/**
 * Header of HTMLPage
 *
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

use hollodotme\TreeMDown\Misc\Opt;
use hollodotme\TreeMDown\Rendering\HTMLPage;

/**
 * Class Header
 *
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class Head extends AbstractSection
{

	/**
	 * Prepared assets
	 *
	 * @var array
	 */
	protected $_prepared_assets = array();

	/**
	 * Prepare the content
	 */
	public function prepare()
	{
		/** @var \DOMDocument $dom */
		$search                 = array();
		$replace                = array();
		$this->_prepared_assets = array();

		// Get contents of file
		foreach ( $this->getAssets( HTMLPage::ASSET_FONT ) as $font_file )
		{
			$filename     = basename( $font_file );
			$file_path    = realpath( $font_file );
			$file_content = base64_encode( file_get_contents( $file_path ) );

			$matches = array();
			preg_match( "#\.([^\.]+)$#", $filename, $matches );

			switch ( $matches[1] )
			{
				case 'eot':
				case 'ttf':
				case 'woff':
					$search[]  = sprintf( 'url(../fonts/%s', $filename );
					$replace[] = sprintf( 'url(data:font/%s;base64,%s', $matches[1], $file_content );
					break;

				case 'svg':
					$search[]  = sprintf( 'url(../fonts/%s', $filename );
					$replace[] = sprintf( 'url(data:image/svg+xml;base64,%s', $matches[1], $file_content );
					break;
			}
		}

		foreach ( $this->getAssets( HTMLPage::ASSET_CSS ) as $css_file )
		{
			$file_content = file_get_contents( $css_file );
			$file_content = str_replace( $search, $replace, $file_content );

			$this->_prepared_assets[ HTMLPage::ASSET_CSS ][] = $file_content;
		}
	}

	/**
	 * Add nodes to DOM
	 */
	public function addNodes()
	{
		$tree = $this->getTree();
		$head = $this->getDom()->createElement( 'head' );

		// Title
		$title = $this->getOptions()->get( Opt::NAME_PROJECT );
		if ( $tree->getSearch()->isCurrentFileValid() )
		{
			$title = basename( $tree->getSearch()->getCurrentFile() ) . ' - ' . $title;
		}

		// Charset
		$head->appendChild( $this->getElementWithAttributes( 'meta', array( 'charset' => 'UTF-8' ) ) );

		// Description
		$head->appendChild(
			$this->getElementWithAttributes(
				'meta', array(
					'name'    => 'description',
					'content' => $this->getOptions()->get( Opt::PROJECT_ABSTRACT ),
				)
			)
		);

		// Viewport
		$head->appendChild(
			$this->getElementWithAttributes(
				'meta', array(
					'name'    => 'viewport',
					'contetn' => 'width=device-width, initial-scale=1.0',
				)
			)
		);

		// Title
		$head->appendChild( $this->getDom()->createElement( 'title', $title ) );

		// Add prepared assets
		foreach ( $this->_prepared_assets[ HTMLPage::ASSET_CSS ] as $css_content )
		{
			$elem = $this->getElementWithAttributes( 'style', array( 'type' => 'text/css' ) );

			$cdata = $this->getDom()->createCDATASection( $css_content );

			$elem->appendChild( $cdata );
			$head->appendChild( $elem );
		}

		$this->getContainer()->appendChild( $head );
	}
}
