# Internal links

## Basic linking

To create a link that points to another markdown file in your current tree, simply add a link with a relative url.

### Example

[Go to "What is TreeMDown"](01-What-Is-TreeMDown.md)

```md
[Go to "What is TreeMDown"](01-What-Is-TreeMDown.md)
```

**Note:** 
 * The url must be relative to your tree root, __not__ to your current file.
 * If you currently have a search term set, it will be preserved in the link.
 * All your file system options will be considered. (e.g. excluded files won't be linked)
  

## Linking with search term

You can add a (new) search term to your internal link by adding a `?q=[Searchterm]`.

So you can easily point to another topic and make more occurances visible in the tree.
 
### Example 

[Have a look at auto table examples!](03-Examples/01-Nested-Table-Of-Contents.md?q=table)

```md
[Have a look at auto table examples!](03-Examples/01-Nested-Table-Of-Contents.md?q=table)
```

## Linking to raw output

You can link directly to the raw content of a file inside of your tree by adding a `?raw`.

## Example

[Watch the source of this file!](02-Advanced/04-Internal-Links.md?raw)

```md
[Watch the source of this file!](02-Advanced/04-Internal-Links.md?raw)
```