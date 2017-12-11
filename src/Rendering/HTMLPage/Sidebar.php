<?php declare(strict_types=1);
/**
 * Sidebar section
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

/**
 * Class Sidebar
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class Sidebar extends AbstractSection
{
	/**
	 * Add the section nodes
	 */
	public function addNodes()
	{
		// Import the tree DOM
		$this->getContainer()->appendChild( $this->getDom()->importNode( $this->_tree->getOutput(), true ) );
	}
}
