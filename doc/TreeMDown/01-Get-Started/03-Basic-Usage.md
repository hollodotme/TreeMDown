# Basic usage

```php
<?php

# Init composer autoloading (change this path to fit your project)
require_once 'vendor/autoload.php';

use hollodotme\TreeMDown\TreeMDown;

# Create an instance with the root folder of your markdown files
$treemdown = new TreeMDown('/path/to/your/markdown/files');

# Display the whole page
$treemdown->display();

```
