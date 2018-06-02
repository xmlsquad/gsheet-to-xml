# sheet-to-xml

Symfony Console command that, given the URL of a (specifically structured) Google Sheet or Google Drive folder of sheets, outputs the contents (perhaps in multiple tabs) in the form of Xml.

Designed be used in the context of the Symfony Console application at https://github.com/forikal-uk/xml-authoring-tools which, in turn, is used in the context of a known directory structure which is based on [xml-authoring-project](https://github.com/forikal-uk/xml-authoring-project).

# Documentation

See: https://github.com/forikal-uk/gsheet-to-xml/blob/master/DOCUMENTATION.md

# Original Specification:

## Schema descriptions

I have published

* a [schema defining how the Google Sheets map to Xml](https://docs.google.com/spreadsheets/d/1ooblH26ti5CyEJvJsLygXUJVnBvT1h6DUf67gCkrbZE/edit?usp=sharing).
* an example [Inventory sheet](https://docs.google.com/spreadsheets/d/1kU_R8RokoMy9qvJqxy72H58cS48EVs0zRJXcgTZ5YFI/edit?usp=sharing).
* an example [Xml representation of Inventory data](https://github.com/forikal-uk/xml-authoring-project/blob/master/src/Inventory/Inventory.xml).
* some of the [rules that define the valid structure of an Inventory sheet](https://github.com/john-arcus/GasInventoryValidator/blob/master/features/ValidateUploadedInventoryFiles.feature).

## Naming convention. 

Given a Google Sheet, unless the sheet's name implies it is 'ignored', it is assumed that its structure is valid structure of an Inventory sheet.
Given a Google Drive folder, it is assumed that any Google Sheets found within have the valid structure of an Inventory sheet (unless the sheet's name implies it is 'ignored').

A Google sheet's with a trailing underscore in their name imply that it should be 'ignored'. 
A Google sheet tab's with a trailing underscore in their name imply that it should be 'ignored'.

If a file is called `foo`, then it is validated as normal.
If a file is called `foo_`, then it is assumed to be 'private' and should be explicitly ignored, but it should be noted (in any feedback) that it was ignored.
If a Google Sheet's tab is named `foo`, then it is validated as normal.
If a Google Sheet's tab is named `foo_`, then it is assumed to be 'private' and should be explicitly ignored, but it should be noted  (in any feedback) that it was ignored.


## Input

- drive-url: The URL of the Google Drive entity (Google Sheet or Google Drive folder).

- is-recursive: if the Google Drive entity is a Google Drive folder, this option specifies whether or not to recurse through sub-directories to find sheets.


## Behaviour

If the `drive-url` is a Google Sheet write out the contents as Xml.
If it is a Google Drive find all Google Sheets within the directory (recursively, if specified) and write out the contents as Xml.

## Output

STD_OUT

Streamed output. 

Xml representation of the Inventory data in the Google Sheet.  


