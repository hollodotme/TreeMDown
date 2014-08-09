<?php
/**
 * Class for the HTML page
 *
 * @author h.woltersdorf
 */

namespace hollodotme\TreeMDown\Rendering;

/**
 * Class HTMLPage
 *
 * @package hollodotme\TreeMDown\Rendering
 */
class HTMLPage
{

	protected $_tree;

	protected $_dom;

	protected $_project_name;

	protected $_short_description;

	protected $_company;

	protected $_base_url = '/';

	public function __construct( HTMLTree $tree )
	{
		$this->_tree = $tree;

		$dom_implementation = new \DOMImplementation();
		$doc_type           = $dom_implementation->createDocumentType( 'html', '', '' );

		$this->_dom = $dom_implementation->createDocument( '', 'html', $doc_type );
		$this->_dom->documentElement->setAttribute( 'lang', 'en' );
	}

	/**
	 * @param string $company
	 */
	public function setCompany( $company )
	{
		$this->_company = $company;
	}

	/**
	 * @param string $project_name
	 */
	public function setProjectName( $project_name )
	{
		$this->_project_name = $project_name;
	}

	/**
	 * @param string $short_description
	 */
	public function setShortDescription( $short_description )
	{
		$this->_short_description = $short_description;
	}

	public function getOutput()
	{
		$this->_addHeadSection();
		$this->_addBodySection();

		return $this->_dom->saveHTML();
	}

	protected function _addHeadSection()
	{
		$head = $this->_dom->createElement( 'head' );

		// Title
		$title = $this->_dom->createElement( 'title', 'TreeMDown' );
		$head->appendChild( $title );

		// bootstrap css
		$bootstrap = $this->_dom->createElement( 'style' );
		$bootstrap->setAttribute( 'type', 'text/css' );
		$css_text = $this->_dom->createCDATASection(
			file_get_contents( __DIR__ . '/../Assets/css/bootstrap-3.2.0.min.css' )
		);
		$bootstrap->appendChild( $css_text );

		$head->appendChild( $bootstrap );

		// bootstrap-theme css
		$bootstrap = $this->_dom->createElement( 'style' );
		$bootstrap->setAttribute( 'type', 'text/css' );
		$css_text = $this->_dom->createCDATASection(
			file_get_contents( __DIR__ . '/../Assets/css/bootstrap-theme-3.2.0.min.css' )
		);
		$bootstrap->appendChild( $css_text );

		$head->appendChild( $bootstrap );

		$github_markdown_css = $this->_dom->createElement( 'style' );
		$github_markdown_css->setAttribute( 'type', 'text/css' );
		$css_text = $this->_dom->createCDATASection(
			file_get_contents( __DIR__ . '/../Assets/css/github-markdown.css' )
		);
		$github_markdown_css->appendChild( $css_text );
		$head->appendChild( $github_markdown_css );

		// highlight github css
		$highlight_github = $this->_dom->createElement( 'style' );
		$highlight_github->setAttribute( 'type', 'text/css' );
		$css_text = $this->_dom->createCDATASection(
			file_get_contents( __DIR__ . '/../Assets/css/highlightjs-github.min.css' )
		);
		$highlight_github->appendChild( $css_text );

		$head->appendChild( $highlight_github );

		$this->_dom->documentElement->appendChild( $head );
	}

	protected function _addBodySection()
	{
		$body = $this->_dom->createElement( 'body' );
		$body->setAttribute( 'role', 'document' );

		$this->_addHeaderSection( $body );

		$container = $this->_dom->createElement( 'div' );
		$container->setAttribute( 'class', 'container-fluid' );
		$container->setAttribute( 'style', 'padding-top: 50px' );
		$container->setAttribute( 'role', 'main' );
		$body->appendChild( $container );

		$row = $this->_dom->createElement( 'row' );
		$row->setAttribute( 'class', 'row' );
		$container->appendChild( $row );

		$nav = $this->_dom->createElement( 'div' );
		$nav->setAttribute( 'class', 'col-md-3 col-sm-2 hidden-xs' );
		$row->appendChild( $nav );

		$content = $this->_dom->createElement( 'div' );
		$content->setAttribute( 'class', 'col-md-9 col-sm-10 col-xs-12' );
		$row->appendChild( $content );

		$this->_addNavSection( $nav );
		$this->_addContentSection( $content );
		$this->_addFooterSection( $container );
		$this->_addScriptSection( $body );

		$this->_dom->documentElement->appendChild( $body );
	}

