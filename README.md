Jouele extension
================
Jouele for [Yellow CMS](https://github.com/datenstrom/yellow) - Is a simple and beautiful audio player for Yellow CMS.

![Jouele for Yellow CMS](https://raw.githubusercontent.com/kodersha/yellow-jouele/master/jouele-screenshot.png)

Install
-------
Download [this extension](https://github.com/kodersha/yellow-jouele/archive/master.zip), put `zip` archive into your `system` folder.

To uninstall delete all plugin files.

Required
--------
[jQuery](https://jquery.com) library

How use?
--------
Make a `[jouele]` shortcut. The following arguments available:

- `FILENAME` or files `PATTERN`.
- `STYLE` - player style or `jouele-playlist` class for autoplay next tracks, if pattern list used. 
- `NAME` - rename one track, **if pattern unused**.

Examples
--------
Single use:

```
[jouele track.mp3 "class" "Artist - Track"]
```

Pattern use:

```
[jouele track-.*.mp3 "class"]
```

Renamed track with autoplay next track playlist:

```
! {.jouele-playlist}
!
! [jouele track-1.mp3 "" "Artist - Track"]
!
! [jouele track-2.mp3 "" "Artist - Track"]
```

More examples
-------
Your can use:

    [jouele simple-song.mp3]
    [jouele playlist.*.mp3 "jouele-playlist"]

Files on your `media/downloads` folder:

    simple-song.mp3
    playlist.song-one-name.mp3
    playlist.song-two-name.mp3
    playlist.song-three-name.mp3

Configuration
-------------
Edit Yellow `config.ini` for custom settings:

- `JoueleStyle: jouele-skin-dark` - custom skin class for Jouele

Known Issues
------------
Cyrillic track ID3 names unsupported. :(

Used
-------
* **[Jouele](https://ilyabirman.net/projects/jouele/)** - Audio player by *Ilya Birman*.
* **[mp3_get_tags](http://www.seabreezecomputers.com/tips/mp3_id3_tag.htm)** - Read the ID3 tag from MP3 files by *Jeff Baker*.
