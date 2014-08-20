<?php
/**
 * Body of HTMLPage
 *
 * @author h.woltersdorf
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

use hollodotme\TreeMDown\Rendering\HTMLPage;

/**
 * Class Body
 *
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class Body extends AbstractSection
{

	/**
	 * The parsed markdown as HTML-string
	 *
	 * @var null|\DOMElement
	 */
	protected $_parsed_markdown = null;

	/**
	 * The TOC
	 *
	 * @var null|\DOMElement
	 */
	protected $_toc = null;

	/**
	 * User messages
	 *
	 * @var array
	 */
	protected $_user_messages = array();

	/**
	 * Prepare the content
	 */
	public function prepare()
	{
		// Prepare parsed markdown
		$this->_prepareParsedMarkdown();

		// Prepare the TOC
		$this->_prepareTOC();
	}

	/**
	 * Add nodes to the DOM
	 */
	public function addNodes()
	{
		$body = $this->getElementWithAttributes( 'body', array('role' => 'document') );

		if ( !is_null( $this->_toc ) )
		{
			$body->setAttribute( 'data-spy', 'scroll' );
			$body->setAttribute( 'data-target', '#toc' );
			$body->setAttribute( 'data-offset', '75' );
		}

		// Header nav section
		$header = new Header( $this->_dom, $body, $this->_tree );
		foreach ( $this->_meta_data as $type => $value )
		{
			$header->setMetaData( $type, $value );
		}

		$header->prepare();
		$header->addNodes();

		$section = $this->getDom()->createElement( 'section' );
		$body->appendChild( $section );

		$container = $this->_dom->createElement( 'div' );
		$container->setAttribute( 'class', 'container-fluid' );
		$container->setAttribute( 'role', 'main' );
		$section->appendChild( $container );

		$row = $this->_dom->createElement( 'div' );
		$row->setAttribute( 'class', 'row' );
		$container->appendChild( $row );

		$nav = $this->_dom->createElement( 'div' );
		$nav->setAttribute( 'class', 'col-lg-3 col-md-3 col-sm-4 hidden-xs' );
		$row->appendChild( $nav );

		$content = $this->_dom->createElement( 'div' );
		$row->appendChild( $content );

		// TOC exists?
		if ( !is_null( $this->_toc ) )
		{
			$content->setAttribute( 'class', 'col-lg-7 col-md-7 col-sm-8 col-xs-12' );

			// Add TOC column
			$toc = $this->_dom->createElement( 'div' );
			$toc->setAttribute( 'class', 'col-lg-2 col-md-2 hidden-sm hidden-xs' );
			$row->appendChild( $toc );

			$this->_addTOCSection( $toc );
		}
		else
		{
			$content->setAttribute( 'class', 'col-lg-9 col-md-9 col-sm-8 col-xs-12' );
		}

		$this->_addNavSection( $nav );
		$this->_addContentSection( $content );
		$this->_addFooterSection( $body );
		$this->_addScriptSection( $body );

		$this->getContainer()->appendChild( $body );
	}

	/**
	 * Adds the page navigation section
	 *
	 * @param \DOMElement $nav
	 */
	protected function _addNavSection( \DOMElement $nav )
	{
		$panel = $this->_dom->createElement( 'div' );
		$panel->setAttribute( 'class', 'panel panel-default' );
		$panel->setAttribute( 'id', 'tmd-nav' );
		$nav->appendChild( $panel );

		$content = $this->_dom->createElement( 'div' );
		$content->setAttribute( 'class', 'panel-body' );
		$panel->appendChild( $content );

		// Tree
		$content->appendChild( $this->_dom->importNode( $this->_tree->getOutput(), true ) );
	}

	/**
	 * Adds the page content section
	 *
	 * @param \DOMElement $content
	 */
	protected function _addContentSection( \DOMElement $content )
	{
		$div = $this->_dom->createElement( 'div' );
		$div->setAttribute( 'class', 'markdown-content' );
		$div->setAttribute( 'id', 'tmd-main-content' );
		$content->appendChild( $div );

		// Add all user messages
		foreach ( $this->_user_messages as $severity => $messages )
		{
			foreach ( $messages as $message )
			{
				$this->_addUserMessage( $div, $severity, $message['title'], $message['message'] );
			}
		}

		// Import parsed content?
		if ( !is_null( $this->_parsed_markdown ) )
		{
			$div->appendChild( $this->getDom()->importNode( $this->_parsed_markdown, true ) );
		}
	}

	/**
	 * Adds the table-of-contents section
	 *
	 * @param \DOMElement $toc
	 */
	protected function _addTOCSection( \DOMElement $toc )
	{
		if ( !is_null( $this->_toc ) )
		{
			$container = $this->getElementWithAttributes( 'div', array('id' => 'toc') );
			$container->appendChild( $this->getDom()->importNode( $this->_toc, true ) );
			$toc->appendChild( $container );
		}
	}

	/**
	 * Adds a user message to the content section
	 *
	 * @param \DOMElement $elem
	 * @param string      $severity
	 * @param string      $header
	 * @param string      $message
	 */
	protected function _addUserMessage( \DOMElement $elem, $severity, $header, $message )
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

	/**
	 * Adds the page footer to content section
	 *
	 * @param \DOMElement $body
	 */
	protected function _addFooterSection( \DOMElement $body )
	{
		$container = $this->getDom()->createElement( 'footer' );
		$body->appendChild( $container );

		$container->appendChild( $this->getElementWithAttributes( 'hr', array('class' => 'clearfix') ) );

		$row = $this->getElementWithAttributes( 'div', array('class' => 'tmd-footer row') );
		$container->appendChild( $row );

		$nav = $this->getElementWithAttributes( 'div', array('class' => 'col-lg-3 col-md-3 col-sm-4 hidden-xs') );
		$nav->appendChild( $this->getDom()->createTextNode( '' ) );
		$row->appendChild( $nav );

		$content = $this->getDom()->createElement( 'div' );
		$row->appendChild( $content );

		$span_company = $this->getElementWithAttributes(
			'span',
			array('class' => 'pull-right small text-muted'),
			sprintf(
				'%s &middot; &copy; %s %s',
				$this->getMetaData( HTMLPage::META_PROJECT_NAME ),
				$this->getMetaData( HTMLPage::META_COMPANY ),
				date( 'Y' )
			)
		);

		if ( !is_null( $this->_toc ) )
		{
			$content->setAttribute( 'class', 'col-lg-7 col-md-7 col-sm-8 col-xs-12' );

			$toc = $this->getDom()->createElement( 'div' );
			$toc->setAttribute( 'class', 'col-lg-2 col-md-2 hidden-sm hidden-xs' );
			$toc->appendChild( $span_company );
			$row->appendChild( $toc );
		}
		else
		{
			$content->setAttribute( 'class', 'col-lg-9 col-md-9 col-sm-8 col-xs-12' );
			$content->appendChild( $span_company );
		}

		$totop = $this->getElementWithAttributes( 'div', array('class' => 'small text-center') );
		$totop->appendChild( $this->getElementWithAttributes( 'a', array('href' => '#'), 'Back to top' ) );
		$content->appendChild( $totop );
	}

	/**
	 * Adds the scripts to the body section
	 *
	 * @param \DOMElement $body
	 */
	protected function _addScriptSection( \DOMElement $body )
	{
		foreach ( $this->getAssets( HTMLPage::ASSET_JS ) as $js_file )
		{
			$file_content = file_get_contents( $js_file );
			$elem         = $this->getElementWithAttributes( 'script', array('type' => 'text/javascript') );
			$elem->appendChild( $this->getDom()->createCDATASection( $file_content ) );
			$body->appendChild( $elem );
		}

		$body->appendChild(
			$this->getElementWithAttributes(
				'script', array('type' => 'text/javascript'),
				'hljs.initHighlightingOnLoad();'
			)
		);
	}

	/**
	 * Prepare the parsed markdown and/or user messages
	 */
	protected function _prepareParsedMarkdown()
	{
		$curent_file_with_root    = $this->getTree()->getSearch()->getCurrentFile( false );
		$curent_file_without_root = $this->getTree()->getSearch()->getCurrentFile( true );

		// Prepare the parsedown content
		if ( empty($curent_file_without_root) )
		{
			if ( $this->_tree->getSearch()->isCurrentFileValid() )
			{
				$this->_user_messages['info'][] = array(
					'title'   => 'No file selected',
					'message' => 'Browse the file tree on the left and click a file.',
				);
			}
			else
			{
				$this->_user_messages['danger'][] = array(
					'title'   => 'Invalid request',
					'message' => 'The file you requested is not accessable by this application.',
				);
			}
		}
		elseif ( file_exists( $curent_file_with_root ) && is_dir( $curent_file_with_root ) )
		{
			$this->_user_messages['warning'][] = array(
				'title'   => 'Directory selected',
				'message' => 'Cannot display the content of directories.
							  Browse the file tree on the left and click a file.',
			);
		}
		elseif ( file_exists( $curent_file_with_root ) && is_readable( $curent_file_with_root ) )
		{
			try
			{
				// Parsedown execution
				$parser = new \ParsedownExtra();

				$finfo         = new \finfo( FILEINFO_MIME_ENCODING );
				$file_encoding = $finfo->file( $curent_file_with_root );
				$file_content  = iconv( $file_encoding, 'utf-8', file_get_contents( $curent_file_with_root ) );

				$markdown = $parser->text( utf8_decode( $file_content ) );

				if ( !empty($markdown) )
				{
					$dom_implementation = new \DOMImplementation();
					$doc_type           = $dom_implementation->createDocumentType( 'html', '', '' );
					$dom                = $dom_implementation->createDocument( '', 'html', $doc_type );
					libxml_use_internal_errors( true );

					$dom->loadHTML( $markdown );

					$errors = libxml_get_errors();

					if ( !empty($errors) )
					{
						$messages = array();

						/** @var \LibXMLError $error */
						foreach ( $errors as $error )
						{
							$messages[] = $error->message;
						}

						$this->_user_messages['warning'][] = array(
							'title'   => 'This markdown file contains erroneous code',
							'message' => join( ', ', $messages ),
						);
					}

					$this->_parsed_markdown = $dom->documentElement;
				}
				else
				{
					$this->_user_messages['warning'][] = array(
						'title'   => ":-( You're not done yet!",
						'message' => 'This file has no content at all.',
					);
				}
			}
			catch ( \Exception $e )
			{
				$this->_parsed_markdown           = null;
				$this->_user_messages['danger'][] = array(
					'title'   => "Oops! An error occured while parsing markdown file",
					'message' => $curent_file_without_root . ': ' . $e->getMessage(),
				);
			}
		}
		else
		{
			$this->_user_messages['danger'][] = array(
				'title'   => '404',
				'message' => 'The file you requested does not exist or is not readable.',
			);
		}
	}

	/**
	 * Prepare the TOC
	 */
	protected function _prepareTOC()
	{
		if ( !is_null( $this->_parsed_markdown ) )
		{

			// setup xpath, this can be factored out
			$xpath = new \DOMXPath( $this->_parsed_markdown->ownerDocument );

			// grab all headings h2 and down from the document
			$headings = array('h2', 'h3');
			foreach ( $headings as $k => $v )
			{
				$headings[$k] = "self::$v";
			}

			$query_headings = join( ' or ', $headings );
			$query          = "//*[$query_headings]";
			$headings       = $xpath->query( $query );

			if ( $headings->length > 0 )
			{
				$dom_implementation = new \DOMImplementation();
				$doc_type           = $dom_implementation->createDocumentType( 'html', '', '' );
				$dom                = $dom_implementation->createDocument( '', 'html', $doc_type );
				$container          = $dom->documentElement;

				$toc_headline = $dom->createElement( 'h2', 'Table of Contents' );
				$container->appendChild( $toc_headline );

				// setup the table of contents element
				$toc_list = $dom->createElement( 'ul' );
				$toc_list->setAttribute( 'class', 'nav tmd-toc-1' );
				$container->appendChild( $toc_list );

				// iterate through headings and build the table of contents
				$current_level = 2;

				/** @var array|\DOMNode[] $parents */
				$parents = array(false, $toc_list);
				$i       = 0;

				/** @var \DOMElement $headline */
				foreach ( $headings as $headline )
				{
					$level = (int)$headline->tagName[1];
					$name  = $headline->textContent; // no support for formatting

					while ( $level > $current_level )
					{
						if ( !$parents[$current_level - 1]->lastChild )
						{
							$li = $dom->createElement( 'li' );
							$parents[$current_level - 1]->appendChild( $li );
						}

						$sublist = $dom->createElement( 'ul' );
						$sublist->setAttribute( 'class', 'nav tmd-toc-2' );
						$parents[$current_level - 1]->lastChild->appendChild( $sublist );
						$parents[$current_level] = $sublist;
						$current_level++;
					}

					while ( $level < $current_level )
					{
						$current_level--;
					}

					$anchor_id = strtolower( preg_replace( "#[^0-9a-z]#i", '-', $name ) ) . '__' . ++$i;

					$line = $dom->createElement( 'li' );
					$link = $dom->createElement( 'a', $name );
					$line->appendChild( $link );
					$parents[$current_level - 1]->appendChild( $line );

					// setup the anchors
					$headline->setAttribute( 'id', $anchor_id );
					$link->setAttribute( 'href', '#' . $anchor_id );

					$top_link = $headline->ownerDocument->createElement( 'a', 'Back to top' );
					$top_link->setAttribute( 'class', 'tmd-h-toplink pull-right' );
					$top_link->setAttribute( 'href', '#' );

					$headline->appendChild( $top_link );
				}

				// Set the TOC
				$this->_toc = $container;
			}
		}
	}
}
