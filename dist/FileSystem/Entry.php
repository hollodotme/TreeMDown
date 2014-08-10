<?php
/**
 * Base class for filesystem entries
 *
 * @author h.woltersdorf
 */

namespace hollodotme\TreeMDown\FileSystem;

/**
 * Class Entry
 *
 * @package hollodotme\TreeMDown\FileSystem
 */
class Entry
{

	/**
	 * Full path to file/folder
	 *
	 * @var null|string
	 */
	protected $_filepath = '';

	/**
	 * Foldername/Filename
	 *
	 * @var string
	 */
	protected $_filename = '';

	/**
	 * Nesting level
	 *
	 * @var int
	 */
	protected $_nesting_level = 0;

	/**
	 * Search
	 *
	 * @var Search
	 */
	protected $_search;

	/**
	 * Constructor
	 *
	 * @param Search      $search   Search
	 * @param null|string $filepath Filepath
	 */
	public function __construct( Search $search, $filepath = '' )
	{
		$this->_search = $search;

		if ( empty($filepath) )
		{
			$this->_filepath      = $this->_search->getRootDir();
			$this->_nesting_level = 0;
		}
		else
		{
			$filepath        = rtrim( $filepath, "\t\r\n\0\x0B/" );
			$this->_filepath = realpath( $filepath );

			$this->_nesting_level = 1;
			$this->_nesting_level += substr_count( $this->getFilePath( true ), DIRECTORY_SEPARATOR );
		}

		$this->_filename = basename( $this->_filepath );
	}

	/**
	 * Rwturn whether the entry is valid
	 *
	 * @return bool
	 */
	public function isValid()
	{
		$is_valid = false;

		if ( $this->_search->isValid() )
		{
			if ( !empty($this->_filepath) )
			{
				if ( file_exists( $this->_filepath ) )
				{
					$is_valid = true;
				}
			}
		}

		return $is_valid;
	}

	/**
	 * Return whether this entry is ignored
	 *
	 * @return bool
	 */
	public function isIgnored()
	{
		$is_ignored = false;

		if ( is_file( $this->_filepath ) )
		{
			$patterns = array();
			foreach ( $this->_search->getIncludePatterns() as $include )
			{
				$patterns[] = str_replace( '\*', '.*', preg_quote( $include, '#' ) );
			}

			$pattern = sprintf( "#^(%s)$#i", join( '|', $patterns ) );
			if ( !preg_match( $pattern, $this->_filename ) )
			{
				$is_ignored = true;
			}
		}

		$patterns = array();
		foreach ( $this->_search->getExcludePatterns() as $exclude )
		{
			$patterns[] = str_replace( '\*', '.*', preg_quote( $exclude, '#' ) );
		}

		$pattern = sprintf( "#^(%s)$#i", join( '|', $patterns ) );
		if ( preg_match( $pattern, $this->_filename ) )
		{
			$is_ignored = true;
		}

		return $is_ignored;
	}

	/**
	 * Return the search
	 *
	 * @return Search
	 */
	public function getSearch()
	{
		return $this->_search;
	}

	/**
	 * Return the filename
	 *
	 * @return string
	 */
	public function getFilename()
	{
		return $this->_filename;
	}

	/**
	 * Return whether this entry is active
	 *
	 * @return boolean
	 */
	public function isActive()
	{
		$is_active = false;

		$filepath = preg_quote( $this->_filepath, '#' );
		if ( preg_match( "#^{$filepath}(" . DIRECTORY_SEPARATOR . "|$)#", $this->_search->getCurrentFile( false ) ) )
		{
			$is_active = true;
		}

		return $is_active;
	}

	/**
	 * Return the search term occurences
	 *
	 * @return int
	 */
	public function getOccurencesInSearch()
	{
		return $this->_search->getOccurences( $this->_filepath );
	}

	/**
	 * Return the nesting level
	 *
	 * @return int
	 */
	public function getNestingLevel()
	{
		return $this->_nesting_level;
	}

	/**
	 * Return the filepath
	 *
	 * @param bool $strip_root_dir
	 *
	 * @return string
	 */
	public function getFilePath( $strip_root_dir = false )
	{
		$root_dir = $this->_search->getRootDir();
		if ( !empty($root_dir) && !empty($strip_root_dir) )
		{
			$root_dir = preg_quote( $root_dir, "#" );
			$filepath = preg_replace( "#^{$root_dir}(" . DIRECTORY_SEPARATOR . "|$)#", '', $this->_filepath );
		}
		else
		{
			$filepath = $this->_filepath;
		}

		return $filepath ?: '';
	}
}
