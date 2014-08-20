<?php
/**
 * Footer section
 * @author hwoltersdorf
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

use hollodotme\TreeMDown\Rendering\HTMLPage;

/**
 * Class Footer
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class Footer extends AbstractSection
{

	/**
	 * TOC exists
	 * @var bool
	 */
	protected $_toc_exists = false;

	/**
	 * Return whether TOC exists
	 * @return bool
	 */
	public function tocExists()
	{
		return $this->_toc_exists;
	}

	/**
	 * Set whether TOC exists
	 *
	 * @param bool $toc_exists
	 */
	public function setTocExists( $toc_exists )
	{
		$this->_toc_exists = $toc_exists;
	}

	/**
	 * Prepare the section
	 */
	public function prepare()
	{
		// TODO: Implement prepare() method.
	}

	/**
	 * Add the section nodes
	 */
	public function addNodes()
	{
		$footer = $this->getDom()->createElement( 'footer' );
		$this->getContainer()->appendChild( $footer );

		$footer->appendChild( $this->getElementWithAttributes( 'hr', array('class' => 'clearfix') ) );

		$row = $this->getElementWithAttributes( 'div', array('class' => 'tmd-footer row') );
		$footer->appendChild( $row );

		$nav = $this->getElementWithAttributes( 'div', array('class' => 'col-lg-3 col-md-3 col-sm-4 hidden-xs') );
		$nav->appendChild( $this->getDom()->createTextNode( '' ) );
		$row->appendChild( $nav );

		$content = $this->getDom()->createElement( 'div' );
		$row->appendChild( $content );

		$span_company = $this->getElementWithAttributes(
			'span',
			array('class' => 'pull-right small text-muted'),
			sprintf(
				'%s &middot; &copy; %s %s',
				$this->getMetaData( HTMLPage::META_PROJECT_NAME ),
				$this->getMetaData( HTMLPage::META_COMPANY ),
				date( 'Y' )
			)
		);

		if ( $this->tocExists() )
		{
			$content->setAttribute( 'class', 'col-lg-7 col-md-7 col-sm-8 col-xs-12' );

			$toc = $this->getDom()->createElement( 'div' );
			$toc->setAttribute( 'class', 'col-lg-2 col-md-2 hidden-sm hidden-xs' );
			$toc->appendChild( $span_company );
			$row->appendChild( $toc );
		}
		else
		{
			$content->setAttribute( 'class', 'col-lg-9 col-md-9 col-sm-8 col-xs-12' );
			$content->appendChild( $span_company );
		}

		$totop = $this->getElementWithAttributes( 'div', array('class' => 'small text-center') );
		$totop->appendChild( $this->getElementWithAttributes( 'a', array('href' => '#'), 'Back to top' ) );
		$content->appendChild( $totop );
	}
}