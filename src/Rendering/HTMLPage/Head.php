<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

use hollodotme\TreeMDown\Misc\Opt;
use hollodotme\TreeMDown\Rendering\HTMLPage;

/**
 * Class Header
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class Head extends AbstractSection
{
	/** @var array */
	protected $preparedAssets = [];

	public function prepare() : void
	{
		$search               = [];
		$replace              = [];
		$this->preparedAssets = [];

		// Get contents of file
		foreach ( $this->getAssets( HTMLPage::ASSET_FONT ) as $fontFile )
		{
			$filename    = basename( $fontFile );
			$filePath    = realpath( $fontFile );
			$fileContent = base64_encode( file_get_contents( $filePath ) );

			$matches = [];
			preg_match( "#\.([^\.]+)$#", $filename, $matches );

			switch ( strtolower( $matches[1] ) )
			{
				case 'eot':
				case 'ttf':
				case 'woff':
					$search[]  = sprintf( 'url(../fonts/%s', $filename );
					$replace[] = sprintf( 'url(data:font/%s;base64,%s', $matches[1], $fileContent );
					break;

				case 'svg':
					$search[]  = sprintf( 'url(../fonts/%s', $filename );
					$replace[] = sprintf( 'url(data:image/svg+xml;base64,%s', $fileContent );
					break;
			}
		}

		foreach ( $this->getAssets( HTMLPage::ASSET_CSS ) as $cssFile )
		{
			$fileContent = file_get_contents( $cssFile );
			$fileContent = str_replace( $search, $replace, $fileContent );

			$this->preparedAssets[ HTMLPage::ASSET_CSS ][] = $fileContent;
		}
	}

	public function addNodes() : void
	{
		$tree = $this->getHtmlTree();
		$head = $this->getDom()->createElement( 'head' );

		// Title
		$title = $this->getOptions()->get( Opt::NAME_PROJECT );
		if ( $tree->getSearch()->isCurrentFileValid() )
		{
			$title = basename( $tree->getSearch()->getCurrentFile() ) . ' - ' . $title;
		}

		// Charset
		$head->appendChild( $this->getElementWithAttributes( 'meta', ['charset' => 'UTF-8'] ) );

		// Description
		$head->appendChild(
			$this->getElementWithAttributes(
				'meta',
				[
					'name'    => 'description',
					'content' => $this->getOptions()->get( Opt::PROJECT_ABSTRACT ),
				]
			)
		);

		// Viewport
		$head->appendChild(
			$this->getElementWithAttributes(
				'meta',
				[
					'name'    => 'viewport',
					'contetn' => 'width=device-width, initial-scale=1.0',
				]
			)
		);

		$head->appendChild( $this->getDom()->createElement( 'title', $title ) );

		foreach ( (array)$this->preparedAssets[ HTMLPage::ASSET_CSS ] as $cssContent )
		{
			$elem = $this->getElementWithAttributes( 'style', ['type' => 'text/css'] );

			$cdata = $this->getDom()->createCDATASection( $cssContent );

			$elem->appendChild( $cdata );
			$head->appendChild( $elem );
		}

		$this->getDomContainer()->appendChild( $head );
	}
}
