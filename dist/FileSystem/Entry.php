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
	public $filepath = '';

	/**
	 * Foldername/Filename
	 *
	 * @var string
	 */
	public $filename = '';

	/**
	 * Currently selected?
	 *
	 * @var bool
	 */
	public $active = false;

	/**
	 * Nesting level
	 *
	 * @var int
	 */
	protected $_nesting_level = 0;

	/**
	 * Root directory
	 *
	 * @var string
	 */
	protected $_root_dir = '';

	/**
	 * Current file
	 *
	 * @var string
	 */
	protected $_current_file = '';

	/**
	 * Search filter pattern (grep)
	 *
	 * @var string
	 */
	protected $_search_filter = '';

	/**
	 * Constructor
	 *
	 * @param string $filepath      filepath
	 * @param int    $nesting_level nesting level
	 */
	public function __construct( $filepath, $nesting_level = 0 )
	{
		$filepath = rtrim( $filepath, "\t\r\n\0\x0B/" );

		$this->filepath       = realpath( $filepath ) ?: null;
		$this->filename       = basename( $this->filepath );
		$this->_nesting_level = intval( $nesting_level );

		if ( $nesting_level == 0 )
		{
			$this->_root_dir = $this->filepath;
		}
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
	 * Return the search filter pattern
	 *
	 * @return string
	 */
	public function getSearchFilter()
	{
		return $this->_search_filter;
	}

	/**
	 * Set a search filter pattern
	 *
	 * @param string $search_filter
	 */
	public function setSearchFilter( $search_filter )
	{
		$this->_search_filter = $search_filter;
	}

	/**
	 * Set the current filepath
	 * Must be relative to the root directory
	 *
	 * @param string $current_file filepath
	 */
	public function setCurrentFile( $current_file )
	{
		$current_file        = trim( $current_file, "\t\r\n\0\x0B/" );
		$this->_current_file = realpath( $this->_root_dir . DIRECTORY_SEPARATOR . $current_file ) ?: null;
		$this->active        = (bool)preg_match( "#^{$this->filepath}#", $this->_current_file );
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
		if ( !empty($this->_current_file) && !empty($strip_root_dir) )
		{
			$file = preg_replace( "#^{$this->_root_dir}/?#", '', $this->_current_file );
		}
		else
		{
			$file = $this->_current_file;
		}

		return $file;
	}

	/**
	 * Set a root directory
	 *
	 * @param string $root_dir
	 */
	public function setRootDir( $root_dir )
	{
		$this->_root_dir = $root_dir;
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
		if ( !empty($this->_root_dir) && !empty($strip_root_dir) )
		{
			$filepath = preg_replace( "#^{$this->_root_dir}/?#", '', $this->filepath );
		}
		else
		{
			$filepath = $this->filepath;
		}

		return $filepath ?: '';
	}
}
