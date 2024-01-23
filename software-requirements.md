# About the project


# Requirements and specifications

[R] The app displays a title message at startup.

[R] The app can extract snippets with requirements from a requirements doc and from PHP source code and compare them.

It works like this:
 - [extract](#extracting-requirements) requirements from a requirements doc (text/markdown)
 - [extract](#extracting-requirements) requirements from PHP source code
 - [compare](#comparing-requirements) the requirements
 - report the differences

[R] The parameters are provided via the command line:
 - the requirements doc file
 - the source code directory

[R] The app displays a help message if the user does not provide the correct arguments.

## Extracting requirements

The app can extract the requirements from:
 - a [text/markdown](#textmarkdown-files) file
 - a [PHP](#php-files) file

[R] If the marker is escaped like this `[R]`, skip it.

### Text/markdown files

For now, let's assume that a requirement is just one line of text that starts with a marker `[R]`.

[R] Whenever we encounter a marker in the file, we take the rest of the line as a requirement item.

### PHP files

For now, let's assume that a requirement is just one line of text anywhere in a comment that starts with a marker `[R]`.

[R] Whenever we encounter a marker in a comment, we take the rest of the line as a requirement item.

## Comparing requirements

Requirements are compared by hash of their content. 

[R] Let's use SHA-1 like git does.

## Reporting the differences

[R] The app calculates the totals:
- total number of requirements
- total number of requirements in the requirements doc
- total number of requirements in the source code
- total number of requirements in both the requirements doc and the source code
- total number of requirements in the requirements doc but not in the source code
- total number of requirements in the source code but not in the requirements doc

[R] The app displays the line numbers of requirements in the requirements doc which are missing from the source code.

[R] The app displays the file names and line numbers of requirements in the source code which are missing from the requirements doc.

# Entities and rules

## ReqItemLocation

- filename
- line number

## ReqItem

- content
- location in req doc
- location in source code

## ReqCollection

- items

## ComparisonResult
