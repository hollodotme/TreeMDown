<?php
/**
 * Header section
 *
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

use hollodotme\TreeMDown\Misc\Opt;
use hollodotme\TreeMDown\Rendering\HTMLPage;

/**
 * Class Header
 *
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class Header extends AbstractSection
{
	/**
	 * Add the section nodes
	 */
	public function addNodes()
	{
		$nav = $this->getElementWithAttributes(
			'nav', array(
				'class' => 'navbar navbar-default navbar-fixed-top',
				'role'  => 'navigation',
			)
		);

		$this->getContainer()->appendChild( $nav );

		// Container
		$container = $this->getElementWithAttributes( 'div', array( 'class' => 'container-fluid' ) );
		$nav->appendChild( $container );

		// Header
		$navbar_header = $this->getElementWithAttributes( 'div', array( 'class' => 'navbar-header' ) );
		$container->appendChild( $navbar_header );

		// Toggle button for mobile view
		$toggle_btn = $this->getElementWithAttributes(
			'button', array(
				'type'        => 'button',
				'class'       => 'navbar-toggle',
				'data-toggle' => 'collapse',
				'data-target' => '#nb-collapse-1',
			)
		);
		$navbar_header->appendChild( $toggle_btn );

		$btn_span = $this->getElementWithAttributes( 'span', array( 'class' => 'sr-only' ), 'Toggle navigation' );
		$toggle_btn->appendChild( $btn_span );

		for ( $i = 0; $i < 3; ++$i )
		{
			$btn_span = $this->getElementWithAttributes( 'span', array( 'class' => 'icon-bar' ) );
			$btn_span->appendChild( $this->getDom()->createTextNode( '' ) );
			$toggle_btn->appendChild( $btn_span );
		}

		// Brand
		$brand = $this->getElementWithAttributes(
			'a', array(
				'class' => 'navbar-brand',
				'href'  => '?'
			),
			$this->getOptions()->get( Opt::NAME_PROJECT )
		);
		$navbar_header->appendChild( $brand );

		// Collapse content
		$content = $this->getElementWithAttributes(
			'div', array(
				'class' => 'navbar-collapse collapse',
				'id'    => 'nb-collapse-1',
			)
		);
		$container->appendChild( $content );

		// Nav links
		$ul = $this->getElementWithAttributes( 'ul', array( 'class' => 'nav navbar-nav hidden-xs' ) );
		$content->appendChild( $ul );

		$li = $this->getDom()->createElement( 'li' );
		$ul->appendChild( $li );

		$li->appendChild(
			$this->getElementWithAttributes(
				'a', array( 'href' => '?' ), $this->getOptions()->get( Opt::PROJECT_ABSTRACT )
			)
		);

		// Search form
		$form = $this->getElementWithAttributes(
			'form', array(
				'class'  => 'navbar-form navbar-right',
				'role'   => 'search',
				'method' => 'get',
				'action' => '',
			)
		);
		$content->appendChild( $form );

		foreach ( $this->getOptions()->get( Opt::BASE_PARAMS ) as $name => $value )
		{
			if ( $name != 'tmd_q' )
			{
				$form->appendChild(
					$this->getElementWithAttributes(
						'input', array(
							'type'  => 'hidden',
							'name'  => $name,
							'value' => $value,
						)
					)
				);
			}
		}

		$group = $this->getElementWithAttributes( 'div', array( 'class' => 'form-group' ) );
		$form->appendChild( $group );

		// Raw content button
		if ( $this->getTree()->getSearch()->isCurrentFileValid()
		     && is_file( $this->getTree()->getSearch()->getCurrentFile( false ) )
		)
		{
			// Button to show raw content
			$query_string = http_build_query(
				array_merge(
					$this->getOptions()->get( Opt::BASE_PARAMS ),
					array( 'tmd_r' => 1 )
				)
			);

			$group->appendChild(
				$this->getElementWithAttributes(
					'a', array(
						'href'   => '?' . $query_string,
						'target' => '_blank',
						'class'  => 'btn btn-default hidden-xs',
						'style'  => 'margin-right: 5px',
					),
					'RAW'
				)
			);
		}

		$group->appendChild(
			$this->getElementWithAttributes(
				'label', array(
					'for'   => 'main-search',
					'class' => 'sr-only'
				),
				'Search'
			)
		);

		$input_group = $this->getElementWithAttributes( 'div', array( 'class' => 'input-group' ) );
		$group->appendChild( $input_group );

		// Search active?
		if ( $this->getTree()->getSearch()->isActive() )
		{
			$input_group->appendChild(
				$this->getElementWithAttributes(
					'span', array( 'class' => 'input-group-addon' ),
					sprintf(
						'%d matches in %d files',
						$this->_tree->getOccurencesInSearch(),
						$this->_tree->getSearch()->getPathsWithOccurencesCount()
					)
				)
			);
		}

		// Search field
		$input_group->appendChild(
			$this->getElementWithAttributes(
				'input', array(
					'class'       => 'form-control',
					'type'        => 'text',
					'name'        => 'tmd_q',
					'size'        => '35',
					'id'          => 'main-search',
					'placeholder' => 'search (with grep) ...',
					'value'       => $this->getTree()->getSearch()->getSearchTerm()
				)
			)
		);

		$btn_span = $this->getElementWithAttributes( 'span', array( 'class' => 'input-group-btn' ) );
		$input_group->appendChild( $btn_span );

		// Search active?
		if ( $this->getTree()->getSearch()->isActive() )
		{
			// Reset button
			$reset = $this->getElementWithAttributes(
				'button', array(
					'type'  => 'button',
					'class' => 'btn btn-danger',
					'id'    => 'reset-main-search'
				)
			);

			$glyph = $this->getElementWithAttributes( 'span', array( 'class' => 'glyphicon glyphicon-remove' ) );
			$glyph->appendChild( $this->getDom()->createTextNode( '' ) );

			$reset->appendChild( $glyph );
			$btn_span->appendChild( $reset );
		}

		// Submit button
		$submit = $this->getElementWithAttributes(
			'button', array(
				'class' => 'btn btn-default',
				'type'  => 'submit'
			)
		);
		$btn_span->appendChild( $submit );

		$glyph = $this->getElementWithAttributes( 'span', array( 'class' => 'glyphicon glyphicon-search' ) );
		$glyph->appendChild( $this->getDom()->createTextNode( '' ) );

		$submit->appendChild( $glyph );
	}
}
