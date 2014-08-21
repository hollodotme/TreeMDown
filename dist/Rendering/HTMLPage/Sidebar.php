<?php
/**
 * Sidebar section
 * @author hwoltersdorf
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
//		$panel = $this->getDom()->createElement( 'div' );
//		$panel->setAttribute( 'class', 'panel panel-default' );
//		$panel->setAttribute( 'id', 'tmd-nav' );
//
//		$this->getContainer()->appendChild( $panel );
//
//		$panel_body = $this->getDom()->createElement( 'div' );
//		$panel_body->setAttribute( 'class', 'panel-body' );
//		$panel->appendChild( $panel_body );

		// Import the tree DOM
		$this->getContainer()->appendChild( $this->getDom()->importNode( $this->_tree->getOutput(), true ) );
	}
}
