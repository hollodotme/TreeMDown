# File system options

## Include Patterns

Default value: `array( '*.md', '*.markdown' )`

Change these suffixes to fit your markdown filenames.

**Note:** These patterns will only be applied to filenames, not to pathnames.

```php
# ...

# Set the filename suffixes for all markdown files
$treemdown->setIncludePatterns( array( '*.md', '*.markdown' ) );

# ...
```

## Exclude patterns

Default value: `array( '.*' )` (All files or folders starting with '.' are excluded.)

Change these patterns to exclude files __and/or__ folders from the tree.

```php
# ...

# Set the exclude patterns for files and/or folders
$treemdown->setExcludePatterns( array( '.*' ) );

# ...
```

## Full example

```php
<?php declare(strict_types=1);

namespace YourVendor\YourProject;

# Init composer autoloading (change this path to fit your project)
require_once 'vendor/autoload.php';

use hollodotme\TreeMDown\TreeMDown;

# Create an instance with the root folder of your markdown files
$treemdown = new TreeMDown('/path/to/your/markdown/files');

# Set include patterns
$treemdown->setIncludePatterns( array( '*.md', '*.mdown', '*.markdown' ) );

# Set exclude patterns
$treemdown->setExcludePatterns( array( '.*', '_hideme', 'config/*' ) );

# Display the whole page
$treemdown->display();
```
