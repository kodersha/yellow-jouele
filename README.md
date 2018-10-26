# Jouele plugin
Jouele for Yellow CMS - Is a simple and beautiful audio player for Yellow CMS.

## Install
1. Download and install [Yellow](https://github.com/datenstrom/yellow/).
2. Download [this plugin](https://github.com/sashatravkina/yellow-plugin-jouele/archive/master.zip), unpack he into your system/plugins folder.
To uninstall delete all plugin file.

## Required
[jQuery](https://jquery.com) library

## How use?
Create a `[jouele]` shortcut.

The following arguments available:

`PATTERN` - file name as regular expression
`STYLE` - player style

## Example

`[jouele simple-song.mp3]`
`[jouele playlist.*mp3]`

**Files**:
```
playlist5.song-one-name.mp3
playlist5.song-two-name.mp3
playlist5.song-three-name.mp3
```

## Configuration
Edit your `config.ini` for custom settings:

`joueleStyle: jouele-skin-dark` - custom skin for jouele
`JoueleDir: media/audio` - path to your audio folder

## Used
* **[Jouele](https://ilyabirman.net/projects/jouele/)** - Audio player by *Ilya Birman*.
* **[mp3_get_tags](http://www.seabreezecomputers.com/tips/mp3_id3_tag.htm)** - Read the ID3 tag from MP3 files by *Jeff Baker*.
