<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering;

use hollodotme\TreeMDown\Misc\Opt;
use hollodotme\TreeMDown\Misc\Options;

/**
 * Class HTMLPage
 * @package hollodotme\TreeMDown\Rendering
 */
class HTMLPage
{
	public const ASSET_CSS         = 'asset_css';

	public const ASSET_FONT        = 'asset_font';

	public const ASSET_JS          = 'asset_js';

	public const ASSET_IMG         = 'asset_img';

	public const META_PROJECT_NAME = 'meta_project_name';

	public const META_ABSTRACT     = 'meta_abstract';

	public const META_COMPANY      = 'meta_company';

	/** @var HTMLTree */
	protected $tree;

	/** @var \DOMDocument */
	protected $dom;

	public function __construct( HTMLTree $tree )
	{
		$this->tree = $tree;

		$domImplementation = new \DOMImplementation();
		$docType           = $domImplementation->createDocumentType( 'html', '', '' );

		$this->dom = $domImplementation->createDocument( '', 'html', $docType );
		$this->dom->documentElement->setAttribute( 'lang', 'en' );
	}

	public function getOptions() : Options
	{
		return $this->tree->getOptions();
	}

	public function getDOMDocument() : \DOMDocument
	{
		// Init head section
		$head = new HTMLPage\Head( $this->dom->documentElement, $this->tree );

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
		$body = new HTMLPage\Body( $this->dom->documentElement, $this->tree );

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

		return $this->dom;
	}
}
