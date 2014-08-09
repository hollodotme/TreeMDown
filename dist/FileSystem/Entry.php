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
	 * Constructor
	 *
	 * @param string $filepath      filepath
	 * @param int    $nesting_level nesting level
	 */
	public function __construct( $filepath, $nesting_level = 0 )
	{
		$this->filepath       = realpath( $filepath ) ?: null;
		$this->filename       = basename( $this->filepath );
		$this->_nesting_level = intval( $nesting_level );
	}
}
