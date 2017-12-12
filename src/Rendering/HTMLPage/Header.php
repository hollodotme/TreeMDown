<?php declare(strict_types=1);
/**
 * Header section
 *
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

use hollodotme\TreeMDown\Misc\Opt;

/**
 * Class Header
 *
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class Header extends AbstractSection
{
	public function addNodes() : void
	{
		$nav = $this->getElementWithAttributes(
			'nav',
			[
				'class' => 'navbar navbar-default navbar-fixed-top',
				'role'  => 'navigation',
			]
		);

		$this->getDomContainer()->appendChild( $nav );

		// Container
		$container = $this->getElementWithAttributes( 'div', ['class' => 'container-fluid'] );
		$nav->appendChild( $container );

		// Header
		$navbar_header = $this->getElementWithAttributes( 'div', ['class' => 'navbar-header'] );
		$container->appendChild( $navbar_header );

		// Toggle button for mobile view
		$toggle_btn = $this->getElementWithAttributes(
			'button',
			[
				'type'        => 'button',
				'class'       => 'navbar-toggle',
				'data-toggle' => 'collapse',
				'data-target' => '#nb-collapse-1',
			]
		);
		$navbar_header->appendChild( $toggle_btn );

		$btn_span = $this->getElementWithAttributes( 'span', ['class' => 'sr-only'], 'Toggle navigation' );
		$toggle_btn->appendChild( $btn_span );

		for ( $i = 0; $i < 3; ++$i )
		{
			$btn_span = $this->getElementWithAttributes( 'span', ['class' => 'icon-bar'] );
			$btn_span->appendChild( $this->getDom()->createTextNode( '' ) );
			$toggle_btn->appendChild( $btn_span );
		}

		// Brand
		$brand = $this->getElementWithAttributes(
			'a',
			[
				'class' => 'navbar-brand',
				'href'  => '?',
			],
			(string)$this->getOptions()->get( Opt::NAME_PROJECT )
		);
		$navbar_header->appendChild( $brand );

		// Collapse content
		$content = $this->getElementWithAttributes(
			'div',
			[
				'class' => 'navbar-collapse collapse',
				'id'    => 'nb-collapse-1',
			]
		);
		$container->appendChild( $content );

		// Nav links
		$ul = $this->getElementWithAttributes( 'ul', ['class' => 'nav navbar-nav hidden-xs'] );
		$content->appendChild( $ul );

		$li = $this->getDom()->createElement( 'li' );
		$ul->appendChild( $li );

		$li->appendChild(
			$this->getElementWithAttributes(
				'a',
				['href' => '?'],
				(string)$this->getOptions()->get( Opt::PROJECT_ABSTRACT )
			)
		);

		// Search form
		$form = $this->getElementWithAttributes(
			'form',
			[
				'class'  => 'navbar-form navbar-right',
				'role'   => 'search',
				'method' => 'get',
				'action' => '',
			]
		);
		$content->appendChild( $form );

		$baseParams = (array)$this->getOptions()->get( Opt::BASE_PARAMS );

		foreach ( $baseParams as $name => $value )
		{
			if ( $name !== 'tmd_q' )
			{
				$form->appendChild(
					$this->getElementWithAttributes(
						'input',
						[
							'type'  => 'hidden',
							'name'  => $name,
							'value' => $value,
						]
					)
				);
			}
		}

		$group = $this->getElementWithAttributes( 'div', ['class' => 'form-group'] );
		$form->appendChild( $group );

		// Raw content button
		if (
			$this->getHtmlTree()->getSearch()->isCurrentFileValid()
			&& is_file( $this->getHtmlTree()->getSearch()->getCurrentFile() )
		)
		{
			// Button to show raw content
			$query_string = http_build_query(
				array_merge(
					$this->getOptions()->get( Opt::BASE_PARAMS ),
					['tmd_r' => 1]
				)
			);

			$group->appendChild(
				$this->getElementWithAttributes(
					'a',
					[
						'href'   => '?' . $query_string,
						'target' => '_blank',
						'class'  => 'btn btn-default hidden-xs',
						'style'  => 'margin-right: 5px',
					],
					'RAW'
				)
			);
		}

		$group->appendChild(
			$this->getElementWithAttributes(
				'label',
				[
					'for'   => 'main-search',
					'class' => 'sr-only',
				],
				'Search'
			)
		);

		$input_group = $this->getElementWithAttributes( 'div', ['class' => 'input-group'] );
		$group->appendChild( $input_group );

		// Search active?
		if ( $this->getHtmlTree()->getSearch()->isActive() )
		{
			$input_group->appendChild(
				$this->getElementWithAttributes(
					'span',
					['class' => 'input-group-addon'],
					sprintf(
						'%d matches in %d files',
						$this->htmlTree->getOccurrencesInSearch(),
						$this->htmlTree->getSearch()->getPathsWithOccurrencesCount()
					)
				)
			);
		}

		// Search field
		$input_group->appendChild(
			$this->getElementWithAttributes(
				'input',
				[
					'class'       => 'form-control',
					'type'        => 'text',
					'name'        => 'tmd_q',
					'size'        => '35',
					'id'          => 'main-search',
					'placeholder' => 'search (with grep) ...',
					'value'       => $this->getHtmlTree()->getSearch()->getSearchTerm(),
				]
			)
		);

		$btn_span = $this->getElementWithAttributes( 'span', ['class' => 'input-group-btn'] );
		$input_group->appendChild( $btn_span );

		// Search active?
		if ( $this->getHtmlTree()->getSearch()->isActive() )
		{
			// Reset button
			$reset = $this->getElementWithAttributes(
				'button',
				[
					'type'  => 'button',
					'class' => 'btn btn-danger',
					'id'    => 'reset-main-search',
				]
			);

			$glyph = $this->getElementWithAttributes( 'span', ['class' => 'glyphicon glyphicon-remove'] );
			$glyph->appendChild( $this->getDom()->createTextNode( '' ) );

			$reset->appendChild( $glyph );
			$btn_span->appendChild( $reset );
		}

		// Submit button
		$submit = $this->getElementWithAttributes(
			'button',
			[
				'class' => 'btn btn-default',
				'type'  => 'submit',
			]
		);
		$btn_span->appendChild( $submit );

		$glyph = $this->getElementWithAttributes( 'span', ['class' => 'glyphicon glyphicon-search'] );
		$glyph->appendChild( $this->getDom()->createTextNode( '' ) );

		$submit->appendChild( $glyph );
	}
}
