# Learning with Texts

**Learning with Texts** (LWT) is a tool for language learning by reading. It is a self-hosted web application.

**THIS IS A THIRD PARTY VERSION**. This version is not the 
official one, and brings many improvements and new featuress. 
It is quicker, has smaller databse size, 
and is open for contributions. The official version is on 
[source forge](https://sourceforge.net/projects/learning-with-texts)

[@gustavklopp's LingL](https://github.com/gustavklopp/LingL) is a wonderful alternative written in Python.

## Installation
Please see [docs/info.html](https://hugofara.github.io/lwt/docs/info.html) for detailed instructions. 
As it is self-hosted, you will need a server, which can be your computer.

* [Install Composer](https://getcomposer.org/download/) if you don't have it.
* **If you installed composer globally**
  * Run ``composer install hugofara/lwt``.
* **If you installed composer locally**
  * Got to the folder where composer.phar is located
  * Run ``php composer.phar install hugofara/lwt``
* Create ``connect.inc.php`` with an existing database user 
(read [docs/info.html](https://hugofara.github.io/lwt/docs/info.html)) for the tutorial.

If the reading page displays a database error, 
try downgrading to PHP version 5.4.45.

And you are ready to go!

## Description
LWT is a language learning web application. To learn a language, you 
need to practice, and we guide you in reading exercices.

First copy/paste any text you want to read. It can be raw text or an RSS feed.
![Adding French text](https://github.com/HugoFara/lwt/raw/master/img/05.jpg)

Then, we parse the text. Unkown words will be displayed with different colors,
just click them to see it in a dictionary.
![Learning French text](https://github.com/HugoFara/lwt/raw/master/img/06.jpg)

Read as much as you want! 

To make sure you memorize new words, you can take review exercises.
![Reviewing French word](https://github.com/HugoFara/lwt/raw/master/img/07.jpg)

The difference with popular remembering software like 
[Anki](https://apps.ankiweb.net/) is that we keep track of the 
context to help you. By the way, we also ship 
an Anki exporter.

## Features
* Support for many languages
* Text parsing for roman languages, right-to-left,
and East-Asian ideographic systems
* Translate words on-the-fly
* Add an audio track and read it online
* Practice words you don't remember
* Statistics to record your progress

### Features not in the official LWT
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

### Improvements compared to the official LWT
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

## Contribute
To contribute, you need to clone or fork this repository.
Run ``git clone https://github.com/HugoFara/lwt``

Next, got to the lwt folder and use ``composer install --dev``.

In short:
```bash
git clone https://github.com/HugoFara/lwt
cd lwt
composer install --dev
```

## Branches
* The stable branch is *master*. Last commit on this branch is 
considered to be bug-free. 
* The *dev* branch is for unstable versions.
* The *official* branch is for the official LWT Releases.
Any other branch if considered under development.

## Useful links
* General documentation at [docs/info.html](https://hugofara.github.io/lwt/docs/info.html).
* Please find more help at [docs/index.html](https://hugofara.github.io/lwt/docs/index.html).
* You can also contact the community by GitHub.

## Unlicense
Under unlicense, view [UNLICENSE.md](UNLICENSE.md), please refer to [http://unlicense.org/].

**Let's learn new languages!**
