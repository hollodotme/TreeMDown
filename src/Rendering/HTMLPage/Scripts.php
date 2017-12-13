<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

use hollodotme\TreeMDown\Rendering\HTMLPage;

/**
 * Class Scripts
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class Scripts extends AbstractSection
{
	public function addNodes() : void
	{
		foreach ( $this->getAssets( HTMLPage::ASSET_JS ) as $jsFile )
		{
			$fileContent = file_get_contents( $jsFile );
			$fileContent = str_replace( '</script>', '<\/script>', $fileContent );

			$elem = $this->getElementWithAttributes( 'script', ['type' => 'text/javascript'] );
			$elem->appendChild( $this->getDom()->createCDATASection( $fileContent ) );
			$this->getDomContainer()->appendChild( $elem );
		}
	}
}
