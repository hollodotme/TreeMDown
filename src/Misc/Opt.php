<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Misc;

/**
 * Class Opt
 * @package hollodotme\TreeMDown\Misc
 */
abstract class Opt
{
	/**
	 * The project name
	 */
	public const NAME_PROJECT = 1;

	/**
	 * The company name
	 */
	public const NAME_COMPANY = 2;

	/**
	 * The project abstract
	 */
	public const PROJECT_ABSTRACT = 4;

	/**
	 * Hide empty folders?
	 */
	public const EMPTY_FOLDERS_HIDDEN = 8;

	/**
	 * The default file
	 */
	public const FILE_DEFAULT = 16;

	/**
	 * Hide filename suffix?
	 */
	public const FILENAME_SUFFIX_HIDDEN = 32;

	/**
	 * Prettify file and folder names?
	 */
	public const NAMES_PRETTYFIED = 64;

	/**
	 * Enable github ribbon
	 */
	public const GITHUB_RIBBON_ENABLED = 128;

	/**
	 * Path include patterns
	 */
	public const PATH_INCLUDE_PATTERNS = 256;

	/**
	 * Path exclude patterns
	 */
	public const PATH_EXCLUDE_PATTERNS = 512;

	/**
	 * Root directory
	 */
	public const DIR_ROOT = 1024;

	/**
	 * Search term
	 */
	public const SEARCH_TERM = 2048;

	/**
	 * Currently selected file
	 */
	public const FILE_CURRENT = 4096;

	/**
	 * Output type
	 */
	public const OUTPUT_TYPE = 8192;

	/**
	 * Raw output type
	 */
	public const OUTPUT_TYPE_RAW = 16384;

	/**
	 * HTML output type
	 */
	public const OUTPUT_TYPE_DOM = 32768;

	/**
	 * GitHub ribbon URL
	 */
	public const GITHUB_RIBBON_URL = 65536;

	/**
	 * Base parameters
	 */
	public const BASE_PARAMS = 131072;
}
