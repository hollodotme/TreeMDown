<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\FileSystem;

/**
 * Class Leaf
 * @package hollodotme\TreeMDown\FileSystem
 */
class Leaf extends Entry
{
	/** @var string */
	protected $error = '';

	public function getError() : string
	{
		return $this->error;
	}

	public function setError( string $error ) : void
	{
		$this->error = $error;
	}

	public function getOutput()
	{
		$string = str_repeat( ' ', $this->nestingLevel );

		if ( !empty( $this->error ) )
		{
			$string .= '!!! ' . $this->error . ' !!!';
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

	public function __toString() : string
	{
		return $this->getOutput();
	}
}
