<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

/**
 * Class TableOfFigures
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class TableOfFigures extends AbstractSection
{
	/** @var \DOMElement */
	protected $tableOfFigures;

	public function getTableOfFigures() : \DOMElement
	{
		return $this->tableOfFigures;
	}

	public function setTableOfFigures( \DOMElement $tableOfFigures ) : void
	{
		$this->tableOfFigures = $tableOfFigures;
	}

	public function addNodes() : void
	{
		if ( null !== $this->tableOfFigures )
		{
			$this->getDomContainer()->appendChild( $this->getDom()->importNode( $this->tableOfFigures, true ) );
		}
	}
}
