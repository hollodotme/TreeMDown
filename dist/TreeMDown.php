<?php
/**
 * Main class for using TreeMDown
 * @author hwoltersdorf
 */

namespace hollodotme\TreeMDown;

use hollodotme\TreeMDown\FileSystem\Search;
use hollodotme\TreeMDown\Rendering\HTMLPage;
use hollodotme\TreeMDown\Rendering\HTMLTree;

/**
 * Class TreeMDown
 * @package hollodotme\TreeMDown
 */
class TreeMDown
{

	/**
	 * The Search instance
	 * @var Search|null
	 */
	protected $_search = null;

	/**
	 * The HTMLTree instance
	 * @var HTMLTree|null
	 */
	protected $_tree = null;

	/**
	 * The default file
	 * @var string
	 */
	protected $_default_file = 'index.md';

	/**
	 * Page metadata
	 * @var array
	 */
	protected $_metadata = array(
		'project_name'      => 'TreeMDown',
		'short_description' => "[triː <'em> daʊn]",
		'company_name'      => 'hollodotme',
	);

	/**
	 * Constructor
	 *
	 * @param string $root_dir Root directory
	 */
	public function __construct( $root_dir = '.' )
	{
		// Init search
		$this->_search = new Search( $root_dir, isset($_GET['tmd_q']) ? $_GET['tmd_q'] : '' );
		$this->_search->setCurrentFile( isset($_GET['tmd_f']) ? $_GET['tmd_f'] : $this->_default_file );
		$this->_search->setExcludePatterns( array('.*') );
		$this->_search->setIncludePatterns( array('*.md', '*.markdown') );

		// Init tree
		$this->_tree = new HTMLTree( $this->_search );
	}

	/**
	 * Set the project name
	 *
	 * @param string $project_name Project name
	 */
	public function setProjectName( $project_name )
	{
		$this->_metadata['project_name'] = strval( $project_name );
	}

	/**
	 * Return the project name
	 * @return string
	 */
	public function getProjectName()
	{
		return $this->_metadata['project_name'];
	}

	/**
	 * Set the short description
	 *
	 * @param string $short_description Short description
	 */
	public function setShortDescription( $short_description )
	{
		$this->_metadata['short_description'] = strval( $short_description );
	}

	/**
	 * Return the short description
	 * @return string
	 */
	public function getShortDescription()
	{
		return $this->_metadata['short_description'];
	}

	/**
	 * Set the company_name name
	 *
	 * @param string $company_name Company name
	 */
	public function setCompanyName( $company_name )
	{
		$this->_metadata['company_name'] = $company_name;
	}

	/**
	 * Return the company name
	 * @return string
	 */
	public function getCompanyName()
	{
		return $this->_metadata['company_name'];
	}

	/**
	 * Return the default file (relative to root directory)
	 * @return string
	 */
	public function getDefaultFile()
	{
		return $this->_default_file;
	}

	/**
	 * Set the default file (relative to root directory)
	 *
	 * @param string $default_file Default file
	 */
	public function setDefaultFile( $default_file )
	{
		$this->_default_file = ltrim( trim( $default_file ), "\t\n\r\0\x0B\/" );

		$this->_search->setCurrentFile( isset($_GET['tmd_f']) ? $_GET['tmd_f'] : $this->_default_file );
	}

	/**
	 * Hide empty folders
	 */
	public function hideEmptyFolders()
	{
		$this->_tree->setFlags( $this->_tree->getFlags() | HTMLTree::EXCLUDE_EMPTY_FOLDERS );
	}

	/**
	 * Show empty folders
	 */
	public function showEmptyFolders()
	{
		$this->_tree->setFlags( $this->_tree->getFlags() & ~HTMLTree::EXCLUDE_EMPTY_FOLDERS );
	}

	/**
	 * Set the include file patterns
	 *
	 * @example array('*.md', '*.markdown')
	 *
	 * @param array $patterns Include patterns
	 */
	public function setIncludePatterns( array $patterns )
	{
		$this->_search->setIncludePatterns( $patterns );
	}

	/**
	 * Return the include file patterns
	 * @return array
	 */
	public function getIncludePatterns()
	{
		return $this->_search->getIncludePatterns();
	}

	/**
	 * Set the exclude file/path patterns
	 *
	 * @example array('.*')
	 *
	 * @param array $patterns Exclude patterns
	 */
	public function setExcludePatterns( array $patterns )
	{
		$this->_search->setExcludePatterns( $patterns );
	}

	/**
	 * Return the file/path exclude patterns
	 * @return array
	 */
	public function getExcludePatterns()
	{
		return $this->_search->getExcludePatterns();
	}

	/**
	 * Return the page output (HTML / Raw)
	 *
	 * @param array $headers Output headers
	 *
	 * @return string
	 */
	public function getOutput( array &$headers = array() )
	{
		// Raw display of current file?
		if ( isset($_GET['tmd_r']) && !empty($_GET['tmd_r']) )
		{
			$headers['Content-type'] = 'text/plain; charset=UTF-8';

			if ( $this->_search->isCurrentFileValid() )
			{
				$output = file_get_contents( $this->_search->getCurrentFile( false ) );
			}
			else
			{
				$output = 'Your current selection is not valid.';
			}
		}
		else
		{
			$headers['Content-type'] = 'text/html; charset=UTF-8';

			$this->_tree->buildTree();

			$page = new HTMLPage( $this->_tree );
			$page->setProjectName( $this->getProjectName() );
			$page->setShortDescription( $this->getShortDescription() );
			$page->setCompany( $this->getCompanyName() );

			$output = $page->getOutput();
		}

		return $output;
	}

	/**
	 * Display the output
	 */
	public function display()
	{
		$headers = array();
		$output  = $this->getOutput( $headers );

		foreach ( $headers as $type => $value )
		{
			header( "{$type}: {$value}" );
		}

		echo $output;
	}
}