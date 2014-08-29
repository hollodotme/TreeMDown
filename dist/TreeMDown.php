<?php
/**
 * Main class for using TreeMDown
 *
 * @author hollodotme
 */

namespace hollodotme\TreeMDown;

use hollodotme\TreeMDown\FileSystem\Search;
use hollodotme\TreeMDown\Misc\DefaultOptions;
use hollodotme\TreeMDown\Misc\Opt;
use hollodotme\TreeMDown\Misc\Options;
use hollodotme\TreeMDown\Rendering\HTMLPage;
use hollodotme\TreeMDown\Rendering\HTMLTree;
use hollodotme\TreeMDown\Utilities\FileEncoder;

/**
 * Class TreeMDown
 *
 * @package hollodotme\TreeMDown
 */
class TreeMDown
{

	/**
	 * @var null|Options
	 */
	protected $_options = null;

	/**
	 * The Search instance
	 *
	 * @var Search|null
	 */
	protected $_search = null;

	/**
	 * The HTMLTree instance
	 *
	 * @var HTMLTree|null
	 */
	protected $_tree = null;

	/**
	 * The Page instance
	 *
	 * @var null|HTMLPage
	 */
	protected $_page = null;

	/**
	 * Constructor
	 *
	 * @param string $root_dir Root directory
	 */
	public function __construct( $root_dir = '.' )
	{
		$this->_options = new DefaultOptions();
		$this->_options->set( Opt::DIR_ROOT, realpath( strval( $root_dir ) ) );
	}

	/**
	 * Return the options
	 *
	 * @return Options
	 */
	public function getOptions()
	{
		return $this->_options;
	}

	/**
	 * Set the options
	 *
	 * @param Options $options
	 */
	public function setOptions( Options $options )
	{
		$this->_options = $options;
	}

	/**
	 * Set the project name
	 *
	 * @param string $project_name Project name
	 */
	public function setProjectName( $project_name )
	{
		$this->_options->set( Opt::NAME_PROJECT, strval( $project_name ) );
	}

	/**
	 * Return the project name
	 *
	 * @return string
	 */
	public function getProjectName()
	{
		return $this->_options->get( Opt::NAME_PROJECT );
	}

	/**
	 * Set the short description
	 *
	 * @param string $short_description Short description
	 */
	public function setShortDescription( $short_description )
	{
		$this->_options->set( Opt::PROJECT_ABSTRACT, strval( $short_description ) );
	}

	/**
	 * Return the short description
	 *
	 * @return string
	 */
	public function getShortDescription()
	{
		return $this->_options->get( Opt::PROJECT_ABSTRACT );
	}

	/**
	 * Set the company_name name
	 *
	 * @param string $company_name Company name
	 */
	public function setCompanyName( $company_name )
	{
		$this->_options->set( Opt::NAME_COMPANY, strval( $company_name ) );
	}

	/**
	 * Return the company name
	 *
	 * @return string
	 */
	public function getCompanyName()
	{
		return $this->_options->get( Opt::NAME_COMPANY );
	}

	/**
	 * Return the default file (relative to root directory)
	 *
	 * @return string
	 */
	public function getDefaultFile()
	{
		return $this->_options->get( Opt::FILE_DEFAULT );
	}

	/**
	 * Set the default file (relative to root directory)
	 *
	 * @param string $default_file Default file
	 */
	public function setDefaultFile( $default_file )
	{
		$this->_options->set( Opt::FILE_DEFAULT, ltrim( trim( $default_file ), "\t\n\r\0\x0B\/" ) );
	}

	/**
	 * Hide empty folders
	 */
	public function hideEmptyFolders()
	{
		$this->_options->set( Opt::EMPTY_FOLDERS_HIDDEN, true );
	}

	/**
	 * Show empty folders
	 */
	public function showEmptyFolders()
	{
		$this->_options->set( Opt::EMPTY_FOLDERS_HIDDEN, false );
	}

	/**
	 * Enable prettified names of folders and files
	 */
	public function enablePrettyNames()
	{
		$this->_options->set( Opt::NAMES_PRETTYFIED, true );
	}

	/**
	 * Disable prettified names of folders and files
	 */
	public function disablePrettyNames()
	{
		$this->_options->set( Opt::NAMES_PRETTYFIED, false );
	}

	/**
	 * Hide filename suffix
	 */
	public function hideFilenameSuffix()
	{
		$this->_options->set( Opt::FILENAME_SUFFIX_HIDDEN, true );
	}

