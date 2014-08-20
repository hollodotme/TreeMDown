<?php
/**
 * TOC section
 * @author hwoltersdorf
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

/**
 * Class TOC
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class TOC extends AbstractSection
{

	/**
	 * The TOC
	 * @var null|\DOMElement
	 */
	protected $_toc = null;

	/**
	 * @return \DOMElement|null
	 */
	public function getToc()
	{
		return $this->_toc;
	}

	/**
	 * @param \DOMElement $toc
	 */
	public function setToc( \DOMElement $toc )
	{
		$this->_toc = $toc;
	}

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
		if ( !is_null( $this->_toc ) )
		{
			$container = $this->getElementWithAttributes( 'div', array('id' => 'toc') );
			$container->appendChild( $this->getDom()->importNode( $this->_toc, true ) );
			$this->getContainer()->appendChild( $container );
		}
	}
}