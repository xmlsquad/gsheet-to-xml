# Installation
- Clone this project
- Run `composer install`
- test the console app: `php bin/console.php --help`

__Note__ Make sure you're using at least PHP 7.1

# Google API Setup

See: [How to: Google API Setup](https://github.com/forikal-uk/xml-authoring-library/blob/master/HowTo-GoogleAPISetup.md)


# Usage
`php bin/console.php forikal:gsheet-to-xml {URL} [--credentials=client_secret.json]`

`{URL}` should be either Drive or Sheets URL in one of following formats
- https://drive.google.com/drive/folders/xxxxxxxxxx-xxxxxxxxx-xxxxxxxxxxxx
- https://docs.google.com/spreadsheets/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/edit

`--credentials` is optional parameter that specifies path to the credentials file with Google secret. Path
must be relative to the directory you're calling the script from.

# Behavior

- Empty rows are skipped without notice.
- Files ending with _ (underscore) are ignored (e.g. foo_.xslx, foo_)
- Tabs ending with _ (underscore) are ignored
- When parsing folders, for every spreadsheet found new `<Product>` XML element is created with `src-spreadsheet` attribute
containing the spreadsheet ID.

# Tests
Run `./vendor/bin/phpunit` to execute test suite

# Resources
- https://www.fillup.io/post/read-and-write-google-sheets-from-php/
- https://developers.google.com/sheets/api/samples/reading
- https://stackoverflow.com/a/16840612
- https://developers.google.com/sheets/api/guides/concepts

# Troubleshooting
- You have to enable both Sheets and Drive API
- You have to share files/folders with email in credentials JSON file

