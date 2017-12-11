<?php declare(strict_types=1);
/**
 * Option constant provider
 *
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Misc;

/**
 * Class Opt
 *
 * @package hollodotme\TreeMDown\Misc
 */
abstract class Opt
{

	/**
	 * The project name
	 */
	const NAME_PROJECT = 1;

	/**
	 * The company name
	 */
	const NAME_COMPANY = 2;

	/**
	 * The project abstract
	 */
	const PROJECT_ABSTRACT = 4;

	/**
	 * Hide empty folders?
	 */
	const EMPTY_FOLDERS_HIDDEN = 8;

	/**
	 * The default file
	 */
	const FILE_DEFAULT = 16;

	/**
	 * Hide filename suffix?
	 */
	const FILENAME_SUFFIX_HIDDEN = 32;

	/**
	 * Prettify file and folder names?
	 */
	const NAMES_PRETTYFIED = 64;

	/**
	 * Enable github ribbon
	 */
	const GITHUB_RIBBON_ENABLED = 128;

	/**
	 * Path include patterns
	 */
	const PATH_INCLUDE_PATTERNS = 256;

	/**
	 * Path exclude patterns
	 */
	const PATH_EXCLUDE_PATTERNS = 512;

	/**
	 * Root directory
	 */
	const DIR_ROOT = 1024;

	/**
	 * Search term
	 */
	const SEARCH_TERM = 2048;

	/**
	 * Currently selected file
	 */
	const FILE_CURRENT = 4096;

	/**
	 * Output type
	 */
	const OUTPUT_TYPE = 8192;

	/**
	 * Raw output type
	 */
	const OUTPUT_TYPE_RAW = 16384;

	/**
	 * HTML output type
	 */
	const OUTPUT_TYPE_DOM = 32768;

	/**
	 * GitHub ribbon URL
	 */
	const GITHUB_RIBBON_URL = 65536;

	/**
	 * Base parameters
	 */
	const BASE_PARAMS = 131072;

}
