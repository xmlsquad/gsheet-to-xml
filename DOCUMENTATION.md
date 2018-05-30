# Google API Setup
- Create project on https://console.developers.google.com/apis/dashboard.
- Click Enable APIs and enable the Google Sheets API and the Google Drive API
- Go to Credentials, then click Create credentials, and select Service account key
- Choose New service account in the drop down. Give the account a name, anything is fine
- For Role I selected Project -> Editor
- For Key type, choose JSON (the default) and download the file. 
This file contains a private key so be very careful with it, it is your credentials after all
- Finally, edit the sharing permissions for the spreadsheet you want to access and share either View 
(if you only want to read the file) or Edit (if you need read/write) access to the client_email address you can 
find in the JSON file.

# Usage
`php bin/console.php forikal:gsheet-to-xml {URL}`

URL should be either Drive or Sheets URL in one of following formats
- https://drive.google.com/drive/folders/xxxxxxxxxx-xxxxxxxxx-xxxxxxxxxxxx
- https://docs.google.com/spreadsheets/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/edit

# Tests
Run `./vendor/bin/phpunit` to execute test suite

# Resources
https://www.fillup.io/post/read-and-write-google-sheets-from-php/
https://developers.google.com/sheets/api/samples/reading
https://stackoverflow.com/a/16840612
https://developers.google.com/sheets/api/guides/concepts

# Troubleshooting
- You have to enable both Sheets and Drive API
- You have to share files/folders with email in credentials JSON file

