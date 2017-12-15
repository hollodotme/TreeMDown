<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

use hollodotme\TreeMDown\Misc\Opt;

/**
 * Class Footer
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class Footer extends AbstractSection
{
	public function addNodes() : void
	{
		$footer = $this->getDom()->createElement( 'footer' );
		$this->getDomContainer()->appendChild( $footer );

		$row = $this->getElementWithAttributes( 'div', ['class' => 'tmd-footer row'] );
		$footer->appendChild( $row );

		$content = $this->getElementWithAttributes(
			'div',
			['class' => 'col-lg-10 col-lg-offset-2 col-md-9 col-md-offset-3 col-sm-8 col-sm-offset-4 col-xs-12']
		);
		$row->appendChild( $content );

		$content->appendChild( $this->getDom()->createElement( 'hr' ) );

		$spanCompany = $this->getElementWithAttributes(
			'div',
			['class' => 'small text-muted'],
			sprintf(
				'%s by %s',
				$this->getOptions()->get( Opt::NAME_PROJECT ),
				$this->getOptions()->get( Opt::NAME_COMPANY )
			)
		);

		$content->appendChild( $spanCompany );

		$toTop = $this->getElementWithAttributes( 'div', ['class' => 'small text-left'] );
		$toTop->appendChild( $this->getElementWithAttributes( 'a', ['href' => '#'], 'Back to top' ) );
		$content->appendChild( $toTop );
	}
}
