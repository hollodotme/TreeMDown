<?php
/**
 * File encoder
 * @author hwoltersdorf
 */

namespace hollodotme\TreeMDown\Utilities;

/**
 * Class FileEncoder
 * @package hollodotme\TreeMDown\Utilities
 */
class FileEncoder
{
	/**
	 * Default encoding
	 */
	const ENCODING_DEFAULT = 'utf-8';

	/**
	 * The file path
	 * @var string
	 */
	protected $_file_path = '';

	/**
	 * The finfo instance
	 * @var \finfo|null
	 */
	protected $_file_info = null;

	/**
	 * Constructor
	 *
	 * @param string $file_path The full path to file
	 */
	public function __construct( $file_path )
	{
		$this->_file_path = $file_path;
		$this->_file_info = new \finfo( FILEINFO_MIME_ENCODING );
	}

	/**
	 * Return the encoding of the file
	 * @return string
	 */
	public function getFileEncoding()
	{
		return $this->_file_info->file( $this->_file_path );
	}

	/**
	 * Return the file contents with $target_encoding
	 *
	 * @see FileEncoder::ENCODING_DEFAULT
	 *
	 * @param string $target_encoding
	 *
	 * @return string
	 */
	public function getFileContents( $target_encoding = self::ENCODING_DEFAULT )
	{
		$file_contents = file_get_contents( $this->_file_path );
		$file_encoding = $this->getFileEncoding();

		if ( $file_encoding != $target_encoding )
		{
			$file_contents = iconv( $file_encoding, $target_encoding, $file_contents );
		}

		return $file_contents;
	}
}