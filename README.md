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
- `STYLE` - your custom player style. 

Examples
--------
Single use:

```
[jouele track.mp3]
```

Pattern use:

```
[jouele track-.*.mp3]
```

More examples
-------
Your can use:

    [jouele single---track.mp3]
    [jouele artist-name-.*.mp3]

Files on your `media/downloads` folder:

    single---track.mp3
    artist-name---track-one.mp3
    artist-name---track-two.mp3
    artist-name---track-three.mp3

Configuration
-------------
Edit Yellow `config.ini` for custom settings:

- `JoueleStyle: jouele-skin-dark` - custom skin class for Jouele.

Known Issues
------------
Cyrillic ID3 tags and track names unsupported. :(

Used
-------
* **[Jouele](https://ilyabirman.net/projects/jouele/)** - Audio player by *Ilya Birman*.
