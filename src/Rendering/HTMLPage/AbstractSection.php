<?php declare(strict_types=1);
/**
 * Abstract section of HTMLPage
 *
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

use hollodotme\TreeMDown\Misc\Options;
use hollodotme\TreeMDown\Rendering\HTMLTree;

/**
 * Class AbstractSection
 *
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
abstract class AbstractSection
{
	/** @var array */
	protected $assets = [];

	/** @var array */
	protected $metaData = [];

	/** @var \DOMElement */
	protected $domContainer;

	/** @var HTMLTree */
	protected $htmlTree;

	public function __construct( \DOMElement $domContainer, HTMLTree $htmlTree )
	{
		$this->domContainer = $domContainer;
		$this->htmlTree     = $htmlTree;
	}

	public function getDom() : \DOMDocument
	{
		return $this->domContainer->ownerDocument;
	}

	public function getDomContainer() : \DOMElement
	{
		return $this->domContainer;
	}

	public function getHtmlTree() : HTMLTree
	{
		return $this->htmlTree;
	}

	public function addAsset( string $type, string $asset ) : void
	{
		$this->assets[ $type ][] = $asset;
	}

	public function setAssetsArray( array $assets ) : void
	{
		$this->assets = $assets;
	}

	public function getAssets( ?string $type = null ) : array
	{
		if ( null === $type )
		{
			return $this->assets;
		}

		return $this->assets[ $type ] ?? [];
	}

	public function getOptions() : Options
	{
		return $this->htmlTree->getOptions();
	}

	public function getElementWithAttributes( string $name, array $attributes, string $content = '' ) : \DOMElement
	{
		$elem = $this->getDom()->createElement( $name, $content );

		foreach ( $attributes as $attr_name => $attr_value )
		{
			$elem->setAttribute( $attr_name, $attr_value );
		}

		return $elem;
	}

	public function prepare() : void
	{
		// Override in extending classes
	}

	abstract public function addNodes() : void;
}
