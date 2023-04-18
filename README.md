Jouele plugin
=============
Jouele for Yellow CMS - Is a simple and beautiful audio player for Yellow CMS.

![Jouele for Yellow CMS](https://raw.githubusercontent.com/kodersha/yellow-jouele/master/jouele-screenshot.png)

Install
-------
1. Download and install [Yellow](https://github.com/datenstrom/yellow/).
2. Download [this plugin](https://github.com/kodersha/yellow-jouele/archive/master.zip), unpack he into your `system` folder.

To uninstall delete all plugin files.

Required
--------
[jQuery](https://jquery.com) library

How use?
--------
Make a `[jouele]` shortcut. The following arguments available:

- `PATTERN` - file name as regular expression
- `STYLE` - player style

Example
-------
Your can use:

    [jouele simple-song.mp3 jouele-skin-dark]
    [jouele simple-song.mp3]
    [jouele playlist.*mp3]

Files on your `media/downloads` folder:

    simple-song.mp3
    playlist.song-one-name.mp3
    playlist.song-two-name.mp3
    playlist.song-three-name.mp3

Configuration
-------------
Edit Yellow `config.ini` for custom settings:

- `JoueleStyle: jouele-skin-dark` - custom skin class for Jouele

Used
-------
* **[Jouele](https://ilyabirman.net/projects/jouele/)** - Audio player by *Ilya Birman*.
* **[mp3_get_tags](http://www.seabreezecomputers.com/tips/mp3_id3_tag.htm)** - Read the ID3 tag from MP3 files by *Jeff Baker*.