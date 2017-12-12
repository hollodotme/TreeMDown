<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

/**
 * Class Sidebar
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class Sidebar extends AbstractSection
{
	public function addNodes() : void
	{
		$this->getDomContainer()->appendChild( $this->getDom()->importNode( $this->htmlTree->getOutput(), true ) );
	}
}
