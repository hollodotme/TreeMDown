<?php
/**
 * Class for grep search in file system
 *
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\FileSystem;

/**
 * Class Search
 *
 * @package hollodotme\TreeMDown\FileSystem
 */
class Search
{

	/**
	 * Root directory
	 *
	 * @var string
	 */
	protected $_root_dir = '';

	/**
	 * Search term
	 *
	 * @var string
	 */
	protected $_search_term = '';

	/**
	 * Patterns to include in search
	 *
	 * @var array
	 */
	protected $_include_patterns = array();

	/**
	 * Patterns to exclude from search
	 *
	 * @var array
	 */
	protected $_exclude_patterns = array();

	/**
	 * Current file
	 *
	 * @var string
	 */
	protected $_current_file = '';

	/**
	 * Current file is valid?
	 *
	 * @var bool
	 */
	protected $_current_file_valid = true;

	/**
	 * Paths with occurences of search term
	 *
	 * @var array
	 */
	protected $_paths_with_occurences = array();

	/**
	 * Constructor
	 *
	 * @param string $root_dir    Root directory
	 * @param string $search_term Search term
	 */
	public function __construct( $root_dir, $search_term )
	{
		$this->_root_dir    = realpath( $root_dir );
		$this->_search_term = $search_term;
	}

	/**
	 * Return whether the search is valid
	 *
	 * @return bool
	 */
	public function isValid()
	{
		return !empty($this->_root_dir);
	}

	/**
	 * Return whether the search is active
	 *
	 * @return bool
	 */
	public function isActive()
	{
		return !empty($this->_search_term);
	}

	/**
	 * Return the root directory
	 *
	 * @return string
	 */
	public function getRootDir()
	{
		return $this->_root_dir;
	}

	/**
	 * Return the search term
	 *
	 * @return string
	 */
	public function getSearchTerm()
	{
		return $this->_search_term;
	}

	/**
	 * Return the exclude patterns
	 *
	 * @return array
	 */
	public function getExcludePatterns()
	{
		return $this->_exclude_patterns;
	}

	/**
	 * Set exclude patterns
	 *
	 * @param array $exclude_patterns
	 */
	public function setExcludePatterns( $exclude_patterns )
	{
		$this->_exclude_patterns = $exclude_patterns;
	}

	/**
	 * Return the include patterns
	 *
	 * @return array
	 */
	public function getIncludePatterns()
	{
		return $this->_include_patterns;
	}

	/**
	 * Set include pattern
	 *
	 * @param array $include_patterns
	 */
	public function setIncludePatterns( array $include_patterns )
	{
		$this->_include_patterns = $include_patterns;
	}

	/**
	 * Set the current filepath
	 * Must be relative to the root directory
	 *
	 * @param string $current_file filepath
	 */
	public function setCurrentFile( $current_file )
	{
		$current_file = trim( $current_file, "\t\r\n\0\x0B/" );
		$current_file = realpath( $this->_root_dir . DIRECTORY_SEPARATOR . $current_file );

		if ( !empty($current_file) )
		{
			$root_dir = preg_quote( $this->_root_dir, '#' );
			if ( !preg_match( "#^{$root_dir}(" . DIRECTORY_SEPARATOR . "|$)#", $current_file ) )
			{
				$this->_current_file_valid = false;
				$this->_current_file       = '';
			}
			else
			{
				$this->_current_file_valid = true;
				$this->_current_file       = $current_file;
			}
		}
		else
		{
			$this->_current_file_valid = false;
		}
	}

	/**
	 * Return whether current file is valid
	 *
	 * @return bool
	 */
	public function isCurrentFileValid()
	{
		$is_valid = true;

		if ( empty($this->_current_file) )
		{
			$is_valid = false;
		}
		elseif ( !$this->_current_file_valid )
		{
			$is_valid = false;
		}
		elseif ( $this->isPathIgnored( $this->_current_file ) )
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

		if ( $this->isCurrentFileValid() )
		{
			if ( !empty($strip_root_dir) )
			{
				$file = preg_replace(
					"#^{$this->_root_dir}(" . DIRECTORY_SEPARATOR . "|$)#", '',
					$this->_current_file
				);
			}
			else
			{
				$file = $this->_current_file;
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
			$patterns = array();
			foreach ( $this->_include_patterns as $include )
			{
				$patterns[] = str_replace( '\*', '.*', preg_quote( $include, '#' ) );
			}

			$pattern = sprintf( "#^(%s)$#i", join( '|', $patterns ) );
			if ( !preg_match( $pattern, $filename ) )
			{
				$is_ignored = true;
			}
		}

		$patterns = array();
		foreach ( $this->_exclude_patterns as $exclude )
		{
			$patterns[] = str_replace( '\*', '.*', preg_quote( $exclude, '#' ) );
		}

		$pattern = sprintf( "#^(%s)$#i", join( '|', $patterns ) );
		if ( preg_match( $pattern, $filename ) )
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
		$key = md5( $this->_root_dir . '::' . $this->_search_term );

		if ( !array_key_exists( $key, $this->_paths_with_occurences ) )
		{
			$this->_paths_with_occurences[ $key ] = array();

			if ( file_exists( $this->_root_dir ) && !empty($this->_search_term) )
			{
				$search_term = escapeshellarg( addcslashes( $this->_search_term, '-' ) );
				$root_dir    = escapeshellarg( $this->_root_dir );

				$excludes = array();
				foreach ( $this->_exclude_patterns as $exclude )
				{
					$excludes[] = '--exclude=' . escapeshellarg( $exclude );
				}

				$includes = array();
				foreach ( $this->_include_patterns as $include )
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
