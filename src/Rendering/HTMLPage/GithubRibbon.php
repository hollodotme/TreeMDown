<?php declare(strict_types=1);
/**
 * Github ribbon section
 *
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

use hollodotme\TreeMDown\Misc\Opt;

/**
 * Class GithubRibbon
 *
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class GithubRibbon extends AbstractSection
{
	/**
	 * Add the section nodes
	 */
	public function addNodes()
	{
		$wrapper = $this->getElementWithAttributes(
			'div', array( 'class' => 'github-fork-ribbon-wrapper left-bottom' )
		);
		$this->getContainer()->appendChild( $wrapper );

		$ribbon = $this->getElementWithAttributes( 'div', array( 'class' => 'github-fork-ribbon' ) );
		$wrapper->appendChild( $ribbon );

		$ribbon->appendChild(
			$this->getElementWithAttributes(
				'a',
				array(
					'href'   => $this->getOptions()->get( Opt::GITHUB_RIBBON_URL ),
					'target' => '_blank'
				),
				'Fork me on GitHub'
			)
		);
	}
}
