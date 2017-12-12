<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\FileSystem;

use hollodotme\TreeMDown\Misc\Opt;

/**
 * Class Tree
 * @package hollodotme\TreeMDown\FileSystem
 */
class Tree extends Entry implements \Iterator, \Countable
{
	/** @var Entry[]|Tree[]|Leaf[] */
	protected $entries = [];

	/** @var string */
	protected $leafObjectClass;

	public function __construct( Search $search, string $filepath = '' )
	{
		parent::__construct( $search, $filepath );
		$this->setLeafObjectClass( Leaf::class );
	}

	public function isValid() : bool
	{
		return (parent::isValid() && !empty( $this->leafObjectClass ));
	}

	public function setLeafObjectClass( string $leafObjectClass ) : void
	{
		$this->leafObjectClass = $leafObjectClass;
	}

	public function getLeafObjectClass() : string
	{
		return $this->leafObjectClass;
	}

	public function current()
	{
		return current( $this->entries );
	}

	public function next()
	{
		next( $this->entries );
	}

	public function key() : ?int
	{
		return key( $this->entries );
	}

	public function valid() : bool
	{
		return (current( $this->entries ) instanceof Entry);
	}

	public function rewind() : void
	{
		reset( $this->entries );
	}

	public function count() : int
	{
		return \count( $this->entries );
	}

	/**
	 * @throws \RuntimeException
	 */
	public function buildTree() : void
	{
		if ( !$this->isValid() )
		{
			throw new \RuntimeException( 'The tree is not set up properly.' );
		}

		$iterator = new \DirectoryIterator( $this->filePath );

		foreach ( $iterator as $fileInfo )
		{
			if ( $fileInfo->isDot() || $this->search->isPathIgnored( $fileInfo->getPathname() ) )
			{
				continue;
			}

			// Subtree?
			if ( $fileInfo->isDir() )
			{
				// Recursion
				$subTree = new static( $this->search, $fileInfo->getPathname() );
				$subTree->setLeafObjectClass( $this->leafObjectClass );
				$subTree->buildTree();

				if ( $subTree->isValid() )
				{
					if ( $subTree->count() > 0 || !$this->getOptions()->get( Opt::EMPTY_FOLDERS_HIDDEN ) )
					{
						$this->entries[] = $subTree;
					}
				}

				continue;
			}

			// Leaf!
			if ( $fileInfo->isFile() )
			{
				$leaf = $this->getLeafObject( $fileInfo->getPathname() );
				if ( $leaf->isValid() )
				{
					$this->entries[] = $leaf;
				}
			}
		}

		if ( !$this->hasFiles() && !$this->getOptions()->get( Opt::EMPTY_FOLDERS_HIDDEN ) )
		{
			$leaf = $this->getLeafObject( $this->filePath );
			$leaf->setError( 'Directory has no files matching the filter.' );
			$this->entries[] = $leaf;
		}

		usort(
			$this->entries,
			function ( Entry $a, Entry $b )
			{
				if ( ($a instanceof Tree) && ($b instanceof Leaf) )
				{
					return -1;
				}

				if ( ($a instanceof Leaf) && ($b instanceof Tree) )
				{
					return 1;
				}

				return strnatcasecmp( $a->getBasename(), $b->getBasename() );
			}
		);
	}

	public function hasFiles() : bool
	{
		$hasFiles = false;

		$this->rewind();

		while ( !$hasFiles && $this->valid() )
		{
			$hasFiles = ($this->current() instanceof Leaf);
			$this->next();
		}

		return $hasFiles;
	}

	public function getOutput()
	{
		$string = str_repeat( ' ', max( 0, $this->nestingLevel ) );

		if ( $this->isActive() )
		{
			$string .= '**' . $this->getDisplayFilename() . '**';
		}
		else
		{
			$string .= $this->basename;
		}

		foreach ( $this->entries as $entry )
		{
			$string .= PHP_EOL . $entry->getOutput();
		}

		return $string;
	}

	public function __toString() : string
	{
		return $this->getOutput();
	}

	/**
	 * Return a new leaf object
	 *
	 * @param string $filepath
	 *
	 * @throws \RuntimeException
	 * @return Leaf
	 */
	public function getLeafObject( string $filepath ) : Leaf
	{
		$leafObject = new $this->leafObjectClass( $this->search, $filepath );

		if ( $leafObject instanceof Leaf )
		{
			return $leafObject;
		}

		throw new \RuntimeException( sprintf( '%s is not a subclass of %s\\Leaf', $this->leafObjectClass, __NAMESPACE__ ) );
	}
}
