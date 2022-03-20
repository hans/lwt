Changelog
=========

This project's changelog. Version marked with "-fork" come from the community, other version come from the canonical LWT ("official" branch on Git).
For git tags, official relases are marked as "v1.0.0", while unofficial ones a marked "v1.0.0-fork".

## [Unreleased]
### Added
* Commands included in ``Makefile`` where transcripted in ``composer.json``.
* Docker integration! It is done through ``Dockerfile``, ``docker-compose.yml``, ``.dockerignore`` and ``.env`` files.

### Changed
* mbstring and mysqli extensions are now clearly asked for by Composer.
* The "database update" part of ``check_update_db`` in ``inc/database_connect.php`` was moved to ``update_database``.
* Almost all JS goes in one file when minified. This has several reasons:
  * Better browser caching: JS code is downloaded once for all.
  * Easier maintaining: it was difficult to manage which php file was needing which JS script.
  * Consistency: scripts were calling functions that should be imported from other files, making the debugging difficult.
* Feed wizard changes
  * The feed wizard got a little broken: the "next" button is always active, even if you did not select text and click "Get".
  * It's style was uniformized with your current theme. 

### Deprecated
* Use ``clean-doc`` install of ``clean`` in ``Makefile`` because it was ambiguous with composer commands.

### Fixed
* Creating the database was sometimes impossible.
* Database names containing special characters (hyphens, carets, etc...) was not possible.

## 2.2.2-fork (February 13 2022)
### Added
* A contribution guide at ``docs/contribute.md``.

### Changed
* Updated the jQuery deprecated events. It should have no consequence.
* Slightly changed the behavior of the CSS/JS minifiers. The relative paths in the return string were different from the ones in the saved file.
* Regenerated documentation.

### Fixed
* Some AJAX files could not work properly since 2.0.3-fork.
  * Refreshing the list of audio files in ``edit_texts.php`` works again.
  * Theme in ``info.html`` works now.
  * Some other problems may have been fixed.
* With ``do_test?text=``, the language name displayed instead of "[L2]" was often wrong.
* ``$fixed_tbpref`` was never declared at global scope.
* Fixed an incompatibility in ``database_connect.php``, ``splitCheckText`` with PHP <7.4.
* Impossible to start bulk_translate_new_words from ``do_text.php``.
* The audio player does no longer show at the end of the page (DOM node was not closed).
* Relative paths in themes were often broken. For instance: no images in audio player.
  * An explanation was also added on how to add custom images in your theme.
* Very small errors corrected in some themes.

### Deprecated
* Deprecated a lot of camelCase functions in ``do_text_text.php`` to their snake_case counterpart. The behavior of the deprecated functions did not change.
  * camelCase to snake_case: ``getTextData`` to ``get_text_data``, ``sentenceParser`` to ``sentence_parser``, ``wordParser`` to ``word_parser`` and ``mainWordLoop`` to ``main_word_loop`` 
  * Typo fixing: ``getLanguagesSettings`` to ``get_language_settings`` (use singular)
  * Signature changed: ``echoTerm`` to ``echo_term`` (no return value, no ``$hideuntil`` parameter)
  * Name uniformisation: ``prepareStyle`` to ``do_text_text_style`` and  ``do_text_javascript`` to ``do_text_text_javascript``.

