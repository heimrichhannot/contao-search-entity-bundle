> This Bundle is deprecated as the functionality is integratet in [Utils Bundle](https://github.com/heimrichhannot/contao-utils-bundle/blob/master/docs/commands/entity_finder.md).

# Contao Search Entity Bundle

A helper to find where contao entities like frontend module or content elements are located. Currently this bundle contains a command which enabled the search of different entities type and outputs where they are located. Later a backend module is planned.

![](docs/screenshot_command.png)

## Features

- search for following entities:
    - content elements
    - modules
    - article
    - page

## Usage

## Install 

Install with composer or contao manager

    composer require heimrichhannot/contao-search-entity-bundle

## Search command

Search is done with the `huh:search-entity` command.

Examples:

    php vendor/bin/contao-console huh:search-entity module 3
    php vendor/bin/contao-console huh:search-entity tl_content 15

Help output:

```
Usage:
  huh:search-entity <type> <id>

Arguments:
  type                  The type you search for. Could be a name or a table.
  id                    The id or the element you search for.

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -e, --env=ENV         The Environment name. [default: "dev"]
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
  This command located an contao entity an return it's location.
  
  You can search for entities within the following tables: tl_article, tl_block, tl_block_module, tl_content, tl_layout, tl_module, tl_page, tl_theme
  
  You can use following aliases for tables: contentelement (tl_content), c (tl_content), frontendmodule (tl_module), module (tl_module), m (tl_module), article (tl_article), a (tl_article), page (tl_page), p (tl_page)
```

## Information

### Search locations

A list about where is searched for entities (recursive).

Articles (`tl_article`)
- parent page

Blocks (`tl_block`)
- themes
- block frontend modules

Block modules (`tl_block_module`)
- parent block

Content elements (`tl_content`)
- parent table

Frontend modules (`tl_module`)
- module content elements
- layout
- block-modules
- insert_module inserttag within html frontend module
  
Layouts (`tl_layout`)
- parent theme
- pages using this layout

Pages (`tl_page`)
- parent page
