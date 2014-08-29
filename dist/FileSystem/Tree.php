<?php
/**
 * Class for builing a recursive tree of a filesystem
 *
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\FileSystem;

use hollodotme\TreeMDown\Misc\Opt;

/**
 * Class Tree
 *
 * @package hollodotme\TreeMDown\FileSystem
 */
class Tree extends Entry implements \Iterator, \Countable
{

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
	public function __construct( Search $search, $filepath = '' )
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
			$iterator = new \DirectoryIterator( $this->_filepath );

			foreach ( $iterator as $file_info )
			{
				if ( !$file_info->isDot() && !$this->_search->isPathIgnored( $file_info->getPathname() ) )
				{
					// Subtree?
					if ( $file_info->isDir() )
					{
						// Recursion
						$sub_tree = new static( $this->_search, $file_info->getPathname() );
						$sub_tree->setLeafObjectClass( $this->_leaf_object_class );
						$sub_tree->buildTree();

						if ( $sub_tree->isValid() )
						{
							if ( !$this->getOptions()->get( Opt::EMPTY_FOLDERS_HIDDEN ) || $sub_tree->count() > 0 )
							{
								$this->_entries[] = $sub_tree;
							}
						}
					}
					// Leaf!
					elseif ( $file_info->isFile() )
					{
						$leaf = $this->getLeafObject( $file_info->getPathname() );
						if ( $leaf->isValid() )
						{
							$this->_entries[] = $leaf;
						}
					}
				}
			}

			// Error on empty directories
			if ( !$this->getOptions()->get( Opt::EMPTY_FOLDERS_HIDDEN ) && !$this->hasFiles() )
			{
				$leaf = $this->getLeafObject( $this->_filepath );
				$leaf->setError( "Directory has no files matching the filter." );
				$this->_entries[] = $leaf;
			}

			// Sort by folder and file and by name
			usort(
				$this->_entries,
				function ( Entry $a, Entry $b )
				{
					if ( ($a instanceof Tree) && ($b instanceof Leaf) )
					{
						return -1;
					}
					elseif ( ($a instanceof Leaf) && ($b instanceof Tree) )
					{
						return 1;
					}
					else
					{
						return strnatcasecmp( $a->getFilename(), $b->getFilename() );
					}
				}
			);
		}
	}

	/**
	 * Return whether this tree has files
	 *
	 * @return bool
	 */
	public function hasFiles()
	{
		$has_files = false;

		$this->rewind();

		while ( !$has_files && $this->valid() )
		{
			$has_files = ($this->current() instanceof Leaf);
			$this->next();
		}

		return $has_files;
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
			$string .= '**' . $this->getDisplayFilename() . '**';
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
