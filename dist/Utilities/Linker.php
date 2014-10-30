<?php
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

	/**
	 * @param Search $search
	 */
	public function __construct( Search $search )
	{
		$this->search = $search;
	}

	/**
	 * @param \DOMNode $node
	 *
	 * @throws \InvalidArgumentException
	 * @return \DOMElement[]
	 */
	public function getInternalLinks( \DOMNode $node )
	{
		$this->guardHasOwnerDocument( $node );

		$internal_links = array();
		$xpath          = new \DOMXPath( $node->ownerDocument );
		$a_tags         = $xpath->query( '*//a[@href]' );

		/** @var \DOMElement $a_tag */
		foreach ( $a_tags as $a_tag )
		{
			$href = $a_tag->getAttribute( 'href' );
			if ( !$this->isHyperRefIgnored( $href ) )
			{
				$internal_links[] = $a_tag;
			}
		}

		return $internal_links;
	}

	/**
	 * @param \DOMNode $node
	 *
	 * @throws \InvalidArgumentException
	 */
	private function guardHasOwnerDocument( \DOMNode $node )
	{
		if ( !($node->ownerDocument instanceof \DOMDocument) )
		{
			throw new \InvalidArgumentException( 'Node has no owner document (DOM)' );
		}
	}

	/**
	 * @param string $href
	 *
	 * @return bool
	 */
	private function isHyperRefIgnored( $href )
	{
		$file_path = $this->getFilePathWithRootDir( $href );

		if ( file_exists( $file_path ) )
		{
			return $this->search->isPathIgnored( $file_path );
		}
		else
		{
			return true;
		}
	}

	/**
	 * @param string $href
	 *
	 * @return string
	 */
	private function getFilePathWithRootDir( $href )
	{
		$root_dir          = $this->search->getOptions()->get( Opt::DIR_ROOT );
		$url_without_query = $this->removeQueryStringIfExists( $href );

		return $root_dir . DIRECTORY_SEPARATOR . $url_without_query;
	}

	/**
	 * @param string $href
	 *
	 * @return string
	 */
	private function removeQueryStringIfExists( $href )
	{
		return preg_replace( "#\?.*$#", '', $href );
	}

	/**
	 * @param \DOMElement $node
	 */
	public function modifyInternalLink( \DOMElement $node )
	{
		$url          = $node->getAttribute( 'href' );
		$url_path     = parse_url( $url, PHP_URL_PATH );
		$query_string = parse_url( $url, PHP_URL_QUERY );

		$modified_query = $this->getModifiedQueryString( $query_string, $url_path );
		$modified_href  = '?' . $modified_query;

		$node->setAttribute( 'href', $modified_href );
	}

	/**
	 * @param string $query_string
	 * @param string $url_path
	 *
	 * @return string
	 */
	private function getModifiedQueryString( $query_string, $url_path )
	{
		$query_vars = array();
		if ( !empty($query_string) )
		{
			parse_str( $query_string, $query_vars );
		}

		if ( isset($query_vars['q']) )
		{
			$query_vars['tmd_q'] = $query_vars['q'];
			unset($query_vars['q']);
		}

		if ( !isset($query_vars['tmd_q']) )
		{
			$query_vars['tmd_q'] = $this->search->getOptions()->get( Opt::SEARCH_TERM );
		}

		$query_vars['tmd_f'] = $url_path;
		unset($query_vars['tmd_r']);

		if ( isset($query_vars['raw']) )
		{
			$query_vars['tmd_r'] = '1';
			unset($query_vars['raw']);
		}

		ksort( $query_vars, SORT_STRING );

		return http_build_query( $query_vars );
	}
}