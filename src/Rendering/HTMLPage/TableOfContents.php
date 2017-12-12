<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

/**
 * Class TableOfContents
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class TableOfContents extends AbstractSection
{
	/** @var \DOMElement */
	protected $tableOfContents;

	public function getTableOfContents() : \DOMElement
	{
		return $this->tableOfContents;
	}

	public function setTableOfContents( \DOMElement $tableOfContents )
	{
		$this->tableOfContents = $tableOfContents;
	}

	public function addNodes() : void
	{
		if ( null !== $this->tableOfContents )
		{
			$this->getDomContainer()->appendChild( $this->getDom()->importNode( $this->tableOfContents, true ) );
		}
	}
}
