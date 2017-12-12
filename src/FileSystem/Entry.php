<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\FileSystem;

use hollodotme\TreeMDown\Misc\Opt;
use hollodotme\TreeMDown\Misc\Options;

/**
 * Class Entry
 * @package hollodotme\TreeMDown\FileSystem
 */
class Entry
{
	/** @var string */
	protected $filePath = '';

	/** @var string */
	protected $basename = '';

	/** @var int */
	protected $nestingLevel = 0;

	/** @var Search */
	protected $search;

	public function __construct( Search $search, string $filepath = '' )
	{
		$this->search = $search;

		if ( '' === $filepath )
		{
			$this->filePath     = $this->search->getRootDir();
			$this->nestingLevel = 0;
		}
		else
		{
			$filepath       = rtrim( $filepath, "\t\r\n\0\x0B/" );
			$this->filePath = (string)realpath( $filepath );

			$this->nestingLevel = 1;
			$this->nestingLevel += substr_count( $this->getFilePath( true ), DIRECTORY_SEPARATOR );
		}

		$this->basename = basename( $this->filePath );
	}

	public function isValid() : bool
	{
		if ( !$this->search->isValid() )
		{
			return false;
		}

		if ( '' === $this->filePath )
		{
			return false;
		}

		if ( $this->search->isPathIgnored( $this->filePath ) && file_exists( $this->filePath ) )
		{
			return false;
		}

		return true;
	}

	public function getSearch() : Search
	{
		return $this->search;
	}

	public function getOptions() : Options
	{
		return $this->search->getOptions();
	}

	public function getBasename() : string
	{
		return $this->basename;
	}

	public function getDisplayFilename() : string
	{
		$displayFilename = $this->basename;

		// Hide suffix?
		if ( (bool)$this->getOptions()->get( Opt::FILENAME_SUFFIX_HIDDEN ) === true )
		{
			$displayFilename = preg_replace( "#^(.+)\.[^\.]+$#", '$1', $displayFilename );
		}

		// Prettify names?
		if ( (bool)$this->getOptions()->get( Opt::NAMES_PRETTYFIED ) === true )
		{
			$displayFilename = preg_replace( '#[\-_]#', ' ', $displayFilename );
		}

		return $displayFilename;
	}

	public function isActive() : bool
	{
		$filepath = preg_quote( $this->filePath, '#' );

		return (bool)preg_match( "#^{$filepath}(" . DIRECTORY_SEPARATOR . '|$)#', $this->search->getCurrentFile() );
	}

	public function getOccurrencesInSearch() : int
	{
		return $this->search->getOccurrences( $this->filePath );
	}

	public function getNestingLevel() : int
	{
		return $this->nestingLevel;
	}

	public function getFilePath( bool $stripRootDir = false ) : string
	{
		$rootDir  = $this->search->getRootDir();
		$filePath = $this->filePath;

		if ( '' !== $rootDir && $stripRootDir )
		{
			$rootDir  = preg_quote( $rootDir, '#' );
			$filePath = preg_replace( "#^{$rootDir}(" . DIRECTORY_SEPARATOR . '|$)#', '', $this->filePath );
		}

		return $filePath ?: '';
	}
}
