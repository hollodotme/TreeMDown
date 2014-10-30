<?php
/**
 * Class for grep search in file system
 *
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\FileSystem;

use hollodotme\TreeMDown\Misc\Opt;
use hollodotme\TreeMDown\Misc\Options;

/**
 * Class Search
 *
 * @package hollodotme\TreeMDown\FileSystem
 */
class Search
{

	/**
	 * The Options
	 *
	 * @var null|Options
	 */
	protected $_options = null;

	/**
	 * Paths with occurences of search term
	 *
	 * @var array
	 */
	protected $_paths_with_occurences = array();

	/**
	 * Constructor
	 *
	 * @param Options $options
	 */
	public function __construct( Options $options )
	{
		$this->_options = $options;
	}

	/**
	 * Return the Options
	 *
	 * @return Options
	 */
	public function getOptions()
	{
		return $this->_options;
	}

	/**
	 * Return whether the search is valid
	 *
	 * @return bool
	 */
	public function isValid()
	{
		$root_dir = $this->getRootDir();

		return !empty($root_dir);
	}

	/**
	 * Return whether the search is active
	 *
	 * @return bool
	 */
	public function isActive()
	{
		$search_term = $this->getSearchTerm();

		return !empty($search_term);
	}

	/**
	 * Return the root directory
	 *
	 * @return string
	 */
	public function getRootDir()
	{
		return $this->getOptions()->get( Opt::DIR_ROOT );
	}

	/**
	 * Return the search term
	 *
	 * @return string
	 */
	public function getSearchTerm()
	{
		return $this->getOptions()->get( Opt::SEARCH_TERM );
	}

	/**
	 * Return whether current file is valid
	 *
	 * @return bool
	 */
	public function isCurrentFileValid()
	{
		$is_valid = true;

		$current_file = $this->_options->get( Opt::FILE_CURRENT );
		$root_dir     = $this->_options->get( Opt::DIR_ROOT );

		if ( empty($current_file) )
		{
			$is_valid = false;
		}
		elseif ( !preg_match( "#^{$root_dir}/#", $current_file ) )
		{
			$is_valid = false;
		}
		elseif ( $this->isPathIgnored( $current_file ) )
		{
			$is_valid = false;
		}

		return $is_valid;
	}

	/**
	 * Return the current file
	 *
	 * @param bool $strip_root_dir
	 *
	 * @return string
	 */
	public function getCurrentFile( $strip_root_dir = false )
	{
		$file = '';

		$current_file = $this->_options->get( Opt::FILE_CURRENT );

		if ( $this->isCurrentFileValid() )
		{
			if ( !empty($strip_root_dir) )
			{
				$file = preg_replace( "#^{$this->getRootDir()}(" . DIRECTORY_SEPARATOR . "|$)#", '', $current_file );
			}
			else
			{
				$file = $current_file;
			}
		}

		return $file;
	}

	/**
	 * Return whether the filepath is ignored
	 *
	 * @param string $filepath Filepath
	 *
	 * @return bool
	 */
	public function isPathIgnored( $filepath )
	{
		$is_ignored = false;
		$filename   = basename( $filepath );

		if ( is_file( $filepath ) )
		{
			$include_patterns = array();
			foreach ( $this->getOptions()->get( Opt::PATH_INCLUDE_PATTERNS ) as $include )
			{
				$include_patterns[] = str_replace( '\*', '.*', preg_quote( $include, '#' ) );
			}

			$include_pattern = sprintf( "#^(%s)$#i", join( '|', $include_patterns ) );
			if ( !preg_match( $include_pattern, $filename ) )
			{
				$is_ignored = true;
			}
		}

		$exclude_patterns = array();
		foreach ( $this->getOptions()->get( Opt::PATH_EXCLUDE_PATTERNS ) as $exclude )
		{
			$exclude_patterns[] = str_replace( '\*', '.*', preg_quote( $exclude, '#' ) );
		}

		$exclude_pattern = sprintf( "#^(%s)$#i", join( '|', $exclude_patterns ) );
		if ( preg_match( $exclude_pattern, $filename ) )
		{
			$is_ignored = true;
		}

		return $is_ignored;
	}

	/**
	 * Return assoc. array with paths as keys and occurences as value
	 *
	 * @return array
	 */
	public function getPathsWithOccurences()
	{
		$key = md5( $this->getRootDir() . '::' . $this->getSearchTerm() );

		if ( !array_key_exists( $key, $this->_paths_with_occurences ) )
		{
			$this->_paths_with_occurences[ $key ] = array();
			$search_term                          = $this->getSearchTerm();

			if ( file_exists( $this->getRootDir() ) && !empty($search_term) )
			{
				$search_term = escapeshellarg( addcslashes( $search_term, '-' ) );
				$root_dir    = escapeshellarg( $this->getRootDir() );

				$excludes = array();
				foreach ( $this->getOptions()->get( Opt::PATH_EXCLUDE_PATTERNS ) as $exclude )
				{
					$excludes[] = '--exclude=' . escapeshellarg( $exclude );
				}

				$includes = array();
				foreach ( $this->getOptions()->get( Opt::PATH_INCLUDE_PATTERNS ) as $include )
				{
					$includes[] = '--include=' . escapeshellarg( $include );
				}

				$command = sprintf(
					'grep -ric %s %s %s %s',
					join( ' ', $includes ),
					join( ' ', $excludes ),
					$search_term,
					$root_dir
				);

				$results = shell_exec( $command );

				if ( !empty($results) )
				{
					$lines = explode( "\n", $results );
					foreach ( $lines as $line )
					{
						if ( !empty($line) )
						{
							list($filepath, $count) = explode( ':', $line );
							$this->_paths_with_occurences[ $key ][ $filepath ] = intval( $count );
						}
					}
				}
			}
		}

		return $this->_paths_with_occurences[ $key ];
	}

	/**
	 * Return the amount of files where at least one occurence of search term exists
	 *
	 * @return int
	 */
	public function getPathsWithOccurencesCount()
	{
		$paths_with_occurences = array_filter(
			$this->getPathsWithOccurences(),
			function ( $value )
			{
				return ($value > 0);
			}
		);

		return count( $paths_with_occurences );
	}

	/**
	 * Return whether the $filepath has at least one occurance of search term
	 *
	 * @param string $filepath Filepath
	 *
	 * @return bool
	 */
	public function hasOccurence( $filepath )
	{
		return ($this->isActive() && $this->getOccurences( $filepath ) > 0);
	}

	/**
	 * Return the amount of occurences of search term in file at $filepath
	 *
	 * @param string $filepath Filepath
	 *
	 * @return int
	 */
	public function getOccurences( $filepath )
	{
		$paths_with_occurences = $this->getPathsWithOccurences();
		$occurences            = 0;

		$check_path = preg_quote( $filepath, '#' );

		foreach ( $paths_with_occurences as $path => $occs )
		{
			if ( preg_match( "#^{$check_path}(" . DIRECTORY_SEPARATOR . "|$)#", $path ) )
			{
				$occurences += $occs;
			}
		}

		return $occurences;
	}
}
