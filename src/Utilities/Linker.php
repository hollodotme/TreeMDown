<?php declare(strict_types=1);
/**
 * Document linker for internal links
 *
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Utilities;

use hollodotme\TreeMDown\FileSystem\Search;
use hollodotme\TreeMDown\Misc\Opt;

class Linker
{
	/** @var Search */
	private $search;

	public function __construct( Search $search )
	{
		$this->search = $search;
	}

	/**
	 * @param \DOMNode $node
	 *
	 * @throws \InvalidArgumentException
	 * @return array|\DOMElement[]
	 */
	public function getInternalLinks( \DOMNode $node ) : array
	{
		$this->guardHasOwnerDocument( $node );

		$internalLinks = [];
		$xpath         = new \DOMXPath( $node->ownerDocument );
		$aTags         = $xpath->query( '*//a[@href]' );

		/** @var \DOMElement $aTag */
		foreach ( $aTags as $aTag )
		{
			$href = $aTag->getAttribute( 'href' );
			if ( !$this->isHyperRefIgnored( $href ) )
			{
				$internalLinks[] = $aTag;
			}
		}

		return $internalLinks;
	}

	/**
	 * @param \DOMNode $node
	 *
	 * @throws \InvalidArgumentException
	 */
	private function guardHasOwnerDocument( \DOMNode $node ) : void
	{
		if ( !($node->ownerDocument instanceof \DOMDocument) )
		{
			throw new \InvalidArgumentException( 'Node has no owner document (DOM)' );
		}
	}

	private function isHyperRefIgnored( string $href ) : bool
	{
		$filePath = $this->getFilePathWithRootDir( $href );

		if ( file_exists( $filePath ) )
		{
			return $this->search->isPathIgnored( $filePath );
		}

		return true;
	}

	private function getFilePathWithRootDir( string $href ) : string
	{
		$rootDir         = $this->search->getOptions()->get( Opt::DIR_ROOT );
		$urlWithoutQuery = $this->removeQueryStringIfExists( $href );

		return $rootDir . DIRECTORY_SEPARATOR . $urlWithoutQuery;
	}

	private function removeQueryStringIfExists( string $href ) : string
	{
		return preg_replace( "#\?.*$#", '', $href );
	}

	public function modifyInternalLink( \DOMElement $node ) : void
	{
		$url         = $node->getAttribute( 'href' );
		$urlPath     = (string)parse_url( $url, PHP_URL_PATH );
		$queryString = (string)parse_url( $url, PHP_URL_QUERY );

		$modifiedQuery = $this->getModifiedQueryString( $queryString, $urlPath );
		$modifiedHref  = '?' . $modifiedQuery;

		$node->setAttribute( 'href', $modifiedHref );
	}

	private function getModifiedQueryString( string $queryString, string $urlPath ) : string
	{
		$queryVars = $this->search->getOptions()->get( Opt::BASE_PARAMS );

		if ( !empty( $queryString ) )
		{
			parse_str( $queryString, $parsedVars );
			$queryVars = array_merge( $queryVars, $parsedVars );
		}

		if ( isset( $queryVars['q'] ) )
		{
			$queryVars['tmd_q'] = $queryVars['q'];
			unset( $queryVars['q'] );
		}

		$queryVars['tmd_f'] = $urlPath;
		unset( $queryVars['tmd_r'] );

		if ( isset( $queryVars['raw'] ) )
		{
			$queryVars['tmd_r'] = '1';
			unset( $queryVars['raw'] );
		}

		ksort( $queryVars, SORT_STRING );

		return http_build_query( $queryVars );
	}
}
