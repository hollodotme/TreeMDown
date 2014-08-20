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
	 * Prepare the section
	 */
	public function prepare()
	{
		// TODO: Implement prepare() method.
	}

	/**
	 * Add the section nodes
	 */
	public function addNodes()
	{
		$panel = $this->_dom->createElement( 'div' );
		$panel->setAttribute( 'class', 'panel panel-default' );
		$panel->setAttribute( 'id', 'tmd-nav' );

		$this->getContainer()->appendChild( $panel );

		$panel_body = $this->_dom->createElement( 'div' );
		$panel_body->setAttribute( 'class', 'panel-body' );
		$panel->appendChild( $panel_body );

		// Import the tree DOM
		$panel_body->appendChild( $this->_dom->importNode( $this->_tree->getOutput(), true ) );
	}
}