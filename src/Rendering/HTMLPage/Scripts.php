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
		foreach ( $this->getAssets( HTMLPage::ASSET_JS ) as $js_file )
		{
			$file_content = file_get_contents( $js_file );
			$file_content = str_replace( '</script>', '<\/script>', $file_content );

			$elem         = $this->getElementWithAttributes( 'script', array('type' => 'text/javascript') );
			$elem->appendChild( $this->getDom()->createCDATASection( $file_content ) );
			$this->getDomContainer()->appendChild( $elem );
		}

		$this->getDomContainer()->appendChild(
			$this->getElementWithAttributes(
				'script', array('type' => 'text/javascript'),
				'hljs.initHighlightingOnLoad();'
			)
		);
	}
}
