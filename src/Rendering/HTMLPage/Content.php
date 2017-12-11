<?php
/**
 * Content section
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

/**
 * Class Content
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class Content extends AbstractSection
{

	/**
	 * The parsed markdown
	 * @var null|\DOMElement
	 */
	protected $_parsed_markdown = null;

	/**
	 * User messages
	 * @var array
	 */
	protected $_user_messages = array();

	/**
	 * @return null|\DOMElement
	 */
	public function getParsedMarkdown()
	{
		return $this->_parsed_markdown;
	}

	/**
	 * @param \DOMElement $parsed_markdown
	 */
	public function setParsedMarkdown( \DOMElement $parsed_markdown )
	{
		$this->_parsed_markdown = $parsed_markdown;
	}

	/**
	 * Return the user messages
	 * @return array
	 */
	public function getUserMessages()
	{
		return $this->_user_messages;
	}

	/**
	 * Set user messages
	 *
	 * @param array $user_messages
	 */
	public function setUserMessages( array $user_messages )
	{
		$this->_user_messages = $user_messages;
	}

	/**
	 * Add the section nodes
	 */
	public function addNodes()
	{
		$div = $this->getDom()->createElement( 'div' );
		$div->setAttribute( 'class', 'tmd-main-content markdown-content' );
		$this->getContainer()->appendChild( $div );

		// Add all user messages
		foreach ( $this->_user_messages as $severity => $messages )
		{
			foreach ( $messages as $message )
			{
				$this->_addUserMessage( $div, $severity, $message['title'], $message['message'] );
			}
		}

		// Import parsed content?
		if ( !is_null( $this->_parsed_markdown ) )
		{
			$div->appendChild( $this->getDom()->importNode( $this->_parsed_markdown, true ) );
		}
	}

	/**
	 * Adds a user message to the content section
	 *
	 * @param \DOMElement $elem
	 * @param string      $severity
	 * @param string      $header
	 * @param string      $message
	 */
	protected function _addUserMessage( \DOMElement $elem, $severity, $header, $message )
	{
		$panel = $this->getDom()->createElement( 'div' );
		$panel->setAttribute( 'class', 'panel panel-' . $severity );
		$elem->appendChild( $panel );

		$heading = $this->getDom()->createElement( 'div' );
		$heading->setAttribute( 'class', 'panel-heading' );
		$panel->appendChild( $heading );

		$title = $this->getDom()->createElement( 'h3', $header );
		$title->setAttribute( 'class', 'panel-title' );
		$heading->appendChild( $title );

		$content = $this->getDom()->createElement( 'div', $message );
		$content->setAttribute( 'class', 'panel-body' );
		$panel->appendChild( $content );
	}
}
