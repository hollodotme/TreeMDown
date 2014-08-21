<?php
/**
 * Github ribbon section
 *
 * @author h.woltersdorf
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

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
					'href' => 'https://github.com/hollodotme/TreeMDown',
					'target' => '_blank'
				),
				'Fork me on GitHub'
			)
		);
	}
}