	/**
	 * Show filename suffix
	 */
	public function showFilenameSuffix()
	{
		$this->_options->set( Opt::FILENAME_SUFFIX_HIDDEN, false );
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
		$this->_options->set( Opt::PATH_INCLUDE_PATTERNS, $patterns );
	}

	/**
	 * Return the include file patterns
	 *
	 * @return array
	 */
	public function getIncludePatterns()
	{
		return $this->_options->get( Opt::PATH_INCLUDE_PATTERNS );
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
		$this->_options->set( Opt::PATH_EXCLUDE_PATTERNS, $patterns );
	}

	/**
	 * Return the file/path exclude patterns
	 *
	 * @return array
	 */
	public function getExcludePatterns()
	{
		return $this->_options->get( Opt::PATH_EXCLUDE_PATTERNS );
	}

	/**
	 * Enable Github ribbon
	 */
	public function enableGithubRibbon()
	{
		$this->_options->set( Opt::GITHUB_RIBBON_ENABLED, true );
	}

	/**
	 * Disable Github ribbon
	 */
	public function disableGithubRibbon()
	{
		$this->_options->set( Opt::GITHUB_RIBBON_ENABLED, false );
	}

	/**
	 * Prepare the options
	 */
	protected function _prepareOptions()
	{
		// Current file
		if ( isset($_GET['tmd_f']) && !empty($_GET['tmd_f']) )
		{
			$current_file = trim( $_GET['tmd_f'], "\t\r\n\0\x0B/" );
		}
		else
		{
			$current_file = $this->_options->get( Opt::FILE_DEFAULT );
		}

		$this->_options->set(
			Opt::FILE_CURRENT,
			realpath( $this->_options->get( Opt::DIR_ROOT ) . DIRECTORY_SEPARATOR . $current_file )
		);

		// Output type
		$output_type = Opt::OUTPUT_TYPE_DOM;
		if ( isset($_GET['tmd_r']) && !empty($_GET['tmd_r']) )
		{
			$output_type = Opt::OUTPUT_TYPE_RAW;
		}

		$this->_options->set( Opt::OUTPUT_TYPE, $output_type );

		// Search term
		$this->_options->set( Opt::SEARCH_TERM, isset($_GET['tmd_q']) ? strval( $_GET['tmd_q'] ) : '' );

		// Base params
		$base_params = array(
			'tmd_f' => $current_file,
			'tmd_q' => $this->_options->get( Opt::SEARCH_TERM ),
		);

		$this->_options->set( Opt::BASE_PARAMS, $base_params );
	}

	/**
	 * Prepare the search
	 */
	protected function _prepareSearch()
	{
		// Init search
		$this->_search = new Search( $this->_options );
	}

	/**
	 * Prepare the tree
	 */
	protected function _prepareTree()
	{
		// Init tree
		$this->_tree = new HTMLTree( $this->_search );
	}

	/**
	 * Prepare the page
	 */
	protected function _preparePage()
	{
		$this->_page = new HTMLPage( $this->_tree );
	}

	/**
	 * Return the page output (HTML / Raw)
	 *
	 * @param array $headers Output headers
	 *
	 * @return string|\DOMDocument
	 */
	public function getOutput( array &$headers = array() )
	{
		$this->_prepareOptions();
		$this->_prepareSearch();
		$this->_prepareTree();

		// Raw output?
		switch ( $this->_options->get( Opt::OUTPUT_TYPE ) )
		{
			case Opt::OUTPUT_TYPE_RAW :
			{
				$headers['Content-type'] = 'text/plain; charset=UTF-8';

				$current_file = $this->_search->getCurrentFile( false );

				if ( $this->_search->isCurrentFileValid() && is_file( $current_file ) )
				{
					$file_encoder = new FileEncoder( $current_file );
					$output       = $file_encoder->getFileContents();
				}
				else
				{
					$output = 'Your current selection is not valid.';
				}

				break;
			}
			case Opt::OUTPUT_TYPE_DOM:
			{
				$headers['Content-type'] = 'text/html; charset=UTF-8';

				$this->_tree->buildTree();

				$this->_preparePage();

				$output = $this->_page->getDOMDocument();

				break;
			}
			default:
				{
				$output = 'No valid output type set.';
				}
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

		if ( $output instanceof \DOMDocument )
		{
			$output->formatOutput = false;
			echo $output->saveHTML();
		}
		else
		{
			echo $output;
		}
	}
}