	protected function _addHeaderSection( \DOMElement $body )
	{
		$nav = $this->_dom->createElement( 'div' );
		$nav->setAttribute( 'class', 'navbar navbar-default navbar-fixed-top' );
		$nav->setAttribute( 'role', 'navigation' );
		$body->appendChild( $nav );

		$container = $this->_dom->createElement( 'div' );
		$container->setAttribute( 'class', 'container-fluid' );
		$nav->appendChild( $container );

		$navbar_header = $this->_dom->createElement( 'div' );
		$navbar_header->setAttribute( 'class', 'navbar-header' );
		$container->appendChild( $navbar_header );

		$toggle_btn = $this->_dom->createElement( 'button' );
		$toggle_btn->setAttribute( 'type', 'button' );
		$toggle_btn->setAttribute( 'class', 'navbar-toggle' );
		$toggle_btn->setAttribute( 'data-toggle', 'collapse' );
		$toggle_btn->setAttribute( 'data-target', '#nb-collapse-1' );
		$navbar_header->appendChild( $toggle_btn );

		$btn_span = $this->_dom->createElement( 'span', 'Toggle navigation' );
		$btn_span->setAttribute( 'class', 'sr-only' );
		$toggle_btn->appendChild( $btn_span );

		$btn_span = $this->_dom->createElement( 'span' );
		$btn_span->setAttribute( 'class', 'icon-bar' );
		$btn_span_text = $this->_dom->createTextNode( '' );
		$btn_span->appendChild( $btn_span_text );
		$toggle_btn->appendChild( $btn_span );

		$btn_span = $this->_dom->createElement( 'span' );
		$btn_span->setAttribute( 'class', 'icon-bar' );
		$btn_span_text = $this->_dom->createTextNode( '' );
		$btn_span->appendChild( $btn_span_text );
		$toggle_btn->appendChild( $btn_span );

		$btn_span = $this->_dom->createElement( 'span' );
		$btn_span->setAttribute( 'class', 'icon-bar' );
		$btn_span_text = $this->_dom->createTextNode( '' );
		$btn_span->appendChild( $btn_span_text );
		$toggle_btn->appendChild( $btn_span );

		$brand = $this->_dom->createElement( 'a', $this->_project_name );
		$brand->setAttribute( 'class', 'navbar-brand' );
		$brand->setAttribute( 'href', 'javascript:void(0);' );
		$navbar_header->appendChild( $brand );

		// Collapse content
		$content = $this->_dom->createElement( 'div' );
		$content->setAttribute( 'class', 'navbar-collapse collapse' );
		$content->setAttribute( 'id', 'nb-collapse-1' );
		$container->appendChild( $content );

		$form = $this->_dom->createElement( 'form' );
		$form->setAttribute( 'class', 'navbar-form navbar-right' );
		$form->setAttribute( 'role', 'search' );
		$form->setAttribute( 'method', 'get' );
		$form->setAttribute( 'action', '' );
		$content->appendChild( $form );

		$current_file = $this->_dom->createElement( 'input' );
		$current_file->setAttribute( 'type', 'hidden' );
		$current_file->setAttribute( 'name', 'f' );
		$current_file->setAttribute( 'value', $this->_tree->getCurrentFile( true ) );
		$form->appendChild( $current_file );

		$group = $this->_dom->createElement( 'div' );
		$group->setAttribute( 'class', 'form-group' );
		$form->appendChild( $group );

		$label = $this->_dom->createElement( 'label', 'Search' );
		$label->setAttribute( 'for', 'main-search' );
		$label->setAttribute( 'class', 'sr-only' );
		$group->appendChild( $label );

		$input_group = $this->_dom->createElement( 'div' );
		$input_group->setAttribute( 'class', 'input-group' );
		$group->appendChild( $input_group );

		$input = $this->_dom->createElement( 'input' );
		$input->setAttribute( 'class', 'form-control' );
		$input->setAttribute( 'type', 'text' );
		$input->setAttribute( 'name', 'q' );
		$input->setAttribute( 'size', '50' );
		$input->setAttribute( 'id', 'main-search' );
		$input->setAttribute( 'placeholder', 'search (with grep) ...' );
		$input->setAttribute( 'value', $this->_tree->getSearchFilter() );
		$input_group->appendChild( $input );

		$btn_span = $this->_dom->createElement( 'span' );
		$btn_span->setAttribute( 'class', 'input-group-btn' );
		$input_group->appendChild( $btn_span );

		$submit = $this->_dom->createElement( 'button', 'go' );
		$submit->setAttribute( 'class', 'btn btn-default' );
		$submit->setAttribute( 'type', 'submit' );
		$btn_span->appendChild( $submit );
	}