## 2.2.1-fork (February 07 2022)
### Changed
* Composer in no longer *required* for standard users (but is still required for contributing).
* Updated ``README.md``.
* [league/commonmark](https://packagist.org/packages/league/commonmark) becomes a dev requirement (no longer required for everyone).
* Re-minified ``jquery.tagit.css``, it may have visual consequences.
* Replaced some jQuery functions by their equivalents. It should not have consequences.

### Fixed
* Calling ``do_text.php?text=`` created a database error, it does no longer.
* Long text were laggy in ``do_text_text.php`` since 2.0.3-fork. Some other issues may have been fixed at the same time.
* Possible unsecure PHP string to JS string conversion in ``do_text_header.php``, function ``browser_tts``.

### Deprecated
* The ``is_mobile`` function now always returns false.
* The ``wordProcessor`` (``do_text_text.php``) function was incorrect. 
  * It now always return 0.
  * Please use ``wordParser``, ``sentenceParser`` and some more code instead.

### Removed
* Removed unnecessary dependencies:
  * [components/jquery](https://packagist.org/packages/components/jquery) version ^3.6 was required, but only 1.12.4 was in use.
  * [flesler/jquery.scrollto](https://packagist.org/packages/flesler/jquery.scrollto) already bundled by git.
  * [mobiledetect/mobiledetectlib](https://packagist.org/packages/mobiledetect/mobiledetectlib) was unused. It is now removed.
  * [happyworm/jplayer](https://packagist.org/packages/happyworm/jplayer) is no longer integrated by composer (no update since 2014), but it still in use.
* The ``components/`` folder was also deleted. It was bundling JS code from composer.
  * It should have been git ignored at least.
  * Its content was unused. The files it was trying to use are duplicated.

  ### Full Changelog
* [v2.2.0...v2.2.1](https://github.com/HugoFara/lwt/compare/v2.2.0...v2.2.1)

## 2.2.0-fork (February 04 2022)
### Added
* Sounds while testing terms!
* New minifier for themes: it is now very easy to create new themes.
* JS files added to documentation.
* ``info_export_template.md`` was imported and adapted from official documentation.

### Changed
* All the do_test*.php part do no longer use frames.
* All the display_impr_text*.php part do no longer use frames.
* It means LWT is now mobile-friendly! 
* Regenerated themes.
* "do_text.php?text=..." is the official way to call texts. The "start" argument is still supported.
* New and enhanced design for the welcome page (``index.php``)
* Enhanced semantic for ``docs/info.html``, and for ``docs/*.md`` files.
* The Doxygen-generated content now uses LWT default style.

### Fixed
* Several database flaws fixed. Now the database should stop rotting with time.
* It is easier to close the right frames in do_text.php.
* When viewing the maximum number of results per page (edit_text.php for instance),
the maximum value was shown as a floating point value.

### Removed
* Floating Menu from JTricks.com was unused since 2.0.4-fork. It is now deleted.

### Full Changelog
* [2.1.0...v2.2.0](https://github.com/HugoFara/lwt/compare/2.1.0...v2.2.0)

## 2.1.0-fork (January 09 2022)
### Added
* Badges in the README providing up-to-date information on the state of the project.
* When reading, right frames are hidden and will slide into screen when needed.

### Changed
* PHP >=7.4 is now the official PHP version.
* Refactored the do_test* pages.
* Better CSS minification.
* Code base inconsistencies and security issues fixed 
(level 5 and above psalm errors fixed).
* Code is much more strongly typed (~80% of the code base). Level 4 psalm errors partially fixed.
* GitHub continuous integration reviewed.
* Regenerated documentation.

### Fixed
* ``composer.json`` is now working! Thanks [chaosarium](https://github.com/chaosarium) for signaling 
this [issue #4](https://github.com/HugoFara/lwt/issues/4)!
* Since 2.0.3-fork, it was difficult to get annotations. This is no longer the case.
* [tag-it](https://github.com/aehlke/tag-it), [jquery-hoverintent](https://github.com/briancherne/jquery-hoverIntent), 
and [jquery-xpath](https://github.com/ilinsky/jquery-xpath) are now copied from raw code, and no longer integrated by composer.

### Removed
* Effectively dropped support for PHP <=7.

### Full Changelog
* [v2.0.4-fork...v2.1.0](https://github.com/HugoFara/lwt/compare/v2.0.4-fork...v2.1.0)

## 2.0.4-fork (December 03 2021)
This version brings a better composer compatibility, and starts revamping 
mobile compatibility.

### Changed
* Starting to refactor for 2021 HTML!
* Much less iframes for reading texts.
* Now you can read texts on mobile without the experimental mobile LWT.
* Texts can be read using "focus mode" on most browsers.
* Updated documentation (expanded and refactored).
* Composer is now the recommended way to download lwt.
* PHP_codesniffer is now recommended, and no longer dev-required.
* Refactored many parts of the code, that gets easier to read.

### Removed
* ``composer.phar`` and ``composer.lock`` are now git ignored.
### Full Changelog
* [v2.0.3-fork...v2.0.4-fork](https://github.com/HugoFara/lwt/compare/v2.0.3-fork...v2.0.4-fork)

## 2.0.3-fork (November 26 2021):
Serious maintaining is back!

This version should be the stable merge between official v2.0.2 and community maintained 1.6.31-fork.
### Added

* Show Learning translation setting.
* README.md created
* Code documentation.
* Automatic text-to-speech.
* Integrating Composer to manage dependencies.
* Issue templates for GitHub.
* Markdown integration in PHP.
* Video player for texts.
* Makefile to simplify workflows.

### Changed

* MeCab is now the default way to learn Japanese.
* JS and CSS are now minified.
* Code liniting.
* Important code refactors.

### Full Changelog 
* [v1.6.31-fork...v2.0.3-fork](https://github.com/HugoFara/lwt/compare/v1.6.31-fork...v2.0.3-fork)

## 2.0.2 (September 07 2021)
### Fixed
* An incompatibility with PHP 8+ (removed function "get\_magic\_quotes\_gpc()" in PHP 8+) has been fixed. Thanks to Lucas L. for the hint.  
      
    
## 2.0.1 (October 07 2020)
### Fixed
* A bug when visiting terms/expressions with key strokes LEFT or RIGHT after a previous status change and with a set status filtering has been fixed.  
      
    
## 2.0.0 (October 04 2020)
### Fixed
* No code changes. Sourceforge links corrected.  
* The old links \[lwt.sf.net\], \[lwt.sourceforge.net\] or \[sourceforge.net/projects/lwt\] are no longer valid!  
* The new links are now [learning-with-texts.sourceforge.io](https://learning-with-texts.sourceforge.io) (documentation and demo database) and [sourceforge.net/projects/learning-with-texts](https://sourceforge.net/projects/learning-with-texts) (project home and downloads).  
      
    
## 1.6.3 (April 06 2020)
### Added
* Some missing confirmation dialogues (when deleting a single text, text tag, term, term tag, or language) added.  
      
    
## 1.6.2 (March 10 2018, this page "info.php" last updated August 12 2019)
### Added  
* Audio playback speed can now be set between 0.5x and 1.5x.  
* Waiting wheel (to indicate saving data to database in the background) added in "Edit Improved Annotated Text".  
* Checking for characters in the Unicode Supplementary Multilingual Planes (> U+FFFF) like emojis or very rare characters improved/added. Such characters are currently not supported.  
### Changed
* jQuery library updated to v1.12.4.  
    "Mobile\_Detect.php" updated to v2.8.30.  
* LWT demo database updated.  
* Documentation updated.
### Fixed
* Some minor glitches fixed.
### Removed
* Glosbe API calls via "glosbe\_api.php" in demo database and language settings wizard removed - they often did not work due to API restrictions. The file "glosbe\_api.php" is still supplied as an example of a close integration of a dictionary API into LWT.  
      
    
## 1.6.1 (February 01 2016, this page "info.php" last updated January 13 2018)
### Changed
* The jQuery and jPlayer libraries have been updated to v1.12.0 and v2.9.2, respectively. The jQuery.ScrollTo package has been updated to v2.1.2.  
    [Link](#links) to Chinese text segmentation "Jieba" added in documentation (Important Links - Additional Resources - For learners of Chinese).  
      

## 1.6.31-fork (October 03 2016)
### Fixed
* Multibyte character parsing fixed (i.e. Chinese).  
      
    
## 1.6.30-fork (July 28 2016)
### Added
* MeCab support (in development).
### Fixed
* Google translate API updated.  
   
    
## 1.6.29-fork (April 21 2016):

### Changed
* In appreviations like 'Mr.' the dot is now part of the term. A reparse of texts is needed to take effect.  
* Wizard Language: Arabic 'RegExp Word Characters' changed.  

### Fixed
* ggl.php API doesn't work (Token generation fixed).  
* Dictionary doesn't open.  
* Negative/uncorrect WordCount in 'edit\_texts\_php'.  

      
    
## 1.6.28-fork (April 07 2016):
### Added
* DB collation check added.

### Changed
* Wizard Language Defaults changed.

### Fixed
* Access denied, LOAD DATA INFILE error (text parsing on a server).  
* 'remove spaces' not working.  

      
    
## 1.6.27-fork (February 21 2016):
### Fixed  
* Bugfix: DB backup/import errors.
* RegExp Word Characters are checked for correct syntax when creating/updating language.

### Removed
* thumbnail/image support removed.  
      
    
## 1.6.26-fork (February 11 2016):

### Changed
* Demo Database updated.  
* mysqli changes from orig. LWT.  
* php-mobile-detect updated.  

### Fixed
* Some bugfixes: errors when emptying database and installing Demo database, added 'follow redirect' to feed.  
      
    
## 1.6.25-fork (January 31 2016):  
### Added 
* Added German Feed 'NachDenkSeiten'(url:'http://www.nachdenkseiten.de/?feed=audiopodcast', Article Section:'//encoded/p', Options:'Article Source:encoded') to DemoDatabase.  
* Added saved expressions to CheckText.  
### Changed
* SplitCheckText rewritten.  
* Database changes (table 'temptextitems'): added: TiCount, dropped: TiLgID, TiTxID, TiTextLC and index TiTextLC  
* Database changes : dropped: index WtWoID, index TtTxID, index AgAtID  
### Fixed
* Bugfix: No Word Counts displayed when text has no saved words. 
      
## 1.6.0 (January 28 2016):  
### Changed
* As mysql\_\* database calls are deprecated and are no longer supported by PHP, they have been changed to the corresponding mysqli\_\* calls. If you run a server with PHP version 7.0.0 or higher, you MUST use LWT 1.6.0 or higher. Thanks to Laurens Vercaigne for his work!  
* Debugging updated. Status information on start page improved. Documentation updated.  
      
    
## 1.5.21 (January 14 2016):  
### Changed
* [Soft hyphens](https://en.wikipedia.org/wiki/Soft_hyphen) (U+00AD, UTF-8: 0xC2 0xAD) are now automatically removed during text import.  
* "Mobile\_Detect.php" updated to v2.8.19.  
      
    
## 1.6.24-fork (January 11 2016):  
### Added
* Added possibility to switch between 'unique' and 'total' word count by clicking on 'u'/'t'-button.  
### Changed
* HTML lang attribute added in testing frame.  
* Speed improvements in 'My Texts' screen.  
* Replaced precentage of 'unknown words' by 'word charts' in 'My Texts' screen.  
### Removed
* Deleted Setting: Show Word Counts of Texts immediately  
      
    
## 1.6.23-fork (December 13 2015):  
### Added
* HTML lang attribute added in reading frame.  
### Changed
* Jplayer, Jquery, JqueryUI updated.  
* Google Translate API changes: random google domain access added (domain can be changed in googleTranslateClass.php), updated generateToken.  
      
    
## 1.6.22-fork (November 11 2015):  
### Added
* Google Translate API(ggl.php): added headers and corrected token.  
      
    
## 1.6.21-fork (October 16 2015):  
### Added
* Google Translate API(ggl.php): added token to URL.  
      
    
## 1.6.20-fork (September 26 2015):  
### Changed
* Jquery Changes in the reading frame for hover\_over/highlight words.  
### Fixed
* Google TextToSpeech callback fixed.  
      
    
## 1.6.19-fork (August 29 2015):  
### Added
* New default settings: 'Tooltips' (new Default: 'JqueryUI') and 'Position of translations' (new Default: 'below').  
### Changed
* Mysql login process changed.  
* JPlayer CSS and Skin changes. Skin are now integrated into 'Themes'.  
      
    
## 1.6.18-fork (June 11 2015):  
### Added
* New Setting: Position of translation  
* Translations can now be displayed 'behind', 'in front of', 'above' or 'below' the term in the reading frame.  
* Improved encoding detection in 'newsfeed import'.  
### Fixed
* Bugfix: Words that are created by 'bulk import' are not updated in the reading frame (i.e. when new translations are added afterwards).  
* Bugfix: mysql error 'duplicate entry' in 'newsfeed import'.  
      
    
## 1.6.17-fork (May 09 2015):  
### Fixed
* Bugfix: Newsfeed Import doesn't load new links if WordPress is used for multiple users.  
      
    
## 1.6.16-fork (May 01 2015):   
### Changed
* Improved Sentence Bondary Detection when parsing texts.  
* User ID is saved in the PHP Session Variable (instead of a Cookie) if wordpress is used to log in. 
* Switch to mysqli extension for database connection.  
* Minified CSS and Javascript files; the uncompressed files can be found in the directory 'src'.  
      
    
## 1.6.15-fork (April 10 2015): 
### Changed
* Some CSS changes.   
### Fixed
* Ggl API retrieval error fixed.  
      
    
## 1.6.14-fork (March 28 2015):  
### Changed
* Glosbe API now uses javascript (browser based) instead of php(server based) to prevent possible retrieval errors when LWT is installed on a webhoster for multiple users.  
      
    
## 1.6.13-fork (March 23 2015):  
### Added
* New Setting: Tooltips (JQueryUI will show images in Tooltips in the Read Text Screen)  
* New Feature: You can now add thumbnail images to your terms. If you click on the icon at the left of the translation field in the new\_term/edit\_term frame/window you can select an thumbnail from 'google image search'. In order to display the images in the Read Text Screen you must set 'Tooltips' to 'JQueryUI'. The thumbnail images are not included in the backup at the moment.  
* New Feature: Key binding J for edit term with Google Image Search added  
### Fixed
* 'error when making backup' fixed  
      
    
## 1.6.12-fork (March 01 2015):  
### Changed
* Jquery, JqueryUI updated  
### Fixed
* CSS/jquery fixes when selecting multiple word expressions in text frame  
* Bugfix: new or imported multiple word expressions are not show in the text with 'remove Spaces' is 1 and 'split Each Char' is 0  
      
    
## 1.6.11-fork (February 09 2015):  
### Fixed
* Bugfix: 'Show term sentences' and 'Create term sentences' fixed  
      
    
## 1.6.10-fork (January 25 2015):  
### Changed
* CSS changes for firefox (version >= 35) in dark themes  
### Fixed
* Bugfixes in bulk import terms  
      
    
## 1.6.9-fork (December 21 2014):  
### Fixed
* Getting article from feed even if there is no link  
      
    
## 1.6.8-fork (December 19 2014):
### Changed
* Google API can now do a requery  
* Dict Lookup from bulk import terms frame is now possible  
      
    
## 1.6.7-fork (December 18 2014):  
### Fixed
* Database errore in newsfeeds  
      
    
## 1.6.6-fork (December 16 2014):
### Added
* Ability to change audio playback speed (doesn't work when using the flash plugin)  
* Combine translation field option when importing words ('Merge translation fields' or 'Update existing translations')  
      
    
## 1.6.5-fork (December 01 2014):
### Fixed
* Error in 'upload\_words.php'  
      
    
## 1.6.4-fork (November 29 2014):
### Fixed
* Wrong dict links for sentence translate  
      
    
## 1.6.3-fork (October 12 2014):
### Added
* Key binding G for edit term with Google Translate added
### Changed
* Google api rewritten (works on webhoster with cURL-plugin)  
      
    
## 1.6.2-fork (October 06 2014):
### Added
* Key binding T for translating sentence added  
* New Backup Option: official LWT backup added

### Changed
* Database Changes: indexes changed in table words  
      
    
## 1.6.1-fork (September 28 2014)
### Added
* Translations of terms can now be display in the reading frame  
* Key bindings when hovering over words in the reading frame  
* Bulk translate new words in the reading frame

Changes from official LWT version 1.5.20 imported:  
    
* Possibility to display similar terms while creating or editing a term. This will give you more language insight, and may ease inputting new terms that are similar. The number of displayed similar terms can be set from 0 (old behavior, default) to 9 on the "Settings" page. Clicking on the green icon in front of a similar term will copy the translation and romanization into the form fields for further editing. Important: If you want to use this new feature, you must change the setting "Similar terms to be displayed while adding/editing a term" to a value greater than 0. It will make more sense to do this if you have already many saved terms (e.g. more than 1,000). If you start with a language and have only a few terms, no or not very similar terms will be normally displayed and this feature will not make much sense.  
        
* "https://" dictionary URIs are now allowed in the language settings. Checking of dictionary URIs in the language settings has been improved.

### Changed
* The Glosbe dictionary page has been improved with a simple form to change the term and do a requery if you are unhappy with the query results.  
        
* The jQuery and jPlayer libraries have been updated to v1.11.1 and v2.7.0, respectively. The jQuery.ScrollTo package has been updated to v1.4.13. The Floating Menu package has been updated to v1.12.  

* Broken links corrected or deleted. From now on, only the installation with EasyPHP for Windows and MAMP for Mac OS X will be explained in detail and is recommended. Other local web server packages, like XAMPP, etc., are of course still possible, but it's beyond the scope of this document to explain all the details for every webserver package (it will also confuse most LWT users who are not familiar with web server packages and their setup).
* "Mobile\_Detect.php" updated to v2.8.3.
    
* Documentation updated.  
        

## 1.5.20 (September 22 2014):
### Changed
* "Mobile\_Detect.php" updated to v2.8.3.

### Fixed
* Missing volume controls in audio player (only on mobile devices) fixed.  
      
   
## 1.5.19 (September 15 2014):  
* Missing tag cache updating fixed (in "Add tag in all/marked texts or terms").  
* Tag caches now also work properly if several instances of LWT are installed in parallel directories on the same server.  
* Information about [which web browser to use for LWT](#abstract) in this document updated.  
      
    
## 1.5.18 (September 14 2014): 
### Added
* Possibility to display similar terms while creating or editing a term. This will give you more language insight, and may ease inputting new terms that are similar. The number of displayed similar terms can be set from 0 (old behavior, default) to 9 on the "Settings" page. Clicking on the green icon in front of a similar term will copy the translation and romanization into the form fields for further editing. Important: If you want to use this new feature, you must change the setting "Similar terms to be displayed while adding/editing a term" to a value greater than 0. It will make more sense to do this if you have already many saved terms (e.g. more than 1,000). If you start with a language and have only a few terms, no or not very similar terms will be normally displayed and this feature will not make much sense.
* New sort option for texts, terms or tags: "Oldest first".  
* The Catalan language has been added to the Language Settings Wizard.

### Changed
* "https://" dictionary URIs are now allowed in the language settings. Checking of dictionary URIs in the language settings has been improved. The Glosbe dictionary page has been improved with a simple form to change the term and do a requery if you are unhappy with the query results.  
* The jQuery and jPlayer libraries have been updated to v1.11.1 and v2.7.0, respectively. The jQuery.ScrollTo package has been updated to v1.4.13. The Floating Menu package has been updated to v1.12.  
* Some error messages (term/tag already exists) have been improved.  
* Documentation updated.

### Fixed
* Some minor bugs fixed: media selection in archived texts, tag import errors, adding existing tag errors, etc.

### Removed
* The audio player skin selection has been removed; the "Blue Monday Small" skin is the standard skin beginning with this release.  
      
       
## 1.6.0-fork (September 12 2014):  
### Added
* New Feature: Longer (>9) expressions can now be saved  
* New Feature: TextToSpeech support for words added  
* New Feature: experimental google api (use 'ggl.php' instead of '\*http://translate.google.com' for google translate)  
* New Feature: new word select mode in read texts (hold down mouse button)

### Changed
* Database Changes: table textitems replaced by textitems2, temporary tables added, global table tts added  
* statistics.php, upload\_words.php rewritten  


## 1.5.17 (June 08 2014):  
### Added
* New Feature: Selecting terms according to a text tag  
* New Feature: Start a document where you left off (only "Read Text Screen")  
* New Feature: Improved Search/Query for Words/Texts  
* New Feature: Automatically import texts from RSS feeds (for more info see: [Newsfeed Import](#feed_imp))  
* New Setting: Button(s) for "words to do" "IGNORE ALL"/"I KNOW ALL"  
* New Setting: Theme  
* New Setting: term/word query with standard/regexp/regexp CS  
* New Sort option "Oldest First"  
* New option "Set Active Term(1-5) Sentences" in My Texts

### Changed
* JQuery, JQuery UI, JPlayer, jQuery.ScrollTo, Tag-it, Sorttable and Floating Menu updated  
* Database table optimization: first check, only optimize if (Overhead >10% of table and > 100KB) or (Overhead > 1,0MB)  
* Database table optimization: data types changed.  
* Documentation updated.

### Fixed
* Importing multiple words with the same tag causes an error  
* Can't select media in Archived Texts  
* Confirmation-popup when leaving via selectbutton in Settings/Preferences even if there are no changes (chrome-browser)  
* Bottom page select doesn't work in firefox (edit\_texts.php, edit\_words.php, edit\_archivedtexts.php, edit\_texttags.php, edit\_tags.php)  
* Setting a tag where tag already exists causes an error  
* New tag isn't saved in SESSION VAR (when adding a new text with a new tag / may cause an error when editing that text)  
      
    
## 1.5.17 (August 15 2014, this document updated Aug 17 2014 and Aug 24 2014):  
### Changed
* Documentation updated. Broken links corrected or deleted. From now on, only the installation with EasyPHP for Windows and MAMP for Mac OS X will be explained in detail and is recommended. Other local web server packages, like XAMPP, etc., are of course still possible, but it's beyond the scope of this document to explain all the details for every webserver package (it will also confuse most LWT users who are not familiar with web server packages and their setup).  
* Documentation updated on August 17 2014: Installation screencasts added.  
* Documentation updated on August 24 2014: Linux (Ubuntu, LinuxMint) installation hints and screencast added.  
### Fixed
* Minor bug in Utilities fixed.  
      
    
## 1.5.16 (February 19 2014):  
### Changed  
* Documentation updated.  
### Fixed
* Paging (via page select, and only at the bottom of a page) did not work correctly in all cases, has been corrected.
      
    
## 1.5.15 (December 17 2013):  
### Changed 
* Documentation updated.  
### Fixed
* Corrected wrong language code (French) within the language wizard definitions. 
      
    
## 1.5.14 (August 05 2013, this document updated Oct 30 2013):  
### Changed
* Documentation updated on October 30 2013.  
### Fixed
* Wrong text display in Print Screen corrected. Special handling of word breaks (if "Remove spaces" = Yes) removed.  
      
    
## 1.5.13 (July 22 2013):  
### Changed
* License texts updated according to text on [unlicense.org](http://unlicense.org/).  
* Documentation updated.  
### Fixed
* Removed an erroneous extra space in "wp\_logincheck.inc.php". Thanks to a poster in the help forum for pointing this out!  
* Minor bug fixes.  
      
    
## 1.5.12 (July 16 2013):  
### Added
* New Sort option for Terms/Expressions: "Word Count in Active Texts" (Descending). Only when you choose this sort option, the word count will be calculated, displayed, and used for sorting the table. This may slow down the term table display. If you prefer faster term table display, choose the other sort options.  
### Changed
* Much better Tablet/iPad® user experience in screens with several frames. There is a new setting "Frame Set Display Mode" where you can select how frame sets are displayed on different devices (default: "Auto"). If you prefer the old mode also on mobile devices, set this to "Force Non-Mobile".  
* Tagging and JQuery UI updated.  
* Better error message when Glosbe API call fails.  
* Unsaved changes alerts extended to tag changes.
### Fixed
* Tags cache updating corrected: when LWT table set has been changed or after restore/emptying tables.  
      
    
## 1.5.11 (July 12 2013):  
### Added
* If an improved annotated text exists, highlight the selected term translation in red in the text window popup and the text display frame (when using the keyboard).  
* New language settings wizard.  
### Changed
* Better check on duplicate language names.  
* Text window popup title is now a link (text color: yellow) to make editing an existent term a little easier.  
* Documentation updated.  
### Fixed
* Some minor bug fixes.  
      
    
## 1.5.10 (July 07 2013):
### Added
* New alerts in some forms when there are unsaved changes during unload event.

### Changed
* Improved database error checking and reporting, better error messages when fatal errors occur, both with traceback information.  
* Some improvements in Glosbe-LWT integration.
* Documentation updated.

### Fixed
* Default values corrected in new language form.
* SQL query optimizations in 1.5.8/1.5.9 caused problems, old versions restored.  
      
    
## 1.5.9 (July 03 2013):  
### Added
* Long Text Import: Importing a long text via file upload or from a text box, with splitting options.  
* Possibility to save the source URI with an active or archived text.  

### Change
* Documentation and some screenshots updated.

### Fixed
* Missing code in tag management stylesheet restored.  
      
    
## 1.5.8 (June 27 2013):  
### Changed
* Optimization of SQL queries for text and print display (reducing query time by up to thirty percent).  
* Checking database status and database upgrade program code rewritten.  
* Some minor improvements and bugfixes. Documentation updated.  
      
    
## 1.5.7 (June 25 2013):  
### Added
* A new 3rd "Flexible" term export is introduced, controlled by an "Export Template" in the language settings. [Read more ...](#extmpl)

### Changed
* Texts are now automatically reparsed, however only after changing language settings that influence the sentence and textitems cache.
* Documentation updated.  

### Fixed
* Some minor corrections in some SQL CREATE/INSERT statements. MySQL session string is now set to an empty string to avoid too strict SQL checking. Thanks to a poster in the help forum for pointing this out!
      
    
## 1.5.6 (June 22 2013):  
### Fixed
* Two SQL statements (Anki/TSV export of marked terms) corrected. Thanks to a poster in the help forum for pointing this out!  
      
    
## 1.5.5 (June 21 2013):
### Added
* Integration of the Glosbe API into LWT via a "special" dictionary link. Read more [here](#glosbe).
* LWT-WordPress integration, read more [here](#wp) (only for users who want to use WordPress authentication together with the LWT multiple user/table set feature introduced in version 1.5.3).

### Changed
* Some minor improvements: Window width of dictionary popups changed from 600 to 800 Pixel. No "\_lwtgeneral" operations if table prefix is fixed.
* Documentation updated.  
      
    
## 1.5.4 (June 19 2013):
### Added
* If more than one table set exists, and $tbpref was NOT set to a fixed value in "connect.inc.php", you can now select a table set via "start.php", or by clicking on the LWT icon or title in the LWT menu screen "index.php".  
* By hovering over the LWT icon in the top left corner of every screen, you can now display the current table set in a yellow tooltip.  
* A new test/review type "Table" has been added in the testing area. Words, translations, romanizations, sentences and status are presented in a table. You may hide and/or sort columns. After testing yourself, you can reveal the hidden information by clicking into the table cell, and change your status.

### Changed
* Player appearance improved. Some settings, that were not saved until now, are now automatically saved. Documentation and some screenshots updated.  
      
    
## 1.5.3 (June 14 2013):  
### Added
* New Feature: It is now possible to create and to use not only ONE set of LWT tables within one database. You are now able to create and use unlimited LWT table sets within one database (as space and MySQL limitations permit). This feature is especially useful for users who want to set up a multi user environment with a set of tables for each user. You can also create one table set for every language you study - this allows you to create different term/text tags for each language. If you don't need this feature, you just use LWT like in earlier versions with the "default table set". Read more [here](#mue) and [here](#database).  

### Changed
* Complete code review. Some minor improvements and bugfixes. Documentation & Anki 1+2 template decks updated.  
      
    
## 1.5.2 (June 09 2013):
### Added
* Easy navigation to the previous and the next text (according to current text filters and sort order) is now possible.

### Changed
* "Backup" does not store anymore the tables 'sentences' and 'textitems'. These tables are now automatically recreated (by reparsing the texts) within "Restore". This makes backup faster and the backup file much smaller, while "Restore" will take a bit longer.
* Documentation and all screenshots updated.  
* Installation procedures (EasyPHP) updated.  
      
    
## 1.5.1 (June 07 2013):
### Added
* Display screen of improved annotated texts (= [hyperliteral translations](http://learnanylanguage.wikia.com/wiki/Hyperliteral_translations) as [interlinear text](http://en.wikipedia.org/wiki/Interlinear_gloss)) improved: Clicking the "T" or "A" lightbulb icons hides/shows the complete text or all annotations. You may also click on a single term or a single annotation to show or to hide it. This enables you to test yourself or to concentrate on one text only. Romanizations, if available, appear now while hovering over a term.

### Changed
* Documentation and screenshots updated.  
      
    
## 1.5.0 (April 22 2013):
### Added
* New Feature: Create and edit an improved annotated text version (as [interlinear text](http://en.wikipedia.org/wiki/Interlinear_gloss)) for online or offline learning. Read more [here](#il).  
* In-Place-editing of translations and romanizations now possible within the terms table.
* You may now empty (= delete the contents of) the LWT database in the "Backup/Restore/Empty Database" screen.

### Changed
* Some minor improvements. Documentation, screenshots and demo database updated.  
      
    
## 1.4.10 (February 22 2013):
### Added
* New option in "Print" screen: annotation can now also be placed above the term (via [Ruby characters](http://en.wikipedia.org/wiki/Ruby_character)). This is especially helpful for Chinese and Japanese, when annotating the text with the romanization (Pinyin, Hiragana, Katakana). Your browser must support ruby markup. Firefox needs the [HTML Ruby Add-On](https://addons.mozilla.org/de/firefox/addon/html-ruby/) to display ruby markup properly.

### Changed
* Help document updated.  
      
    
## 1.4.9 (August 29 2012):
### Fixed
* Anki and TSV export bug fixed (in some cases the term tags were not exported).  
      
    
## 1.4.8 (May 11 2012, some external links updated June 19 2012):
### Added
* Timing of transactions now possible as an additional debugging option in settings.inc.php ($dspltime = 1; normally switched off = 0).

### Changed
* "Important Links" section within this document updated and expanded.  

### Fixed
* Correction of a small (typo) bug in js/jq\_pgm.js that caused an SQL error when pressing the "E" key (Thank you, anthonylauder!). See also [this thread](http://sourceforge.net/projects/lwt/forums/forum/1813497/topic/5265425).  
* Correction in do\_text\_header.php: fix negative audio positions to zero. See also [this thread](http://sourceforge.net/projects/lwt/forums/forum/1813497/topic/5220016).
      
    
## 1.4.7 (April 6 2012):
### Fixed
* Correction of some minor mistakes and glitches in the code.  
      
    
## 1.4.6 (March 14 2012):
### Fixed
* Documentation and example database corrected. Google Translate links now open in a popup window, not in a frame of the frameset, as Google now disallows this. Please make sure to deactivate popup window blockers.  
      
    
## 1.4.5 (October 01 2011, documentation updated Oct 13 2011, external links updated Dec 8 2011):
### Added
* Double-Click on a term and "A" key (while going through non-blue terms via keyboard) sets audio position approximately to text position. Hovering over sentence marker (green or red dot) in the terms table shows tooltip with sentence. Some updates and corrections in the documentation.  
      
    
## 1.4.4 (September 23 2011):
### Changed
* Changed the handling of backslash removal in posted data on servers with magic\_quotes\_gpc = Off. Documentation updated.  
      
    
## 1.4.3 (September 21 2011):
### Changed
* Changed some unintentionally written short PHP open tags "<?" to "<?php". Thanks to a poster in the help forum for pointing this out.  
      
    
## 1.4.2 (September 19 2011):
### Removed
* Removed PGUP/PGDN key bindings (mark first/last non-blue term, use HOME/END keys). Some minor changes in the documentation.  
      
    
## 1.4.1 (September 15 2011):
### Changed
* Anki Export changed. The full sentence doesn't have brackets anymore.  
* Data in Anki template updated.  
      
    
## 1.4.0 (September 09 2011):
### Added
* Mobile Version (experimental, via mobile.php): Selection of Language, Text, and Sentence, Playing the audio (if exists), Reading the text either sentence-by-sentence or term-by-term (saved words shown with translation, romanization, and status (via color). This mobile interface does not yet allow data manipulations.  
* Texts, archived texts and terms can now also be filtered by "untagged".  
* Added a "Repeat Audio / Single Play" toggle button for media player.  
      
    
## 1.3.1 (September 05 2011):
### Added
* New multi actions for marked/all terms: Set Terms to Lowercase, Capitalize Terms, Delete Sentences of Terms.

### Changed
* Screenshots updated.  
      
    
## 1.3.0 (September 03 2011):
### Added
* Tagging of texts and archived texts introduced. With this feature, it will be easier to categorize and organize your texts. After having tagged your texts, you are able to filter texts according to one or two tags.  
* Rudimentary right-to-left (rtl) script support: new db field in languages to set a language to right-to-left script, all relevant parts with respect to rtl support changed. A simple Hebrew example added to demonstrate rtl support.

### Changed
* Documentation, screenshots, Anki example file updated.  
      
    
## 1.2.2 (August 26 2011):
### Added
* Added column "Percentage Unknown Words" in Texts table.
* During reading a text, you can now create terms that do not occur in the text. Click on the yellow icon in the top left frame, and type in the term, translation, etc. You may now also edit the text directly.
* iPod touch®/iPhone®/iPad® icons and splash screen added, and HTML header for touch devices modified. (Thanks, Derek!)

### Changed
* Documentation and screenshots updated.  

### Fixed
* Removed a bug that prevented Strg-C/Cmd-C (and other key strokes) in text/test frames.  
* Removed a bug in statistics calculation.  
* Removed a bug in text selection dropdown control.
* Removed language column in Texts/Terms tables if language filter is set.
      
    
## 1.2.1 (August 25 2011):
### Added
* New Rewind and Fast Forward button for audio player. User can set the time to rewind or fast forward from 1 to 10 seconds.  
* Added a section in the help document about the setup of LWT for iPads®, etc.

### Changed
* Documentation and screenshots updated.  
      
    
## 1.2.0 (August 24 2011):  
* Tagging of terms introduced. Tags are little pieces of information (20 characters max., no spaces, no commas, case sensitive!) attached to terms to help you catagorize and organize your terms. You can import (CSV, TSV) and export (Anki, TSV) terms together with tag information. Two new tables save tag information, and they are created automatically during first usage after update.  
* Display of example sentences is now delayed.  
* Backup creates the database SQL file now as a gzipped file. Restore can process old unzipped or new gzipped files. Gzipped files are much smaller in size (10-20 % of original size).  
* Documentation and screenshots updated.  
      
    
## 1.1.1 (August 17 2011):
### Added
* New option in "Print" screen to select whether annotation should be placed in front (new option) or after (default) the term.  
* New option in settings screen "Visit only saved terms with status(es) ..." to specify which terms are visited when using RIGHT/SPACE/LEFT/etc. keys in the text frame (default: ALL non-blue terms = statuses 1..5, Ign, WKn).

### Changed
* The Status filter lists have been expanded.
* Documentation and screenshots updated.

### Fixed
* Fixed problems with non-ASCII characters in media file names (hopefully).
      
    
## 1.1.0 (August 16 2011):  
### Added
* New status display during tests: "Elapsed Time / Total = Not yet tested + Wrong + Correct", plus a small bar graph.  
* Tests can now be done also via key strokes (but you must first click in the test frame): SPACE: show solution, UP/DOWN: Status +1/-1, ESC: don't change status, NUMBER KEYS 1-5: set status to 1-5, I: set "Ignored", W: set "Well known", E: edit term.  
* In the "Read Text" frame, the next unknown (blue) word in the text can now be shown for term creation just by pressing the RETURN key. The term will be marked by a red border. You type in the translation, etc., and press RETURN to save the word. Now you can press RETURN again to show the next unknown (blue) word, enter a translation, save it, and so on... There is sometimes the problem that some external dictionaries catch the focus although the cursor should be in the translation field in the edit frame. Especially Chrome behaves badly, and I cannot change this. Please try different dictionaries and/or browsers.  
* You can also review/manage saved (non-blue) terms with key strokes in the "Read Text" frame (RIGHT or SPACE: next term, LEFT: previous term, PAGE-UP or HOME: first term, PAGE-DOWN or END: last term, NUMBER KEYS 1-5: set current term status to 1-5, I: set current term status to "Ignored", W: set current term status to "Well known", E: edit current term, ESC: reset). The current term has a black border and the frame scrolls automatically. The term is displayed in the top right frame.  
* New refresh button refreshes media files combo box (without page reload) on the text input/edit screen.  
* Information added how to install LWT at a (free) webhoster.

### Changed
* DB design altered: Table "words" changed: 3 new columns to make random word selection (in tests) and score calculation/query faster.  
* Translation, romanization and sentence are now optional. An empty translation or an asterisk in the translation field are equivalent. Terms without translation or in status "Well Known" or "Ignore" will never be tested. Import of terms without translation is now possible.  
* Textarea input boxes have now all a maximum text/bytes length check.  
* Testing algorithm revised, simplified and optimized.  
* Terms due today and tomorrow are marked in score column (red/yellow) in terms table.  
* Documentation and screenshots updated, new floating menu.

### Fixed
* EasyPHP installation corrected.  
      
    
## 1.0.4 (August 11 2011):
### Added
* Checking maximum text length in text input/edit/check screens.  

### Changed
* Code cleanup and optimization, better code documentation.
* Media directory is not anymore included, user has to create it if needed. The demo media are now all online.  
* Demo database installation is now done within LWT (optional). If the database is empty, a hint on the main screen is displayed either to install the demo db or to start with the definition of a language. Backup/Restore now with more hints and an option to install the demo database.  
* Documentation and screenshots updated.  
      
    
## 1.0.3 (August 09 2011):
### Added
* New button to open a new text immediately after saving.  
* Dictionaries can now be opened not only within the frame set (default) but also in a popup window (please deactivate popup window blocking in your browser!). To open a dictionary in a separate popup window, put an asterisk \* in front of the Uniform Resource Identifier (Example: \*http://mywebdict.com?q=###). Please use this method if a web dictionary does not open properly within the frame set.  
* The application now always checks if the current text or language still exist (could have sometimes resulted in empty query results after deleting).  
* New settings "Texts per Page", "Show Word Counts of Texts immediately", "Archived Texts per Page" and "Terms per Page". You can now define how many texts or terms are on one page. As the calculation of text word counts can slow down the loading of a long text table, you can switch off this calculation (and do the calculation later).  
    "New/Edit Term" now allow dictionary lookup (always in popup window). If you want to enter a term manually, you must first select the language on the "My Terms" page.

### Changed
* Documentation and screenshots updated.  
      
    
## 1.0.2 (August 05 2011):
### Added
* Language definition: If the searchword in the Uniform Resource Identifiers (URIs) needs to be converted into a different encoding (standard is UTF-8), you can now use _###encoding###_ as a placeholder. Example: _http://mywebdict.com?q=###ISO-8859-15###_. A list of encodings can be found [here](http://php.net/manual/en/mbstring.supported-encodings.php) (omit the asterisk if one is at the end).

### Changed
* Documentation and screenshots updated. Thai example added in demo database.  
      
    
## 1.0.1 (August 04 2011):
### Added
* Empty database will be created automatically if database does not exist. Tables will be automatically recreated if some or all tables are missing. System handles (future) database changes automatically.  

### Changed
* Testing: Last term tested will not show up immediately. Automatically selected sentences in tests now must not contain unsaved (blue) words. This does not apply if a sentence saved with a term contains unknown words - such sentences may come up within a test.
* Documentation and screenshots updated.  
      
    
## 1.0.0 (August 01 2011):  
* First stable release. For some time, there won't be any new releases. I hope you'll understand that. Please post all problems, questions, and (hopefully not too many) bugs [here](http://sourceforge.net/projects/lwt/forums/forum/1813497), and ideas and suggestions for new features [here](http://lwt.uservoice.com). Thanks!  
      
    
## 0.9.8 (July 31 2011):
### Added
* New Text Display Mode (see new checkbox on the "Read text" screen).  
  * \[Show All\] = ON (the only mode in previous releases): ALL terms are shown, and all multi-word terms are shown as superscripts before the first word. The superscript indicates the number of words in the multi-word term.  
  * \[Show All\] = OFF (new): Multi-word terms now hide single words and shorter or overlapping multi-word terms. This makes it easier to concentrate on multi-word terms while displaying them without superscripts, but creation and deletion of multi-word terms can be a bit slow in long texts.

### Changed 
* Documentation and screenshots updated.  
      
    
## 0.9.7 (July 28 2011):
### Changed
* Internal improvements, status names, abbreviations, and tooltips (Thanks, Arthaey!). Printout of texts with optional inline annotation (translation and/or romanization) of terms that are of specified status(es). Documentation and screenshots corrected.  
      
    
## 0.9.6 (July 26 2011):
### Changed
* More visual improvements (layout, tables, etc.), many improvements and bugfixes. Dictionary Uniform Resource Identifiers (URIs) use now ### as a placeholder for the searchword. If ### is missing, the searchword will be appended (like in older versions). More multi-actions for terms. Translation and sentence fields do NOT accept newlines anymore, now the form is submitted. Status entry changed from dropdown to radio buttons. Backup/Restore improved. CSS and XHTML corrected and validated. Korean example added. An Anki example deck is now provided: "LWT.anki" in directory "anki". Documentation and screenshots improved.  
      
    
## 0.9.5 (July 23 2011):
### Changed
* Visual improvements (icons), a few bug fixes. Database Restore changed for bigger files. Two new tests to test L2 -> L1 or L1 -> L2 without sentence (just the term). The term filter operates not only on the term field but also on the romanization and translation field.  
      
    
## 0.9.4 (July 22 2011):
### Added
* New buttons to switch easier between active texts and archived texts.  
* New settings "Testing: Number of sentences displayed from text, if available" and "Terms: Number of sentences generated from text, if available". Default is "Just ONE". The options "TWO (+ previous)" and "THREE (+ previous, + next)" now allow you to do MCD (Massive-Context Cloze Deletion) testing, as proposed by Khatzumoto @ AJATT.  

### Changed
* "Import of terms" now only needs a translation if the status is set to 1, 2, 3, 4 or 5. Furthermore it not only imports terms as TAB (ASCII 9) separated values (TSV) or "#" separated values, but also as comma separated values (CSV, strings in quotes ("...", if needed), a quote within a string as double quotes (""); this is the format that LingQ uses for exporting terms).
* Some minor bug fixes. Documentation updated.  
      
    
## 0.9.3 (July 21 2011):
### Fixed
* After a lot of multi-platform/multi-server testing: several bug fixes and improvements. One severe bug that crashed importing and checking of texts on XAMPP/Win removed (Thanks, Kendall!). Within text, archive, and term queries, selected sort order will be retained (saved in database). All other query settings are now retained per session. Documentation updated.  
      
    
## 0.9.2 (July 19 2011):
### Fixed
* New "Settings" screen. Documentation updated and improved.  
      
    
## 0.9.1 (July 18 2011):
### Changed
* Testing totally revamped (and extended). Many, many improvements and bugfixes. Documentation and screenshots updated. EasyPHP for Windows installation explained.

### Fixed
* Many, many improvements and bugfixes.
      
    
## 0.9.0 (July 14 2011):
### Added
* TSV export of terms added.
* New buttons to jump from test to text and vice versa.
* Questions and answers added

### Changed
* Frame screen "Edit term" is now more compact to save space. 
* Anki export improved. 
* The multi actions drop-down-list is now disabled when no checkboxes are checked.
* Testing completely revised and improved. 
* It is now possible to create expressions with up to 9 words (previous releases: up to 6 words). 
   * Important: Re-parsing is necessary to use this feature in existing texts. 
   * New texts have this feature will automatically. 
* Documentation and screenshots updated.  
      
    
## 0.8.2 (July 13 2011):
### Changed
* Minor improvements.
* Term score formula revised.
* Clicking on a blue (unknown) word opens "New Term" and first dictionary automatically.
* Documentation (topic: Term scores) added.

### Fixed
* Minor bug fixes.
      
    
## 0.8.1 (July 12 2011):
### Changed
* Mac installation and upgrade (see documentation) completely changed.  
      
    
## 0.8.0 (July 11 2011):
### Added
* Possibility to change term (but only uppercase/lowercase changes allowed). 
* Import terms with overwrite now possible.
* Number of saved words and "To Do" words are now displayed in "My Texts". 
* Backup and Restore of the LWT database.

### Changed
* "Learn/Edit Word/Expression" opens first dictionary automatically. 
* Documentation updated and improved.  
      
    
## 0.7.0 (July 09 2011):
### Changed
* Minor improvements, more documentation.
* Japanese example added.  
      
    
## 0.6.0 (July 08 2011):
### Changed
* Improvements, more documentation.
* Settings now stored in DB (not in cookies anymore).  

### Fixed
* Bugfixes.
      
    
## 0.5.0 (June 30 2011): 
### Added
* Text archiving.

### Changed
* Improvements.

### Fixed
* Bugfixes.
      
    
## 0.4.0 (June 29 2011):  
### Added
* Basic documentation completed
* Two new tests.

### Changed
* Improvements.

### Fixed
* Bugfixes.
      
    
## 0.3.0 (June 27 2011):  
### Added
* More documentation.

### Changed
* Improvements.

### Fixed
* Bugfixes.
      
    
## 0.2.0 (June 25 2011):  
### Added
* More documentation.
### Changed
* Improvements.

### Fixed
* Bugfixes.
      
    
## 0.1.0 (June 24 2011):
* Initial release.