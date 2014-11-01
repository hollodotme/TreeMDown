<?php
/**
 * Class for the HTML page
 *
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering;

use hollodotme\TreeMDown\Misc\Opt;
use hollodotme\TreeMDown\Misc\Options;

/**
 * Class HTMLPage
 *
 * @package hollodotme\TreeMDown\Rendering
 */
class HTMLPage
{

	const ASSET_CSS         = 'asset_css';

	const ASSET_FONT        = 'asset_font';

	const ASSET_JS          = 'asset_js';

	const ASSET_IMG         = 'asset_img';

	const META_PROJECT_NAME = 'meta_project_name';

	const META_ABSTRACT     = 'meta_abstract';

	const META_COMPANY      = 'meta_company';

	/**
	 * Tree instance
	 *
	 * @var HTMLTree
	 */
	protected $_tree;

	/**
	 * DOM document
	 *
	 * @var \DOMDocument
	 */
	protected $_dom;

	/**
	 * Constructor
	 *
	 * @param HTMLTree $tree
	 */
	public function __construct( HTMLTree $tree )
	{
		$this->_tree = $tree;

		$dom_implementation = new \DOMImplementation();
		$doc_type           = $dom_implementation->createDocumentType( 'html', '', '' );

		$this->_dom = $dom_implementation->createDocument( '', 'html', $doc_type );
		$this->_dom->documentElement->setAttribute( 'lang', 'en' );
	}

	/**
	 * Return the options (wrapper)
	 *
	 * @return Options
	 */
	public function getOptions()
	{
		return $this->_tree->getOptions();
	}

	/**
	 * Return the rendered DOMDocument
	 *
	 * @return \DOMDocument
	 */
	public function getDOMDocument()
	{
		// Init head section
		$head = new HTMLPage\Head( $this->_dom->documentElement, $this->_tree );

		// Add font assets
		$head->addAsset( self::ASSET_FONT, __DIR__ . '/../Assets/fonts/glyphicons-halflings-regular.eot' );
		$head->addAsset( self::ASSET_FONT, __DIR__ . '/../Assets/fonts/glyphicons-halflings-regular.ttf' );
		$head->addAsset( self::ASSET_FONT, __DIR__ . '/../Assets/fonts/glyphicons-halflings-regular.svg' );
		$head->addAsset( self::ASSET_FONT, __DIR__ . '/../Assets/fonts/glyphicons-halflings-regular.woff' );

		// Add css assets
		$head->addAsset( self::ASSET_CSS, __DIR__ . '/../Assets/css/bootstrap-3.2.0.min.css' );
		$head->addAsset( self::ASSET_CSS, __DIR__ . '/../Assets/css/bootstrap-theme-3.2.0.min.css' );
		$head->addAsset( self::ASSET_CSS, __DIR__ . '/../Assets/css/github-markdown.min.css' );
		$head->addAsset( self::ASSET_CSS, __DIR__ . '/../Assets/css/highlightjs-default.min.css' );
		$head->addAsset( self::ASSET_CSS, __DIR__ . '/../Assets/css/highlightjs-github.min.css' );
		$head->addAsset( self::ASSET_CSS, __DIR__ . '/../Assets/css/treemdown.min.css' );

		if ( $this->getOptions()->get( Opt::GITHUB_RIBBON_ENABLED ) )
		{
			$head->addAsset( self::ASSET_CSS, __DIR__ . '/../Assets/css/github-fork-ribbon.min.css' );
		}

		// Init body section
		$body = new HTMLPage\Body( $this->_dom->documentElement, $this->_tree );

		// Add script assets
		$body->addAsset( self::ASSET_JS, __DIR__ . '/../Assets/js/jquery-2.1.1.min.js' );
		$body->addAsset( self::ASSET_JS, __DIR__ . '/../Assets/js/bootstrap-3.2.0.min.js' );
		$body->addAsset( self::ASSET_JS, __DIR__ . '/../Assets/js/treemdown.min.js' );
		$body->addAsset( self::ASSET_JS, __DIR__ . '/../Assets/js/highlight-8.2.min.js' );

		// Prepare sections
		$head->prepare();
		$body->prepare();

		// Add section nodes
		$head->addNodes();
		$body->addNodes();

		return $this->_dom;
	}
}