	protected function _addNavSection( \DOMElement $nav )
	{
		$panel = $this->_dom->createElement( 'div' );
		$panel->setAttribute( 'class', 'panel panel-default' );
		$nav->appendChild( $panel );

		$heading = $this->_dom->createElement( 'div' );
		$heading->setAttribute( 'class', 'panel-heading' );
		$panel->appendChild( $heading );

		$title = $this->_dom->createElement( 'h3', 'Tree' );
		$title->setAttribute( 'class', 'panel-title' );
		$heading->appendChild( $title );

		$content = $this->_dom->createElement( 'div' );
		$content->setAttribute( 'class', 'panel-body' );
		$panel->appendChild( $content );

		// Tree
		$content->appendChild( $this->_dom->importNode( $this->_tree->getOutput(), true ) );
	}

	protected function _addContentSection( \DOMElement $content )
	{
		$div = $this->_dom->createElement( 'div' );
		$div->setAttribute( 'class', 'markdown-content' );
		$content->appendChild( $div );

		try
		{
			// Parsedown execution
			$parser   = new \ParsedownExtra();
			$markdown = $parser->text( file_get_contents( $this->_tree->getCurrentFile( false ) ) );

			if ( !empty($markdown) )
			{
				$dom = new \DOMDocument( '1.0', 'UTF-8' );
				$dom->loadHTML( $markdown );

				$div->appendChild( $this->_dom->importNode( $dom->documentElement, true ) );
			}
			else
			{
				$this->_addErrorSection(
					$div,
					'warning',
					":-( You're not done yet!",
					"This file has no content at all."
				);
			}
		}
		catch ( \Exception $e )
		{
			$this->_addErrorSection(
				$div,
				'danger',
				'Oops! An error occured while parsing markdown file',
				$this->_tree->getCurrentFile( true ) . ': ' . $e->getMessage()
			);
		}
	}

	protected function _addErrorSection( \DOMElement $elem, $severity, $header, $message )
	{
		$panel = $this->_dom->createElement( 'div' );
		$panel->setAttribute( 'class', 'panel panel-' . $severity );
		$elem->appendChild( $panel );

		$heading = $this->_dom->createElement( 'div' );
		$heading->setAttribute( 'class', 'panel-heading' );
		$panel->appendChild( $heading );

		$title = $this->_dom->createElement( 'h3', $header );
		$title->setAttribute( 'class', 'panel-title' );
		$heading->appendChild( $title );

		$content = $this->_dom->createElement( 'div', $message );
		$content->setAttribute( 'class', 'panel-body' );
		$panel->appendChild( $content );
	}

	protected function _addFooterSection( \DOMElement $body )
	{
		$hr = $this->_dom->createElement( 'hr' );
		$hr->setAttribute('class', 'clearfix');
		$body->appendChild( $hr );

		$div = $this->_dom->createElement( 'div', '&copy; ' . $this->_company . ' ' . date( 'Y' ) );
		$div->setAttribute( 'class', 'page-footer text-right small text-muted' );
		$body->appendChild( $div );
	}

	protected function _addScriptSection( \DOMElement $body )
	{
		// jquery.js
		$jquery = $this->_dom->createElement( 'script' );
		$jquery->setAttribute( 'type', 'text/javascript' );

		$jq_content = $this->_dom->createCDATASection(
			file_get_contents( __DIR__ . '/../Assets/js/jquery-2.1.1.min.js' )
		);
		$jquery->appendChild( $jq_content );

		$body->appendChild( $jquery );

		// bootstrap.js
		$bootstrap_js = $this->_dom->createElement( 'script' );
		$bootstrap_js->setAttribute( 'type', 'text/javascript' );

		$js_text = $this->_dom->createCDATASection(
			file_get_contents( __DIR__ . '/../Assets/js/bootstrap-3.2.0.min.js' )
		);
		$bootstrap_js->appendChild( $js_text );

		$body->appendChild( $bootstrap_js );

		// highlight.js
		$highlightjs = $this->_dom->createElement( 'script' );
		$highlightjs->setAttribute( 'type', 'text/javascript' );

		$js_text = $this->_dom->createCDATASection(
			file_get_contents( __DIR__ . '/../Assets/js/highlight-8.1.min.js' )
		);
		$highlightjs->appendChild( $js_text );

		$body->appendChild( $highlightjs );

		$hljs_init = $this->_dom->createElement( 'script', 'hljs.initHighlightingOnLoad();' );
		$hljs_init->setAttribute( 'type', 'text/javascript' );
		$body->appendChild( $hljs_init );
	}
}
