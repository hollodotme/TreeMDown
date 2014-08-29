<?php
/**
 * Body of HTMLPage
 *
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

use hollodotme\TreeMDown\Rendering\HTMLPage;
use hollodotme\TreeMDown\Utilities\FileEncoder;

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
	 * The TOF
	 *
	 * @var null|\DOMElement
	 */
	protected $_tof = null;

	/**
	 * User messages
	 *
	 * @var array
	 */
	protected $_user_messages = array();

	/**
	 * Github ribbon enabled?
	 *
	 * @var bool
	 */
	protected $_github_ribbon_enabled = false;

	/**
	 * Enable/disable github ribbon
	 *
	 * @param bool $enable
	 */
	public function enableGithubRibbon( $enable )
	{
		$this->_github_ribbon_enabled = $enable;
	}

	/**
	 * Prepare the content
	 */
	public function prepare()
	{
		// Prepare parsed markdown
		$this->_prepareParsedMarkdown();

		// Prepare the TOC
		$this->_prepareTOC();

		// Prepare the TOF
		$this->_prepareTOF();
	}

	/**
	 * Add nodes to the DOM
	 */
	public function addNodes()
	{
		// Add Body element
		$body = $this->getElementWithAttributes( 'body', array( 'role' => 'document' ) );
		$this->getContainer()->appendChild( $body );

		if ( !is_null( $this->_toc ) )
		{
			$body->setAttribute( 'data-spy', 'scroll' );
			$body->setAttribute( 'data-target', '#toc' );
			$body->setAttribute( 'data-offset', '75' );
		}

		// Add header nav section
		$header = new Header( $body, $this->_tree );
		$header->prepare();
		$header->addNodes();

		$section = $this->getDom()->createElement( 'section' );
		$body->appendChild( $section );

		$container = $this->getDom()->createElement( 'div' );
		$container->setAttribute( 'class', 'container-fluid' );
		$container->setAttribute( 'role', 'main' );
		$section->appendChild( $container );

		$row = $this->getDom()->createElement( 'div' );
		$row->setAttribute( 'class', 'row' );
		$container->appendChild( $row );

		$sidebar_column = $this->getDom()->createElement( 'div' );
		$sidebar_column->setAttribute( 'class', 'col-lg-3 col-md-3 col-sm-4 hidden-xs sidebar' );
		$sidebar_column->setAttribute( 'id', 'tmd-sidebar' );
		$row->appendChild( $sidebar_column );

		$content_column = $this->getDom()->createElement( 'div' );
		$row->appendChild( $content_column );

		// TOC exists?
		if ( !is_null( $this->_toc ) )
		{
			$content_column->setAttribute(
				'class',
				'col-lg-7 col-lg-offset-3 col-md-7 col-md-offset-3 col-sm-8 col-sm-offset-4 col-xs-12 content'
			);

			// Add TOC column
			$toc_column = $this->getDom()->createElement( 'div' );
			$toc_column->setAttribute( 'class', 'col-lg-2 col-md-2 hidden-sm hidden-xs toc' );
			$toc_column->setAttribute( 'id', 'toc' );
			$row->appendChild( $toc_column );

			$toc = new TableOfContents( $toc_column, $this->_tree );
			$toc->setToc( $this->_toc );

			$toc->prepare();
			$toc->addNodes();

			if ( !is_null( $this->_tof ) )
			{
				$tof = new TableOfFigures( $toc_column, $this->_tree );
				$tof->setTof( $this->_tof );

				$tof->prepare();
				$tof->addNodes();
			}
		}
		elseif ( !is_null( $this->_tof ) )
		{
			$content_column->setAttribute(
				'class',
				'col-lg-7 col-lg-offset-3 col-md-7 col-md-offset-3 col-sm-8 col-sm-offset-4 col-xs-12 content'
			);

			// Add TOF column
			$tof_column = $this->getDom()->createElement( 'div' );
			$tof_column->setAttribute( 'class', 'col-lg-2 col-md-2 hidden-sm hidden-xs toc' );
			$tof_column->setAttribute( 'id', 'toc' );
			$row->appendChild( $tof_column );

			$tof = new TableOfFigures( $tof_column, $this->_tree );
			$tof->setTof( $this->_tof );

			$tof->prepare();
			$tof->addNodes();
		}
		else
		{
			$content_column->setAttribute(
				'class',
				'col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-8 col-sm-offset-4 col-xs-12 content'
			);
		}

		// Add sidebar section
		$sidebar = new Sidebar( $sidebar_column, $this->_tree );
		$sidebar->prepare();
		$sidebar->addNodes();

		// Add GitHub ribbon?
		if ( $this->_github_ribbon_enabled )
		{
			$github_ribbon = new GithubRibbon( $sidebar_column, $this->_tree );
			$github_ribbon->prepare();
			$github_ribbon->addNodes();
		}

		// Add content section
		$content = new Content( $content_column, $this->_tree );
		$content->setUserMessages( $this->_user_messages );
		if ( !is_null( $this->_parsed_markdown ) )
		{
			$content->setParsedMarkdown( $this->_parsed_markdown );
		}
		$content->prepare();
		$content->addNodes();

		// Add footer section
		$footer = new Footer( $body, $this->_tree );
		$footer->prepare();
		$footer->addNodes();

		// Add scripts section
		$scripts = new Scripts( $body, $this->_tree );
		$scripts->setAssetsArray( $this->_assets );
		$scripts->prepare();
		$scripts->addNodes();
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

				$file_encoder = new FileEncoder( $curent_file_with_root );
				$markdown     = $parser->text( utf8_decode( $file_encoder->getFileContents() ) );

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
			$query    = "//*[self::h2 or self::h3]";
			$headings = $xpath->query( $query );

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
				$parents = array( false, $toc_list );
				$i       = 0;

				/** @var \DOMElement $headline */
				foreach ( $headings as $headline )
				{
					$level = (int)$headline->tagName[1];
					$name  = $headline->textContent; // no support for formatting

					while ( $level > $current_level )
					{
						if ( !$parents[ $current_level - 1 ]->lastChild )
						{
							$li = $dom->createElement( 'li' );
							$parents[ $current_level - 1 ]->appendChild( $li );
						}

						$sublist = $dom->createElement( 'ul' );
						$sublist->setAttribute( 'class', 'nav tmd-toc-2' );
						$parents[ $current_level - 1 ]->lastChild->appendChild( $sublist );
						$parents[ $current_level ] = $sublist;
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
					$parents[ $current_level - 1 ]->appendChild( $line );

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

	/**
	 * Prepare the table of figures
	 */
	protected function _prepareTOF()
	{
		if ( !is_null( $this->_parsed_markdown ) )
		{
			// setup xpath, this can be factored out
			$xpath = new \DOMXPath( $this->_parsed_markdown->ownerDocument );

			$query  = "//*[self::img]";
			$images = $xpath->query( $query );

			if ( $images->length > 0 )
			{
				$dom_implementation = new \DOMImplementation();
				$doc_type           = $dom_implementation->createDocumentType( 'html', '', '' );
				$dom                = $dom_implementation->createDocument( '', 'html', $doc_type );
				$container          = $dom->documentElement;

				$tof_headline = $dom->createElement( 'h2', 'Figures' );
				$container->appendChild( $tof_headline );

				// setup the table of contents element
				$tof_list = $dom->createElement( 'ul' );
				$tof_list->setAttribute( 'class', 'tmd-toc-1' );
				$container->appendChild( $tof_list );

				/** @var \DOMElement $image */
				foreach ( $images as $i => $image )
				{
					// Image alt text
					$name = $image->getAttribute( 'alt' );

					// Image title
					if ( empty($name) )
					{
						$name = $image->getAttribute( 'title' );
					}

					// Image filename
					if ( empty($name) )
					{
						$name = basename( $image->getAttribute( 'src' ) );
					}

					$li   = $dom->createElement( 'li' );
					$link = $dom->createElement( 'a', $name );
					$li->appendChild( $link );
					$tof_list->appendChild( $li );

					// setup the anchors
					$anchor_id = 'figure_' . strtolower( preg_replace( "#[^0-9a-z]#i", '-', $name ) ) . '__' . $i;
					$image->setAttribute( 'id', $anchor_id );
					$link->setAttribute( 'href', '#' . $anchor_id );
				}

				// Set the TOF
				$this->_tof = $container;
			}
		}
	}
}
