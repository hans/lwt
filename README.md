# Learning with Texts

> [Learning with Texts](http://lwt.sf.net) (LWT) is a tool for Language Learning.

This is @PirtleShell's fork of [@andreask7's fork](https://github.com/andreask7/lwt). Its altered database structure makes it quicker, and it has many features not found in the original. It also looks more likely to develop communally, whereas the original is fairly stagnant and not open for contributions.

**THIS IS A THIRD PARTY VERSION**
IT DIFFERS IN MANY RESPECTS FROM THE OFFICIAL LWT-VERSION

## New in this Version (not available in the OFFICIAL LWT)

* Database improvements (db size is much smaller now)
* Automatically import texts from RSS feeds
* Support for different themes
* Longer (>9) expressions can now be saved (up to 250 characters)
* Display translations of terms with status(es) in the reading frame
* Save text/audio position in the reading frame
* Multiwords selection (click and hold on a word -> move to another word -> release mouse button)
* Key bindings work when you hover over a word
* Bulk translate new words in the reading frame
* Google api (use 'ggl.php' instead of '*http://translate.google.com' for Google Translate)
* Text to speech support (only words)
* Optional "ignore all" button in read texts
* New key bindings in the reading frame: T (translate sentence), P (pronounce term), G (edit term with Google Translate)
* Ability to change audio playback speed (doesn't work when using the flash plugin)
* Improved Search/Query for Words/Texts
* Selecting terms according to a text tag
* Term import with more options (i.e.: combine translations, multiple tag import)
* Two database backup modes (new or old structure)


# Install instructions
## Run in [Docker](https://docs.docker.com/get-docker/)
This repository contains docker-compose file to accomodate running LWT in a docker container.
#### To use it, change into project root folder and run

	docker-compose -f docker/docker-compose.yml up -d
	
#### By default the server can be accessed on port 8010
http://localhost:8010

To remove the created containers run

	docker-compose -f docker/docker-compose.yml down
	
---
## Original README from LWT

PLEASE READ MORE ...
Either open ... info.htm (within the distribution)
or     open ... http://lwt.sf.net (official LWT)

"Learning with Texts" (LWT) is free and unencumbered software
released into the PUBLIC DOMAIN.

Anyone is free to copy, modify, publish, use, compile, sell, or
distribute this software, either in source code form or as a
compiled binary, for any purpose, commercial or non-commercial,
and by any means.

In jurisdictions that recognize copyright laws, the author or
authors of this software dedicate any and all copyright
interest in the software to the public domain. We make this
dedication for the benefit of the public at large and to the
detriment of our heirs and successors. We intend this
dedication to be an overt act of relinquishment in perpetuity
of all present and future rights to this software under
copyright law.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE
AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS BE LIABLE
FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

For more information, please refer to [http://unlicense.org/].
