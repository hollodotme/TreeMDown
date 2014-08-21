<?php
/**
 * Footer section
 *
 * @author hwoltersdorf
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

use hollodotme\TreeMDown\Rendering\HTMLPage;

/**
 * Class Footer
 *
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class Footer extends AbstractSection
{
	/**
	 * Add the section nodes
	 */
	public function addNodes()
	{
		$footer = $this->getDom()->createElement( 'footer' );
		$this->getContainer()->appendChild( $footer );

		$row = $this->getElementWithAttributes( 'div', array( 'class' => 'tmd-footer row' ) );
		$footer->appendChild( $row );

		$content = $this->getElementWithAttributes(
			'div',
			array( 'class' => 'col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-8 col-sm-offset-4 col-xs-12' )
		);
		$row->appendChild( $content );

		$content->appendChild( $this->getDom()->createElement( 'hr' ) );

		$span_company = $this->getElementWithAttributes(
			'span',
			array( 'class' => 'pull-right small text-muted' ),
			sprintf(
				'%s by %s %s',
				$this->getMetaData( HTMLPage::META_PROJECT_NAME ),
				$this->getMetaData( HTMLPage::META_COMPANY ),
				date( 'Y' )
			)
		);

		$content->appendChild( $span_company );

		$totop = $this->getElementWithAttributes( 'div', array( 'class' => 'small text-left' ) );
		$totop->appendChild( $this->getElementWithAttributes( 'a', array( 'href' => '#' ), 'Back to top' ) );
		$content->appendChild( $totop );
	}
}
