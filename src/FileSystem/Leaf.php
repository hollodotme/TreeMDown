<?php declare(strict_types=1);
/**
 * Class for a tree leaf
 *
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\FileSystem;

/**
 * Class Leaf
 *
 * @package hollodotme\TreeMDown\FileSystem
 */
class Leaf extends Entry
{

	/**
	 * Error message
	 *
	 * @var string
	 */
	protected $_error = '';

	/**
	 * Return the error
	 *
	 * @return string
	 */
	public function getError()
	{
		return $this->_error;
	}

	/**
	 * Set an error
	 *
	 * @param string $error
	 */
	public function setError( $error )
	{
		$this->_error = $error;
	}

	/**
	 * Return a string representation of this instance
	 *
	 * @return string
	 */
	public function getOutput()
	{
		$string = str_repeat( ' ', $this->_nesting_level );

		if ( !empty($this->_error) )
		{
			$string .= '!!! ' . $this->_error . ' !!!';
		}
		elseif ( $this->isActive() )
		{
			$string .= '**' . $this->getDisplayFilename() . '**';
		}
		else
		{
			$string .= $this->getDisplayFilename();
		}

		return $string;
	}

	/**
	 * Return a string representation of this instance for implicit casting
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->getOutput();
	}
}
