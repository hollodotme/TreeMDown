<?php declare(strict_types=1);
/**
 * Main class for using TreeMDown
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
 * @package hollodotme\TreeMDown
 */
class TreeMDown
{

	/** @var Options */
	protected $options;

	/** @var Search */
	protected $search;

	/** @var HTMLTree */
	protected $tree;

	/** @var HTMLPage */
	protected $page;

	public function __construct( string $rootDir = '.' )
	{
		$this->options = new DefaultOptions();
		$this->options->set( Opt::DIR_ROOT, realpath( $rootDir ) );
	}

	public function getOptions() : Options
	{
		return $this->options;
	}

	public function setOptions( Options $options ) : void
	{
		$this->options = $options;
	}

	public function setProjectName( string $projectName ) : void
	{
		$this->options->set( Opt::NAME_PROJECT, $projectName );
	}

	public function getProjectName() : string
	{
		return $this->options->get( Opt::NAME_PROJECT );
	}

	public function setShortDescription( string $shortDescription ) : void
	{
		$this->options->set( Opt::PROJECT_ABSTRACT, $shortDescription );
	}

	public function getShortDescription() : string
	{
		return $this->options->get( Opt::PROJECT_ABSTRACT );
	}

	public function setCompanyName( string $companyName ) : void
	{
		$this->options->set( Opt::NAME_COMPANY, $companyName );
	}

	public function getCompanyName() : string
	{
		return $this->options->get( Opt::NAME_COMPANY );
	}

	public function getDefaultFile() : string
	{
		return $this->options->get( Opt::FILE_DEFAULT );
	}

	public function setDefaultFile( string $defaultFile ) : void
	{
		$this->options->set( Opt::FILE_DEFAULT, ltrim( trim( $defaultFile ), "\t\n\r\0\x0B\/" ) );
	}

	public function hideEmptyFolders() : void
	{
		$this->options->set( Opt::EMPTY_FOLDERS_HIDDEN, true );
	}

	public function showEmptyFolders() : void
	{
		$this->options->set( Opt::EMPTY_FOLDERS_HIDDEN, false );
	}

	public function enablePrettyNames() : void
	{
		$this->options->set( Opt::NAMES_PRETTYFIED, true );
	}

	public function disablePrettyNames() : void
	{
		$this->options->set( Opt::NAMES_PRETTYFIED, false );
	}

	public function hideFilenameSuffix() : void
	{
		$this->options->set( Opt::FILENAME_SUFFIX_HIDDEN, true );
	}

	public function showFilenameSuffix() : void
	{
		$this->options->set( Opt::FILENAME_SUFFIX_HIDDEN, false );
	}

	public function setIncludePatterns( array $patterns ) : void
	{
		$this->options->set( Opt::PATH_INCLUDE_PATTERNS, $patterns );
	}

	public function getIncludePatterns() : array
	{
		return $this->options->get( Opt::PATH_INCLUDE_PATTERNS );
	}

	public function setExcludePatterns( array $patterns ) : void
	{
		$this->options->set( Opt::PATH_EXCLUDE_PATTERNS, $patterns );
	}

	public function getExcludePatterns() : array
	{
		return $this->options->get( Opt::PATH_EXCLUDE_PATTERNS );
	}

	public function enableGithubRibbon() : void
	{
		$this->options->set( Opt::GITHUB_RIBBON_ENABLED, true );
	}

	public function disableGithubRibbon() : void
	{
		$this->options->set( Opt::GITHUB_RIBBON_ENABLED, false );
	}

	protected function prepareOptions() : void
	{
		// Current file
		if ( isset( $_GET['tmd_f'] ) && !empty( $_GET['tmd_f'] ) )
		{
			$current_file = trim( $_GET['tmd_f'], "\t\r\n\0\x0B/" );
		}
		else
		{
			$current_file = $this->options->get( Opt::FILE_DEFAULT );
		}

		$this->options->set(
			Opt::FILE_CURRENT,
			realpath( $this->options->get( Opt::DIR_ROOT ) . DIRECTORY_SEPARATOR . $current_file )
		);

		// Output type
		if ( isset( $_GET['tmd_r'] ) && !empty( $_GET['tmd_r'] ) )
		{
			$output_type = Opt::OUTPUT_TYPE_RAW;
		}
		else
		{
			$output_type = Opt::OUTPUT_TYPE_DOM;
		}

		$this->options->set( Opt::OUTPUT_TYPE, $output_type );

		// Search term
		$this->options->set( Opt::SEARCH_TERM, isset( $_GET['tmd_q'] ) ? strval( $_GET['tmd_q'] ) : '' );

		// Base params
		$base_params = [
			'tmd_f' => $current_file,
			'tmd_q' => $this->options->get( Opt::SEARCH_TERM ),
		];

		$this->options->set( Opt::BASE_PARAMS, $base_params );
	}

	protected function prepareSearch() : void
	{
		// Init search
		$this->search = new Search( $this->options );
	}

	protected function prepareTree() : void
	{
		// Init tree
		$this->tree = new HTMLTree( $this->search );
	}

	protected function preparePage() : void
	{
		$this->page = new HTMLPage( $this->tree );
	}

	/**
	 * @param array $headers Output headers
	 *
	 * @throws \RuntimeException
	 *
	 * @return string|\DOMDocument
	 */
	public function getOutput( array &$headers = [] )
	{
		$this->prepareOptions();
		$this->prepareSearch();
		$this->prepareTree();

		// Raw output?
		switch ( $this->options->get( Opt::OUTPUT_TYPE ) )
		{
			case Opt::OUTPUT_TYPE_RAW :
				{
					$headers['Content-type'] = 'text/plain; charset=UTF-8';

					$current_file = $this->search->getCurrentFile( false );

					if ( $this->search->isCurrentFileValid() && is_file( $current_file ) )
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

					$this->tree->buildTree();

					$this->preparePage();

					$output = $this->page->getDOMDocument();

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
	 * @throws \RuntimeException
	 */
	public function display() : void
	{
		$headers = [];
		$output  = $this->getOutput( $headers );

		foreach ( $headers as $type => $value )
		{
			header( "{$type}: {$value}" );
		}

		if ( $output instanceof \DOMDocument )
		{
			$output->formatOutput = false;
			echo $output->saveHTML();
			flush();
			
			return;
		}

		echo $output;
		flush();
	}
}
