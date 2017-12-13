<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

use hollodotme\TreeMDown\Misc\Opt;
use hollodotme\TreeMDown\Utilities\FileEncoder;
use hollodotme\TreeMDown\Utilities\Linker;

/**
 * Class Body
 *
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class Body extends AbstractSection
{
	/** @var \DOMElement */
	protected $parsedMarkdown;

	/** @var \DOMElement */
	protected $tableOfContents;

	/** @var \DOMElement */
	protected $tableOfFigures;

	/** @var array */
	protected $userMessages = [];

	public function prepare() : void
	{
		// Prepare parsed markdown
		$this->prepareParsedMarkdown();

		// Prepare the TOC
		$this->prepareTableOfContents();

		// Prepare the TOF
		$this->prepareTableOfFigures();
	}

	protected function prepareParsedMarkdown() : void
	{
		$curentFileWithRoot    = $this->getHtmlTree()->getSearch()->getCurrentFile();
		$curentFileWithoutRoot = $this->getHtmlTree()->getSearch()->getCurrentFile( true );

		if ( '' === $curentFileWithoutRoot )
		{
			if ( $this->htmlTree->getSearch()->isCurrentFileValid() )
			{
				$this->userMessages['info'][] = [
					'title'   => 'No file selected',
					'message' => 'Browse the file tree on the left and click a file.',
				];

				return;
			}

			$this->userMessages['danger'][] = [
				'title'   => 'Invalid request',
				'message' => 'The file you requested is not accessable by this application.',
			];

			return;
		}

		if ( file_exists( $curentFileWithRoot ) && is_dir( $curentFileWithRoot ) )
		{
			$this->userMessages['warning'][] = [
				'title'   => 'Directory selected',
				'message' => 'Cannot display the content of directories.
							  Browse the file tree on the left and click a file.',
			];

			return;
		}

		if ( file_exists( $curentFileWithRoot ) && is_readable( $curentFileWithRoot ) )
		{
			try
			{
				// Parsedown execution
				$parser = new \ParsedownExtra();

				$fileEncoder = new FileEncoder( $curentFileWithRoot );
				$markdown    = $parser->text( utf8_decode( $fileEncoder->getFileContents() ) );

				if ( !empty( $markdown ) )
				{
					$domImplementation = new \DOMImplementation();
					$docType           = $domImplementation->createDocumentType( 'html', '', '' );
					$dom               = $domImplementation->createDocument( '', 'html', $docType );
					libxml_use_internal_errors( true );

					$dom->loadHTML( $markdown );

					$errors = libxml_get_errors();

					if ( !empty( $errors ) )
					{
						$messages = [];

						/** @var \LibXMLError $error */
						foreach ( $errors as $error )
						{
							$messages[] = $error->message;
						}

						$this->userMessages['warning'][] = [
							'title'   => 'This markdown file contains erroneous code',
							'message' => implode( ', ', $messages ),
						];
					}

					$this->parsedMarkdown = $dom->documentElement;

					$this->modifyInternalLinks();

					return;
				}

				$this->userMessages['warning'][] = [
					'title'   => ":-( You're not done yet!",
					'message' => 'This file has no content at all.',
				];
			}
			catch ( \Throwable $e )
			{
				$this->parsedMarkdown           = null;
				$this->userMessages['danger'][] = [
					'title'   => 'Oops! An error occured while parsing markdown file',
					'message' => $curentFileWithoutRoot . ': ' . $e->getMessage(),
				];
			}

			return;
		}

		$this->userMessages['danger'][] = [
			'title'   => '404',
			'message' => 'The file you requested does not exist or is not readable.',
		];
	}

	protected function prepareTableOfContents() : void
	{
		if ( null === $this->parsedMarkdown )
		{
			return;
		}

		// setup xpath, this can be factored out
		$xpath = new \DOMXPath( $this->parsedMarkdown->ownerDocument );

		// grab all headings h2 and down from the document
		$query    = '//*[self::h2 or self::h3]';
		$headings = $xpath->query( $query );

		if ( 0 === $headings->length )
		{
			return;
		}

		$domImplementation = new \DOMImplementation();
		$docType           = $domImplementation->createDocumentType( 'html', '', '' );
		$dom               = $domImplementation->createDocument( '', 'html', $docType );
		$container         = $dom->documentElement;

		$tocHeadline = $dom->createElement( 'h4', 'Table of Contents' );
		$container->appendChild( $tocHeadline );

		// setup the table of contents element
		$tocList = $dom->createElement( 'ul' );
		$tocList->setAttribute( 'class', 'nav tmd-toc-1' );
		$container->appendChild( $tocList );

		// iterate through headings and build the table of contents
		$currentLevel = 2;

		/** @var array|\DOMNode[] $parents */
		$parents = [false, $tocList];
		$i       = 0;

		/** @var \DOMElement $headline */
		foreach ( $headings as $headline )
		{
			$level = (int)$headline->tagName[1];
			$name  = $headline->textContent; // no support for formatting

			while ( $level > $currentLevel )
			{
				if ( !$parents[ $currentLevel - 1 ]->lastChild )
				{
					$li = $dom->createElement( 'li' );
					$parents[ $currentLevel - 1 ]->appendChild( $li );
				}

				$sublist = $dom->createElement( 'ul' );
				$sublist->setAttribute( 'class', 'nav tmd-toc-2' );
				$parents[ $currentLevel - 1 ]->lastChild->appendChild( $sublist );
				$parents[ $currentLevel ] = $sublist;
				$currentLevel++;
			}

			while ( $level < $currentLevel )
			{
				$currentLevel--;
			}

			$anchorId = strtolower( preg_replace( '#[^\da-z]#i', '-', $name ) ) . '__' . ++$i;

			$line = $dom->createElement( 'li' );
			$link = $dom->createElement( 'a', $name );
			$line->appendChild( $link );
			$parents[ $currentLevel - 1 ]->appendChild( $line );

			$headline->setAttribute( 'id', $anchorId );
			$link->setAttribute( 'href', '#' . $anchorId );

			$topLink = $headline->ownerDocument->createElement( 'a', 'Back to top' );
			$topLink->setAttribute( 'class', 'tmd-h-toplink pull-right' );
			$topLink->setAttribute( 'href', '#' );

			$headline->appendChild( $topLink );
		}

		$this->tableOfContents = $container;
	}

	protected function prepareTableOfFigures() : void
	{
		if ( null === $this->parsedMarkdown )
		{
			return;
		}

		$xpath = new \DOMXPath( $this->parsedMarkdown->ownerDocument );

		$query  = '//*[self::img]';
		$images = $xpath->query( $query );

		if ( 0 === $images->length )
		{
			return;
		}

		$domImplementation = new \DOMImplementation();
		$docType           = $domImplementation->createDocumentType( 'html', '', '' );
		$dom               = $domImplementation->createDocument( '', 'html', $docType );
		$container         = $dom->documentElement;

		$tofHeadline = $dom->createElement( 'h4', 'Figures' );
		$container->appendChild( $tofHeadline );

		// setup the table of contents element
		$tofList = $dom->createElement( 'ul' );
		$tofList->setAttribute( 'class', 'tmd-toc-1' );
		$container->appendChild( $tofList );

		/** @var \DOMElement $image */
		foreach ( $images as $i => $image )
		{
			// Image alt text
			$name = $image->getAttribute( 'alt' );

			// Image title
			if ( empty( $name ) )
			{
				$name = $image->getAttribute( 'title' );
			}

			// Image filename
			if ( empty( $name ) )
			{
				$name = basename( $image->getAttribute( 'src' ) );
			}

			$li   = $dom->createElement( 'li' );
			$link = $dom->createElement( 'a', $name );
			$li->appendChild( $link );
			$tofList->appendChild( $li );

			// setup the anchors
			$anchorId = 'figure_' . strtolower( preg_replace( '#[^\da-z]#i', '-', $name ) ) . '__' . $i;
			$image->setAttribute( 'id', $anchorId );
			$link->setAttribute( 'href', '#' . $anchorId );
		}

		$this->tableOfFigures = $container;
	}

	public function addNodes() : void
	{
		// Add Body element
		$body = $this->getElementWithAttributes( 'body', ['role' => 'document'] );
		$this->getDomContainer()->appendChild( $body );

		if ( null !== $this->tableOfContents )
		{
			$body->setAttribute( 'data-spy', 'scroll' );
			$body->setAttribute( 'data-target', '#toc' );
			$body->setAttribute( 'data-offset', '75' );
		}

		// Add header nav section
		$header = new Header( $body, $this->htmlTree );
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

		$sidebarColumn = $this->getDom()->createElement( 'div' );
		$sidebarColumn->setAttribute( 'class', 'col-lg-3 col-md-3 col-sm-4 hidden-xs sidebar' );
		$sidebarColumn->setAttribute( 'id', 'tmd-sidebar' );
		$sidebarColumn->setIdAttribute( 'id', true );
		$row->appendChild( $sidebarColumn );

		$contentColumn = $this->getDom()->createElement( 'div' );
		$row->appendChild( $contentColumn );

		// TOC exists?
		if ( null !== $this->tableOfContents )
		{
			$contentColumn->setAttribute(
				'class',
				'col-lg-7 col-lg-offset-3 col-md-7 col-md-offset-3 col-sm-8 col-sm-offset-4 col-xs-12 content'
			);

			// Add TOC column
			$tocColumn = $this->getDom()->createElement( 'div' );
			$tocColumn->setAttribute( 'class', 'col-lg-2 col-md-2 hidden-sm hidden-xs toc' );
			$tocColumn->setAttribute( 'id', 'toc' );
			$row->appendChild( $tocColumn );

			$toc = new TableOfContents( $tocColumn, $this->htmlTree );
			$toc->setTableOfContents( $this->tableOfContents );

			$toc->prepare();
			$toc->addNodes();

			if ( null !== $this->tableOfFigures )
			{
				$tof = new TableOfFigures( $tocColumn, $this->htmlTree );
				$tof->setTableOfFigures( $this->tableOfFigures );

				$tof->prepare();
				$tof->addNodes();
			}
		}
		elseif ( null !== $this->tableOfFigures )
		{
			$contentColumn->setAttribute(
				'class',
				'col-lg-7 col-lg-offset-3 col-md-7 col-md-offset-3 col-sm-8 col-sm-offset-4 col-xs-12 content'
			);

			// Add TOF column
			$tofColumn = $this->getDom()->createElement( 'div' );
			$tofColumn->setAttribute( 'class', 'col-lg-2 col-md-2 hidden-sm hidden-xs toc' );
			$tofColumn->setAttribute( 'id', 'toc' );
			$row->appendChild( $tofColumn );

			$tof = new TableOfFigures( $tofColumn, $this->htmlTree );
			$tof->setTableOfFigures( $this->tableOfFigures );

			$tof->prepare();
			$tof->addNodes();
		}
		else
		{
			$contentColumn->setAttribute(
				'class',
				'col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-8 col-sm-offset-4 col-xs-12 content'
			);
		}

		// Add sidebar section
		$sidebar = new Sidebar( $sidebarColumn, $this->htmlTree );
		$sidebar->prepare();
		$sidebar->addNodes();

		// Add GitHub ribbon?
		if ( $this->getOptions()->get( Opt::GITHUB_RIBBON_ENABLED ) )
		{
			$githubRibbon = new GithubRibbon( $sidebarColumn, $this->htmlTree );
			$githubRibbon->prepare();
			$githubRibbon->addNodes();
		}

		// Add content section
		$content = new Content( $contentColumn, $this->htmlTree );
		$content->setUserMessages( $this->userMessages );
		if ( null !== $this->parsedMarkdown )
		{
			$content->setParsedMarkdown( $this->parsedMarkdown );
		}
		$content->prepare();
		$content->addNodes();

		// Add footer section
		$footer = new Footer( $body, $this->htmlTree );
		$footer->prepare();
		$footer->addNodes();

		// Add scripts section
		$scripts = new Scripts( $body, $this->htmlTree );
		$scripts->setAssetsArray( $this->assets );
		$scripts->prepare();
		$scripts->addNodes();
	}

	/**
	 * @throws \InvalidArgumentException
	 */
	private function modifyInternalLinks() : void
	{
		if ( null === $this->parsedMarkdown )
		{
			return;
		}

		$linker = new Linker( $this->htmlTree->getSearch() );

		foreach ( $linker->getInternalLinks( $this->parsedMarkdown ) as $internalLink )
		{
			$linker->modifyInternalLink( $internalLink );
		}
	}
}
