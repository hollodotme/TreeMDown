<?php

namespace hollodotme\TreeMDown\Test\Unit\Linker;

use hollodotme\TreeMDown\FileSystem\Search;
use hollodotme\TreeMDown\Misc\DefaultOptions;
use hollodotme\TreeMDown\Misc\Opt;
use hollodotme\TreeMDown\Utilities\Linker;

class LinkerTest extends \PHPUnit_Framework_TestCase
{

	/** @var DefaultOptions */
	private $test_options;

	/** @var \DOMDocument */
	private $test_dom;

	public function setUp()
	{
		$this->test_options = new DefaultOptions();
		$this->test_options->set( Opt::DIR_ROOT, __DIR__ . '/../Fixures/LinkerTest' );

		$this->test_dom = new \DOMDocument( '1.0', 'UTF-8' );
		$this->test_dom->loadHTMLFile( __DIR__ . '/../Fixures/InternalLink_1.html' );
	}

	public function testGetInternalLinks()
	{
		$linker = new Linker( new Search( $this->test_options ) );

		$internal_links = $linker->getInternalLinks( $this->test_dom->documentElement );

		$this->assertContainsOnlyInstancesOf( '\DOMElement', $internal_links );
		$this->assertCount( 4, $internal_links );
	}

	/**
	 * @dataProvider modifyHrefProvider
	 */
	public function testModifyInternalLink( $href, $expected_href )
	{
		$linker = new Linker( new Search( $this->test_options ) );
		$dom    = new \DOMDocument( '1.0', 'UTF-8' );
		$a      = $dom->createElement( 'a', 'Unit-Test' );
		$a->setAttribute( 'href', $href );

		$linker->modifyInternalLink( $a );

		$this->assertEquals( $expected_href, $a->getAttribute( 'href' ) );
	}

	public function modifyHrefProvider()
	{
		return array(
			array( 'index.md', '?tmd_f=index.md&tmd_q=' ),
			array( 'index.md?tmd_f=blubb.md', '?tmd_f=index.md&tmd_q=' ),
			array( 'index.md?tmd_r=1', '?tmd_f=index.md&tmd_q=' ),
			array( 'index.md?raw', '?tmd_f=index.md&tmd_q=&tmd_r=1' ),
			array( 'index.md?tmd_q=Searchword', '?tmd_f=index.md&tmd_q=Searchword' ),
			array( 'index.md?q=Searchword', '?tmd_f=index.md&tmd_q=Searchword' ),
			array( 'folder/f-a-q.md', '?tmd_f=folder%2Ff-a-q.md&tmd_q=' ),
		);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetInternalLinksFailsOnNodeWithoutOwnerDocument()
	{
		$linker = new Linker( new Search( $this->test_options ) );
		$node   = new \DOMElement( 'test', 'unit' );

		$linker->getInternalLinks( $node );
	}
}
