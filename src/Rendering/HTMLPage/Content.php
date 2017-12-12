<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

/**
 * Class Content
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class Content extends AbstractSection
{
	/** @var \DOMElement */
	protected $parsedMarkdown;

	/** @var array */
	protected $userMessages = [];

	public function getParsedMarkdown() : \DOMElement
	{
		return $this->parsedMarkdown;
	}

	public function setParsedMarkdown( \DOMElement $parsedMarkdown ) : void
	{
		$this->parsedMarkdown = $parsedMarkdown;
	}

	public function getUserMessages() : array
	{
		return $this->userMessages;
	}

	public function setUserMessages( array $userMessages ) : void
	{
		$this->userMessages = $userMessages;
	}

	public function addNodes() : void
	{
		$div = $this->getDom()->createElement( 'div' );
		$div->setAttribute( 'class', 'tmd-main-content markdown-content' );
		$this->getDomContainer()->appendChild( $div );

		// Add all user messages
		foreach ( $this->userMessages as $severity => $messages )
		{
			foreach ( (array)$messages as $message )
			{
				$this->addUserMessage( $div, new UserMessage( $message['title'], $message['message'], $severity ) );
			}
		}

		if ( null !== $this->parsedMarkdown )
		{
			$div->appendChild( $this->getDom()->importNode( $this->parsedMarkdown, true ) );
		}
	}

	protected function addUserMessage( \DOMElement $elem, UserMessage $userMessage ) : void
	{
		$panel = $this->getDom()->createElement( 'div' );
		$panel->setAttribute( 'class', 'panel panel-' . $userMessage->getSeverity() );
		$elem->appendChild( $panel );

		$heading = $this->getDom()->createElement( 'div' );
		$heading->setAttribute( 'class', 'panel-heading' );
		$panel->appendChild( $heading );

		$title = $this->getDom()->createElement( 'h3', $userMessage->getTitle() );
		$title->setAttribute( 'class', 'panel-title' );
		$heading->appendChild( $title );

		$content = $this->getDom()->createElement( 'div', $userMessage->getMessage() );
		$content->setAttribute( 'class', 'panel-body' );
		$panel->appendChild( $content );
	}
}
