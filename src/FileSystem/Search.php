<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\FileSystem;

use hollodotme\TreeMDown\Misc\Opt;
use hollodotme\TreeMDown\Misc\Options;

/**
 * Class Search
 * @package hollodotme\TreeMDown\FileSystem
 */
final class Search
{
	/** @var Options */
	private $options;

	/** @var array */
	private $pathsWithOccurrences = [];

	public function __construct( Options $options )
	{
		$this->options = $options;
	}

	public function getOptions() : Options
	{
		return $this->options;
	}

	public function isValid() : bool
	{
		$rootDir = $this->getRootDir();

		return '' !== $rootDir;
	}

	public function isActive() : bool
	{
		$searchTerm = $this->getSearchTerm();

		return '' !== $searchTerm;
	}

	public function getRootDir() : string
	{
		return (string)$this->getOptions()->get( Opt::DIR_ROOT );
	}

	public function getSearchTerm() : string
	{
		return (string)$this->getOptions()->get( Opt::SEARCH_TERM );
	}

	public function isCurrentFileValid() : bool
	{
		$currentFile = (string)$this->getOptions()->get( Opt::FILE_CURRENT );
		$rootDir     = $this->getRootDir();

		if ( '' === $currentFile )
		{
			return false;
		}

		if ( !preg_match( "#^{$rootDir}/#", $currentFile ) )
		{
			return false;
		}

		if ( $this->isPathIgnored( $currentFile ) )
		{
			return false;
		}

		return true;
	}

	public function getCurrentFile( bool $stripRootDir = false ) : string
	{
		if ( !$this->isCurrentFileValid() )
		{
			return '';
		}

		$currentFile = (string)$this->getOptions()->get( Opt::FILE_CURRENT );

		if ( true === $stripRootDir )
		{
			return preg_replace( "#^{$this->getRootDir()}(" . DIRECTORY_SEPARATOR . '|$)#', '', $currentFile );
		}

		return $currentFile;
	}

	public function isPathIgnored( string $filePath ) : bool
	{
		$isIgnored = false;
		$filename  = basename( $filePath );

		if ( is_file( $filePath ) )
		{
			$includePatterns     = [];
			$pathIncludePatterns = (array)$this->getOptions()->get( Opt::PATH_INCLUDE_PATTERNS );

			foreach ( $pathIncludePatterns as $include )
			{
				$includePatterns[] = str_replace( '\*', '.*', preg_quote( $include, '#' ) );
			}

			$includePattern = sprintf( '#^(%s)$#i', implode( '|', $includePatterns ) );

			if ( !preg_match( $includePattern, $filename ) )
			{
				$isIgnored = true;
			}
		}

		$excludePatterns     = [];
		$pathExcludePatterns = (array)$this->getOptions()->get( Opt::PATH_EXCLUDE_PATTERNS );

		foreach ( $pathExcludePatterns as $exclude )
		{
			$excludePatterns[] = str_replace( '\*', '.*', preg_quote( $exclude, '#' ) );
		}

		$excludePattern = sprintf( '#^(%s)$#i', implode( '|', $excludePatterns ) );

		if ( preg_match( $excludePattern, $filename ) )
		{
			$isIgnored = true;
		}

		return $isIgnored;
	}

	public function getPathsWithOccurrences() : array
	{
		$searchTerm = $this->getSearchTerm();
		$key        = md5( $this->getRootDir() . '::' . $searchTerm );

		if ( !isset( $this->pathsWithOccurrences[ $key ] ) )
		{
			$this->pathsWithOccurrences[ $key ] = [];

			if ( '' === $searchTerm || !file_exists( $this->getRootDir() ) )
			{
				return $this->pathsWithOccurrences[ $key ];
			}

			$searchTerm = escapeshellarg( addcslashes( $searchTerm, '-' ) );
			$rootDir    = escapeshellarg( $this->getRootDir() );

			$excludes            = [];
			$pathExcludePatterns = (array)$this->getOptions()->get( Opt::PATH_EXCLUDE_PATTERNS );

			foreach ( $pathExcludePatterns as $exclude )
			{
				$excludes[] = '--exclude=' . escapeshellarg( $exclude );
			}

			$includes            = [];
			$pathIncludePatterns = (array)$this->getOptions()->get( Opt::PATH_INCLUDE_PATTERNS );

			foreach ( $pathIncludePatterns as $include )
			{
				$includes[] = '--include=' . escapeshellarg( $include );
			}

			$command = sprintf(
				'grep -ric %s %s %s %s',
				implode( ' ', $includes ),
				implode( ' ', $excludes ),
				$searchTerm,
				$rootDir
			);

			$results = shell_exec( $command );

			if ( '' === $results )
			{
				return $this->pathsWithOccurrences[ $key ];
			}

			$lines = explode( "\n", $results );
			foreach ( $lines as $line )
			{
				if ( '' === $line )
				{
					continue;
				}

				[$filePath, $count] = explode( ':', $line );

				$this->pathsWithOccurrences[ $key ][ $filePath ] = (int)$count;
			}
		}

		return $this->pathsWithOccurrences[ $key ];
	}

	public function getPathsWithOccurrencesCount() : int
	{
		$pathsWithOccurrences = array_filter(
			$this->getPathsWithOccurrences(),
			function ( int $value )
			{
				return ($value > 0);
			}
		);

		return \count( $pathsWithOccurrences );
	}

	public function hasOccurrence( string $filePath ) : bool
	{
		return ($this->isActive() && $this->getOccurrences( $filePath ) > 0);
	}

	public function getOccurrences( string $filePath ) : int
	{
		$pathsWithOccurrences = $this->getPathsWithOccurrences();
		$occurrences          = 0;

		$checkPath = preg_quote( $filePath, '#' );

		foreach ( $pathsWithOccurrences as $path => $occs )
		{
			if ( preg_match( "#^{$checkPath}(" . DIRECTORY_SEPARATOR . '|$)#', $path ) )
			{
				$occurrences += $occs;
			}
		}

		return $occurrences;
	}
}
