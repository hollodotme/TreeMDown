<?php declare(strict_types=1);
/**
 * Table of figures section
 *
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

/**
 * Class TableOfFigures
 *
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class TableOfFigures extends AbstractSection
{

	/**
	 * The TOF
	 *
	 * @var null|\DOMElement
	 */
	protected $_tof = null;

	/**
	 * Return the TOF
	 *
	 * @return \DOMElement|null
	 */
	public function getTof()
	{
		return $this->_tof;
	}

	/**
	 * Set the TOF
	 *
	 * @param \DOMElement|null $tof
	 */
	public function setTof( $tof )
	{
		$this->_tof = $tof;
	}

	/**
	 * Add the section nodes
	 */
	public function addNodes()
	{
		if ( !is_null( $this->_tof ) )
		{
			$this->getContainer()->appendChild( $this->getDom()->importNode( $this->_tof, true ) );
		}
	}
}
