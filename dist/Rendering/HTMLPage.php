<?php
/**
 * Class for the HTML page
 *
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering;

/**
 * Class HTMLPage
 *
 * @package hollodotme\TreeMDown\Rendering
 */
class HTMLPage
{
	const ASSET_CSS = 'asset_css';
	const ASSET_FONT = 'asset_font';
	const ASSET_JS = 'asset_js';
	const ASSET_IMG = 'asset_img';

	const META_PROJECT_NAME = 'meta_project_name';
	const META_ABSTRACT = 'meta_abstract';
	const META_COMPANY = 'meta_company';

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
	 * User defined project name
	 *
	 * @var string
	 */
	protected $_project_name = 'Project name';

	/**
	 * User defined short description
	 *
	 * @var string
	 */
	protected $_short_description = 'Short description';

	/**
	 * User defined company
	 *
	 * @var string
	 */
	protected $_company = 'Company';

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
	 * Set the company
	 *
	 * @param string $company
	 */
	public function setCompany( $company )
	{
		$this->_company = $company;
	}

	/**
	 * Set the project name
	 *
	 * @param string $project_name
	 */
	public function setProjectName( $project_name )
	{
		$this->_project_name = $project_name;
	}

	/**
	 * Set the short description
	 *
	 * @param string $short_description
	 */
	public function setShortDescription( $short_description )
	{
		$this->_short_description = $short_description;
	}

	/**
	 * Return the page output
	 *
	 * @return string
	 */
	public function getOutput()
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
		$head->addAsset( self::ASSET_CSS, __DIR__ . '/../Assets/css/github-markdown.css' );
		$head->addAsset( self::ASSET_CSS, __DIR__ . '/../Assets/css/highlightjs-github.min.css' );
		$head->addAsset( self::ASSET_CSS, __DIR__ . '/../Assets/css/treemdown.min.css' );

		// Add meta data
		$head->setMetaData( self::META_PROJECT_NAME, $this->_project_name );
		$head->setMetaData( self::META_ABSTRACT, $this->_short_description );
		$head->setMetaData( self::META_COMPANY, $this->_company );

		// Init body section
		$body = new HTMLPage\Body( $this->_dom->documentElement, $this->_tree );

		// Add script assets
		$body->addAsset( self::ASSET_JS, __DIR__ . '/../Assets/js/jquery-2.1.1.min.js' );
		$body->addAsset( self::ASSET_JS, __DIR__ . '/../Assets/js/bootstrap-3.2.0.min.js' );
		$body->addAsset( self::ASSET_JS, __DIR__ . '/../Assets/js/treemdown.min.js' );
		$body->addAsset( self::ASSET_JS, __DIR__ . '/../Assets/js/highlight-8.1.min.js' );

		// Add meta data
		$body->setMetaData( self::META_PROJECT_NAME, $this->_project_name );
		$body->setMetaData( self::META_ABSTRACT, $this->_short_description );
		$body->setMetaData( self::META_COMPANY, $this->_company );

		// Prepare sections
		$head->prepare();
		$body->prepare();

		// Add section nodes
		$head->addNodes();
		$body->addNodes();

		$this->_dom->formatOutput = false;

		return $this->_dom->saveHTML();
	}
}
