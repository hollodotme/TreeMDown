<?php
/**
 * Class for builing a recursive tree of a filesystem
 *
 * @author h.woltersdorf
 */

namespace hollodotme\TreeMDown\FileSystem;

/**
 * Class Tree
 *
 * @package hollodotme\TreeMDown\FileSystem
 */
class Tree extends Entry implements \Iterator, \Countable
{
	/**
	 * Flag: Hide empty folders
	 */
	const EXCLUDE_EMPTY_FOLDERS = 1;

	/**
	 * Ignore pattern
	 *
	 * @var string
	 */
	protected $_ignore = "#^\.#";

	/**
	 * Filename filter pattern
	 *
	 * @var string
	 */
	protected $_file_filter = "#\.md$#";

	/**
	 * Root directory
	 *
	 * @var string
	 */
	protected $_root_dir = '';

	/**
	 * Flags
	 *
	 * @var int
	 */
	protected $_flags = 0;

	/**
	 * All tree entries on this nesting level
	 *
	 * @var Entry[]|Tree[]|Leaf[]
	 */
	protected $_entries = array();

	/**
	 * Leaf object class
	 *
	 * @var string
	 */
	protected $_leaf_object_class;

	/**
	 * Constructor
	 *
	 * @param string $filepath
	 * @param int    $nesting_level
	 */
	public function __construct( $filepath, $nesting_level = 0 )
	{
		parent::__construct( $filepath, $nesting_level );
		$this->_leaf_object_class = __NAMESPACE__ . '\\Leaf';
	}

	/**
	 * Set a leaf object class
	 *
	 * @param string $leaf_object_class full qualified class name
	 */
	public function setLeafObjectClass( $leaf_object_class )
	{
		$this->_leaf_object_class = $leaf_object_class;
	}

	/**
	 * Set the ignore pattern
	 *
	 * @param string $ignore RegEx pattern
	 */
	public function setIgnore( $ignore )
	{
		$this->_ignore = $ignore;
	}

	/**
	 * Set the file filter pattern
	 *
	 * @param string $file_filter RegEx pattern
	 */
	public function setFileFilter( $file_filter )
	{
		$this->_file_filter = $file_filter;
	}

	/**
	 * Set the flags
	 *
	 * @param int $flags Flags
	 */
	public function setFlags( $flags )
	{
		$this->_flags = $flags;
	}

	/**
	 * Return the Flags
	 *
	 * @return int
	 */
	public function getFlags()
	{
		return $this->_flags;
	}

	/**
	 * Return the current element
	 */
	public function current()
	{
		return current( $this->_entries );
	}

	/**
	 * Move forward to next element
	 */
	public function next()
	{
		next( $this->_entries );
	}

	/**
	 * Return the key of the current element
	 *
	 * @return int|null
	 */
	public function key()
	{
		return key( $this->_entries );
	}

	/**
	 * Checks if current position is valid
	 *
	 * @return bool
	 */
	public function valid()
	{
		return (current( $this->_entries ) instanceof Entry);
	}

	/**
	 * Rewind the Iterator to the first element
	 */
	public function rewind()
	{
		reset( $this->_entries );
	}

	/**
	 * Count elements of an object
	 *
	 * @return int
	 */
	public function count()
	{
		return count( $this->_entries );
	}

	/**
	 * Builds the tree recursive
	 *
	 * @return Tree
	 */
	public function buildTree()
	{
		$tree         = array( 'dirs' => array(), 'files' => array() );
		$dir          = $this->filepath;
		$current_file = $this->_current_file ?: null;

		if ( empty($dir) )
		{
			$dir = null;

			$leaf             = $this->_getLeafObject( '', $this->_nesting_level );
			$leaf->error      = 'Directory does not exist.';
			$this->_entries[] = $leaf;
		}
		else
		{
			if ( !is_null( $dir ) )
			{
				$items = scandir( $dir );

				foreach ( $items as $item )
				{
					if ( !preg_match( $this->_ignore, $item ) )
					{
						$path = $dir . DIRECTORY_SEPARATOR . $item;

						if ( is_dir( $path ) )
						{
							// Recursion
							$sub_tree = new static( $path, $this->_nesting_level + 1 );
							$sub_tree->setRootDir( $this->_root_dir );
							$sub_tree->setCurrentFile( $this->getCurrentFile( true ) );
							$sub_tree->setIgnore( $this->_ignore );
							$sub_tree->setFileFilter( $this->_file_filter );
							$sub_tree->setFlags( $this->_flags );
							$sub_tree->setSearchFilter( $this->_search_filter );
							$sub_tree->buildTree();

							if ( !($this->_flags & self::EXCLUDE_EMPTY_FOLDERS) || $sub_tree->count() > 0 )
							{
								$tree['dirs'][ $item ] = $sub_tree;
							}
						}
						elseif ( preg_match( $this->_file_filter, $item ) )
						{
							$leaf        = $this->_getLeafObject( $path, $this->_nesting_level );
							$leaf->error = '';

							$tree['files'][ $item ] = $leaf;
						}
					}
				}

				if ( !($this->_flags & self::EXCLUDE_EMPTY_FOLDERS) && empty($tree['files']) )
				{
					$leaf                  = $this->_getLeafObject( $dir, $this->_nesting_level );
					$leaf->error           = "Directory has no files matching the filter.";
					$tree['files']['none'] = $leaf;
				}
			}

			uksort( $tree['dirs'], "strnatcasecmp" );
			uksort( $tree['files'], "strnatcasecmp" );

			$this->_entries = array_merge(
				array_values( $tree['dirs'] ),
				array_values( $tree['files'] )
			);
		}

		return $this;
	}

	/**
	 * Return a string represetation of this instance
	 *
	 * @return string
	 */
	public function getOutput()
	{
		$string = str_repeat( ' ', max( 0, $this->_nesting_level ) );

		if ( $this->active )
		{
			$string .= '**' . $this->filename . '**';
		}
		else
		{
			$string .= $this->filename;
		}

		foreach ( $this->_entries as $entry )
		{
			$string .= PHP_EOL . $entry->getOutput();
		}

		return $string;
	}

	/**
	 * Return a string represetation of this instance for implicit casting
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->getOutput();
	}

	/**
	 * Return a new leaf object
	 *
	 * @param string $filepath
	 * @param int    $nesting_level
	 *
	 * @return Leaf
	 */
	protected function _getLeafObject( $filepath, $nesting_level )
	{
		$leaf_object = new $this->_leaf_object_class( $filepath, $nesting_level + 1 );

		if ( !($leaf_object instanceof Leaf) )
		{
			throw new \RuntimeException(
				sprintf(
					'%s is not a subclass of hollodotme\\TreeMDown\\FileSystem\\Leaf',
					$this->_leaf_object_class
				)
			);
		}
		else
		{
			$leaf_object->setRootDir( $this->_root_dir );
			$leaf_object->setCurrentFile( $this->getCurrentFile( true ) );
			$leaf_object->setSearchFilter( $this->_search_filter );
		}

		return $leaf_object;
	}
}
