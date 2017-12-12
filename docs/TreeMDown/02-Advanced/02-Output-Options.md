# Ouput options

These options are provided to change some output behaviour to fit your needs.

## Show/hide empty folders

Default behaviour: Empty folders will be shown.

If empty folders were hidden every folder that has no content won't be displayed in the tree.
Of course the red colored hint "Directory has no files matching the filter." won't appear anymore, too.

```php
# ...

# Hide empty folders
$treemdown->hideEmptyFolders();

# or

# Show empty folders (default)
$treemdown->showEmptyFolders();

# ...
```

## Default markdown file

Default value: `index.md`

Sets the default markdown file that will be shown if no file or path is selected (initial state).
The given file or path/to/file must be __relative__ to the root directory of your markdown files.

```php
# ...

# Set start/README.md as default file
# This will resolve to: /path/to/your/markdown/files/start/README.md
$treemdown->setDefaultFile('start/README.md');

# ...
```

## Show / hide filename suffix

Default behaviour: Filename suffix will be shown.

```php
# ...

# Hide filename suffix
$treemdown->hideFilenameSuffix();

# or

# Show filename suffix (default)
$treemdown->showFilenameSuffix();

# ...
```

## Enable / disable pretty directory and file names

Default behaviour: Pretty names are disabled.

```php
# ...

# Enable pretty names
$treemdown->enablePrettyNames();

# or

# Disable pretty names (default)
$treemdown->disablePrettyNames();

# ...
```

## Show / hide GitHub ribbon

Default behaviour: The GitHub ribbon is hidden.

If the GitHub ribbon is enabled it will be shown fixed in the lower left corner of the page and
link to the [TreeMDown GitHub repository](https://github.com/hollodotme/TreeMDown).

```php
# ...

# Enable the GitHub ribbon
$treemdown->enableGithubRibbon();

# or

# Disable the GitHub ribbon (default)
$treemdown->disableGithubRibbon();

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

# Hide empty folders
$treemdown->hideEmptyFolders();

# Hide filename suffix
$treemdown->hideFilenameSuffix();

# Enable pretty names of directories and files
$treemdown->enablePrettyNames();

# Set the default file
$treemdown->setDefaultFile('start/README.md');

# Enable the GitHub ribbon
$treemdown->enableGithubRibbon();

# Display the whole page
$treemdown->display();
```
