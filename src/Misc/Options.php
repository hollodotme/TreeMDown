<?php declare(strict_types=1);
/**
 * Options wrapper
 *
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Misc;

/**
 * Class Options
 *
 * @package hollodotme\TreeMDown\Misc
 */
class Options
{

	/**
	 * The options
	 *
	 * @var array
	 */
	protected $_options = [];

	/**
	 * Set an option value
	 *
	 * @param int          $option Option
	 * @param string|array $value  Value
	 */
	public function set( $option, $value )
	{
		$this->_options[ $option ] = $value;
	}

	/**
	 * Get an option value
	 *
	 * @param int $option Option
	 *
	 * @return null|string|array|boolean
	 */
	public function get( $option )
	{
		if ( isset( $this->_options[ $option ] ) )
		{
			$value = $this->_options[ $option ];
		}
		else
		{
			$value = null;
		}

		return $value;
	}
}
