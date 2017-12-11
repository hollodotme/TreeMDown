<?php declare(strict_types=1);
/**
 * File encoder
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Utilities;

/**
 * Class FileEncoder
 * @package hollodotme\TreeMDown\Utilities
 */
class FileEncoder
{
	public const ENCODING_DEFAULT = 'utf-8';

	/** @var string */
	protected $filePath = '';

	/** @var \finfo */
	protected $fileInfo;

	public function __construct( string $filePath )
	{
		$this->filePath = $filePath;
		$this->fileInfo = new \finfo( FILEINFO_MIME_ENCODING );
	}

	public function getFileEncoding() : string
	{
		return $this->fileInfo->file( $this->filePath );
	}

	public function getFileContents( string $targetEncoding = self::ENCODING_DEFAULT ) : string
	{
		$fileContents = file_get_contents( $this->filePath );
		$fileEncoding = $this->getFileEncoding();

		if ( $fileEncoding !== $targetEncoding )
		{
			$fileContents = iconv( $fileEncoding, $targetEncoding, $fileContents );
		}

		return $fileContents;
	}
}
