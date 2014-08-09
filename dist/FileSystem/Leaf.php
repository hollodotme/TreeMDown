<?php
/**
 * Class for a tree leaf
 *
 * @author h.woltersdorf
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
	public $error = '';

	/**
	 * Return a string representation of this instance
	 *
	 * @return string
	 */
	public function getOutput()
	{
		$string = str_repeat( ' ', $this->_nesting_level );

		if ( !empty($this->error) )
		{
			$string .= '!!! ' . $this->error . ' !!!';
		}
		elseif ( $this->active )
		{
			$string .= '**' . $this->filename . '**';
		}
		else
		{
			$string .= $this->filename;
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
