---
navigation: Preferences
title: 
description:
---

### Preferences ###

Here you can specify the settings for the entire page. Nearly all the entries are self-explanatory.

#### Page title / Subtitle

The default page title (can be overwritten in each page). Depending on the active template, this information may appear at different locations.

#### System E-Mail

This information will be used for shipping the system emails. For example, when the confirmation link will be sent to a new user.


#### Global Headers (HTML)

Here you can, for example, link Javascript libraries or save CSS specifications. This information is loaded into each page. A typical example would be the Google Analytics code.


#### Backup

All database files can be downloaded to the local computer by a single click. For security reasons, there is no upload function for these files. To activate a backup again, the SQLite file(s) must be uploaded via FTP to the directory <code>/content/SQLite/</code>.

To save space, older statistics databases (<code>logfile***. Sqlite3</code>) can be deleted directly from the here.

***

## Updates

#### Auto Update

Once an update is available, you see a message in the Backend -> System. Clicking on __Update__ starts the script. This function updates __all__ necessary directories and files. Also the default theme will be completely replaced.
