# Contao Search Entity Bundle

A helper to find where contao entities like frontend module or content elements are located. Currently this bundle contains a command which enabled the search of different entities type and outputs where they are located. Later a backend module is planned.

![](docs/screenshot_command.png)

## Features

- search for following entities:
    - content elements
    - modules
    - article
    - page
- following search locations are supported for entites:
    - content elements:
        - parent table
    - frontend modules
        - module content elements
        - layout
        - block-modules
        - insert_module inserttag within html frontend module
    - articles
        - parent page
    - page
        - parent page
    - layout
        - parent theme
    
## Usage

## Install 

Install with composer or contao manager

    composer require heimrichhannot/contao-search-entity-bundle

## Search command

Search is done with the `huh:search-entity` command.

Example:

    php vendor/bin/contao-console huh:search-entity -m 3

```
Usage:
  huh:search-entity [options]

Options:
  -c, --contentelement[=CONTENTELEMENT]  The id of an content element
  -m, --frontendmodule[=FRONTENDMODULE]  The id of an frontend module
```