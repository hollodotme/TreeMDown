<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Misc;

/**
 * Class Options
 * @package hollodotme\TreeMDown\Misc
 */
class Options
{
	/** @var array */
	protected $options = [];

	public function set( int $option, $value ) : void
	{
		$this->options[ $option ] = $value;
	}

	public function get( int $option )
	{
		return $this->options[ $option ] ?? null;
	}
}
