# Search

Type `php` into the search field in the upper right corner and hit ENTER.

## What happens?

1. All files in the tree will be `grep`'ed für `php`.
2. All folders that contain files containing the word `php` will be expanded.
3. You can see the number of occurences badged at each file.
4. You can see the sum of ocurrences in files badged at each folder.
5. You can see a summary of occurences on the left side of the search field.
6. The search will be permanent while browsing the tree an viewing documents.
7. You can reset/cancel the search by clicking the red button on the right side of the search field.

## Search with placeholder

The search is done with grep, so you can use `*` or `.*` for searching with placeholders.

For example type: `number of .* badged` (There should be only 2 occurences in this file)

**Note:** The search will grep over the files __before__ they are transaated to HTML.
