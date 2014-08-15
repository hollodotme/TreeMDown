<?php
/**
 * Example script for using TreeMDown
 * @author hollodotme
 */

// include composer autoloading
require_once __DIR__ . '/../vendor/autoload.php';

use hollodotme\TreeMDown\TreeMDown;

// Create instance
$tmd = new TreeMDown( '/path/to/your/markdown/files' );

/**
 * Options section, comment out and change values to fit your needs
 */

# [Page meta data]
#
# Set a projectname
#$tmd->setProjectName('Your project name');

# Set a short description
#$tmd->setShortDescription('Your short description');

# Set a company name
#$tmd->setCompanyName('Your company name');

# [Output options]
#
# Show or hide empty folders in tree
#
# Default: Empty folders will be displayed
#
#$tmd->showEmptyFolders();
#$tmd->hideEmptyFolders();

# Set the default file that is shown if no file or path is selected (initial state)
# The file path must be __relative__ to the root directory above: '/path/to/your/markdown/files'
#
# Default: index.md
#
#$tmd->setDefaultFile('index.md');

# [File system options]
#
# Set the patterns for files you want to include
#
# Default: array( '*.md', '*.markdown')
#
#$tmd->setIncludePatterns( array( '*.md', '*.markdown') );

# Set the patterns for files/path you want to exclude
#
# Default: array( '.*' )
#
#$tmd->setExcludePatterns( array( '.*' ) );

$tmd->display();