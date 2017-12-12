<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

/**
 * Class UserMessage
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
final class UserMessage
{
	/** @var string */
	private $title;

	/** @var string */
	private $message;

	/** @var string */
	private $severity;

	public function __construct( string $title, string $message, string $severity )
	{
		$this->title    = $title;
		$this->message  = $message;
		$this->severity = $severity;
	}

	public function getTitle() : string
	{
		return $this->title;
	}

	public function getMessage() : string
	{
		return $this->message;
	}

	public function getSeverity() : string
	{
		return $this->severity;
	}
}
