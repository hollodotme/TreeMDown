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
	 * @param Search $search   Search
	 * @param string $filepath Filepath
	 */
	public function __construct( Search $search, $filepath )
	{
		parent::__construct( $search, $filepath );
		$this->setLeafObjectClass( __NAMESPACE__ . '\\Leaf' );
	}

	/**
	 * Return whether the tree is valid
	 *
	 * @return bool
	 */
	public function isValid()
	{
		return (parent::isValid() && !empty($this->_leaf_object_class));
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
	 * Return the leaf object class
	 *
	 * @return string
	 */
	public function getLeafObjectClass()
	{
		return $this->_leaf_object_class;
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
	 *
	 * @return null|Tree|Leaf|Entry
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
	 * Return whether this tree contains the $filepath
	 *
	 * @param string $filepath Filepath
	 *
	 * @return bool
	 */
	public function containsPath( $filepath )
	{
		$contains_path = false;

		$this->rewind();

		while ( !$contains_path && $this->valid() )
		{
			if ( $this->current()->getFilePath( false ) == $filepath )
			{
				$contains_path = true;
			}
			elseif ( $this->current() instanceof Tree )
			{
				$contains_path = $this->current()->containsPath( $filepath );
			}

			$this->next();
		}

		return $contains_path;
	}

	/**
	 * Builds the tree recursive
	 */
	public function buildTree()
	{
		if ( !$this->isValid() )
		{
			throw new \RuntimeException( 'The tree is not set up properly.' );
		}
		else
		{
			$tree        = array( 'dirs' => array(), 'files' => array() );
			$current_dir = $this->_filepath;

			$iterator = new \DirectoryIterator( $current_dir );

			foreach ( $iterator as $file_info )
			{
				if ( !$file_info->isDot() && !$this->_search->isPathIgnored( $file_info->getPathname() ) )
				{
					// Subtree?
					if ( $file_info->isDir() )
					{
						// Recursion
						$sub_tree = new static( $this->_search, $file_info->getPathname() );
						$sub_tree->setFlags( $this->_flags );
						$sub_tree->setLeafObjectClass( $this->_leaf_object_class );
						$sub_tree->buildTree();

						if ( !($this->_flags & self::EXCLUDE_EMPTY_FOLDERS) || $sub_tree->count() > 0 )
						{
							$tree['dirs'][ $file_info->getFilename() ] = $sub_tree;
						}
					}
					// Leaf!
					elseif ( $file_info->isFile() )
					{
						$tree['files'][ $file_info->getFilename() ] = $this->getLeafObject(
							$file_info->getPathname()
						);
					}
				}
			}

			// Error on empty directories
			if ( !($this->_flags & self::EXCLUDE_EMPTY_FOLDERS) && empty($tree['files']) )
			{
				$leaf = $this->getLeafObject( $current_dir );
				$leaf->setError( "Directory has no files matching the filter." );
				$tree['files'][''] = $leaf;
			}

			uksort( $tree['dirs'], "strnatcasecmp" );
			uksort( $tree['files'], "strnatcasecmp" );

			$this->_entries = array_merge(
				array_values( $tree['dirs'] ),
				array_values( $tree['files'] )
			);
		}
	}

	/**
	 * Return a string represetation of this instance
	 *
	 * @return string
	 */
	public function getOutput()
	{
		$string = str_repeat( ' ', max( 0, $this->_nesting_level ) );

		if ( $this->isActive() )
		{
			$string .= '**' . $this->_filename . '**';
		}
		else
		{
			$string .= $this->_filename;
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
	 *
	 * @return Leaf
	 */
	public function getLeafObject( $filepath )
	{
		$leaf_object = new $this->_leaf_object_class( $this->_search, $filepath );

		if ( !($leaf_object instanceof Leaf) )
		{
			throw new \RuntimeException(
				sprintf(
					'%s is not a subclass of %s\\Leaf',
					$this->_leaf_object_class .
					__NAMESPACE__
				)
			);
		}

		return $leaf_object;
	}
}
