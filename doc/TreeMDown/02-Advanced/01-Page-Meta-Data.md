# Page meta data

There are three main strings, that can be personalized to make it **your** TreeMDown.

## Project name

This string appears in the upper left corner of the navigation bar as "brand" and in the lower left
corner of the page footer.

```php

# ...

# Set a project name
$treemdown->setProjectName('My project name');

# ...

```

## Short description

This string appears right side if your project name in the upper navigation bar.

```php

# ...

# Set a short description
$treemdown->setShortDescription('My short description');

# ...

```

## Company name

This string appears in the lower right corner of the page footer right beside your project name.

```php

# ...

# Set a company name
$treemdown->setCompanyName('My company name');

# ...

```

## Full example

```php
<?php

# Init composer autoloading (change this path to fit your project)
require_once 'vendor/autoload.php';

use hollodotme\TreeMDown\TreeMDown;

# Create an instance with the root folder of your markdown files
$treemdown = new TreeMDown('/path/to/your/markdown/files');

# Set the project name
$treemdown->setProjectName('My project');

# Set the short description
$treemdown->setShortDescription('my short description');

# Set the company name
$treemdown->setCompanyName('My company');

# Display the whole page
$treemdown->display();

```
