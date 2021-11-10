# Learning with Texts

[Learning with Texts](https://sourceforge.net/projects/learning-with-texts)
 (LWT) is a tool for language learning by reading.

**THIS IS A THIRD PARTY VERSION**
This version is not the official one, and brings many improvements 
and new featuress. It is quicker, has smaller databse size, 
and is open for contributions.

If the reading page displays a database error, 
try downgrading to PHP version 5.4.45.

[@gustavklopp's LingL](https://github.com/gustavklopp/LingL) is a wonderful alternative written in Python.

## Installation
Please see [docs/info.html](docs/info.html) for detailed instructions. You will need a server, 
which can be your computer.
1. Clone or fork this repo on your server.
2. Edit ``connect.inc.php`` with an existing databse user.
3. Run ``make install``.

## Description
LWT is a language learning web application. To learn a language, you 
need to practice, and we guide you in reading exercices.

First copy/paste any text you want to read. It can be raw text or an RSS feed.
![Adding French text](https://github.com/HugoFara/lwt/raw/master/img/05.jpg)

Then, we parse the text. Unkown words will be displayed with different colors,
just click them to see it in a dictionary.
![Learning French text](https://github.com/HugoFara/lwt/raw/master/img/06.jpg)

Next, you can take review exercises to memorize new words.
![Reviewing French word](https://github.com/HugoFara/lwt/raw/master/img/07.jpg)

The difference with popular remembering software like Anki is that we keep 
track of the context to help you. By the way, we also ship an Anki exporter.


## New in this Version (not available in the OFFICIAL LWT)

### New features
* Automatically import texts from RSS feeds
* Support for different themes
* Display translations of terms with status(es) in the reading frame
* Multiwords selection (click and hold on a word 
→ move to another word → release mouse button)
* Key bindings work when you hover over a word
* Bulk translate new words in the reading frame
* Text to speech support (only words)
* Optional "ignore all" button in read texts
* New key bindings in the reading frame: 
  * T (translate sentence), 
  * P (pronounce term), 
  * G (edit term with Google Translate)
* Selecting terms according to a text tag
* Two database backup modes (new or old structure)
* Support for text to speech with no provided audio.

### Improvements
* Database improvements (db size is much smaller now)
* Longer (>9) expressions can now be saved (up to 250 characters)
* Save text/audio position in the reading frame* Google api 
(use 'ggl.php' instead of '*http://translate.google.com' for Google Translate)
* Improved Search/Query for Words/Texts
* Term import with more options (i.e.: combine translations, multiple tag import)
* Support for MeCab for Japanese word-by-word automatic translation.
* You can include video files from popular video platforms.
* Code documentation.
* Code is well organised, making debugging and contribution easier.
* Server caching improved.

## Branches
* The stable branch is *master*. Last commit on this branch is 
considered to be bug-free. 
* The *dev* branch is for unstable versions.
* The *official* branch is for the official LWT Releases.
Any other branch if considered under development.

## Useful links
* General documentation at [docs/info.html](docs/info.html).
* Please find more help at [docs/index.html](docs/index.html).
* You can also contact the community by GitHub.

## Unlicense
Under unlicense, view [UNLICENSE.md](UNLICENSE.md), please refer to [http://unlicense.org/].

**Let's learn new languages!**
