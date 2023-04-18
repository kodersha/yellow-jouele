<?php
// Jouele extension, https://github.com/kodersha/yellow-plugin-jouele

class YellowJouele {
    const VERSION = "0.8.22";
    public $yellow;         // access to API

    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("joueleStyle", "");
    }
    
    // Handle page content of shortcut
    public function onParseContentShortcut($page, $name, $text, $type) {
        $output = null;
        if ($name=="jouele" && ($type=="block" || $type=="inline")) {
            list($pattern, $style) = $this->yellow->toolbox->getTextArguments($text);
            if (is_string_empty($pattern)) {
                $pattern = "unknown";
                $files = $this->yellow->media->clean();
            } else {
                $joueles = $this->yellow->system->get("coreDownloadLocation");
                $files = $this->yellow->media->index(true, true)->match("#$joueles$pattern#");
            }
            if (!is_array_empty($files)) {
                $page->setLastModified($files->getModified());
                $style = $this->yellow->system->get("joueleStyle");
                $output = '<p class="jouele-playlist '.htmlspecialchars($style).'">';
                    foreach ($files as $file) {
                        $tags = jouele_get_tags($file->fileName);
                        $output .= '<a href="'.htmlspecialchars($file->getLocation()).'" class="jouele" data-length="'.$tags['formatted_time'].'" data-space-control="true">'.$tags["artist"].' - '.$tags["title"].'</a>';
                    }
                $output .= '</p>';
            } else {
                $page->error(500, "Jouele '$pattern' does not exist!");
            }
        }
        return $output;
    }

    // Handle page extra data
    public function onParsePageExtra($page, $name) {
        $output = null;
        if ($name=="header") {
            $extensionLocation = $this->yellow->system->get("coreServerBase").$this->yellow->system->get("coreExtensionLocation");
            $output = "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"{$extensionLocation}jouele.css\" />\n";
            $output .= "<script type=\"text/javascript\" defer=\"defer\" src=\"{$extensionLocation}jouele.js\"></script>\n";
        }
        return $output;
    }
}

function jouele_get_tags($file) {
	$id3_tags = array();
	$versions = array("00" => "2.5", "01" => "x", "10" => "2", "11" => "1");
	$layers = array("00" => "x", "01" => "3", "10" => "2", "11" => "1");
	$bitrates = array(
		'V1L1'=>array(0,32,64,96,128,160,192,224,256,288,320,352,384,416,448),
        'V1L2'=>array(0,32,48,56, 64, 80, 96,112,128,160,192,224,256,320,384),
        'V1L3'=>array(0,32,40,48, 56, 64, 80, 96,112,128,160,192,224,256,320),
        'V2L1'=>array(0,32,48,56, 64, 80, 96,112,128,144,160,176,192,224,256),
        'V2L2'=>array(0, 8,16,24, 32, 40, 48, 56, 64, 80, 96,112,128,144,160),
        'V2L3'=>array(0, 8,16,24, 32, 40, 48, 56, 64, 80, 96,112,128,144,160),
        );  
    $sample_rates = array(
			'1'   => array(44100,48000,32000),
            '2'   => array(22050,24000,16000),
            '2.5' => array(11025,12000, 8000),
        );
	
	$handle = fopen($file, "r+");
	 
	if (!$handle) return; {
		$tags_array = array('TIT2' => 'title', 'TALB' => 'album', 'TPE1' => 'artist', 'TYER' => 'year', 'COMM' => 'comment', 'TCON' => 'genre', 'TLEN' => 'length', 'TT2' => 'title', 'TAL' => 'album', 'TP1' => 'artist', 'TYE' => 'year', 'COM' => 'comment', 'TCO' => 'genre', 'TLE' => 'length');
		$null = chr(0);
		$data = fread($handle, 10);
		if (substr($data,0,3) == 'ID3') {
			$id3_major_version = hexdec(bin2hex(substr($data,3,1)));
			if ($id3_major_version >= 3)
				$tags_array = array('TIT2' => 'title', 'TALB' => 'album', 'TPE1' => 'artist', 'TYER' => 'year', 'COMM' => 'comment', 'TCON' => 'genre', 'TLEN' => 'length');
			else 
				$tags_array = array('TT2' => 'title', 'TAL' => 'album', 'TP1' => 'artist', 'TYE' => 'year', 'COM' => 'comment', 'TCO' => 'genre', 'TLE' => 'length');			
			$id3_tags["id3_tag_version"] = "2.".$id3_major_version;
			$id3_revision = hexdec(bin2hex(substr($data,4,1)));
			$id3_flags = decbin(ord(substr($data,5,1)));
			$id3_flags = str_pad($id3_flags, 8, 0, STR_PAD_LEFT);
			$footer_flag = $id3_flags[3];
			$mb_size = ord(substr($data,6,1));
			$kb_size = ord(substr($data,7,1));
			$byte128_size = ord(substr($data,8,1));
			$byte_size = ord(substr($data,9,1));
			$total_size = ($mb_size * 2097152) + ($kb_size * 16384) + ($byte128_size * 128) + $byte_size;
			$data .= stream_get_contents($handle, $total_size + ($footer_flag * 10));
			foreach ($tags_array as $key => $value) {
				if ($id3_major_version == 3 || $id3_major_version == 4)
					$tag_header_length = 10; 
				else
					$tag_header_length = 6; 
				if ($tag_pos = strpos($data, $key.$null)) {
					$tag_abbr = trim(substr($data, $tag_pos, 4));
					$content_length = hexdec(bin2hex(substr($data, $tag_pos + ($tag_header_length/2),3)));
					$content = trim(substr($data, $tag_pos + $tag_header_length, $content_length));
					$tag_content = "";
					for ($i = 0; $i < strlen($content); $i++)
						if($content[$i] >= " " && $content[$i] <= "~") $tag_content .= $content[$i];
					$id3_tags[$value] = $tag_content;
				}
			}
			if ($id3_major_version != 2)
				$data = "";
		}
		$bits = null;
		while (!feof($handle)) {
			$data .= stream_get_contents($handle, 10);
			for ($i = 0; $i < strlen($data); $i++)
				$bits .= str_pad(decbin(ord($data[$i])), 8, 0, STR_PAD_LEFT);
			$frame_pos = strpos($bits, "11111111111");
			if ($frame_pos !== false) {
				$id3_tags["version"] = $versions[substr($bits, $frame_pos + 11, 2)];
				$id3_tags["layer"] = $layers[substr($bits, $frame_pos + 13, 2)];
				$id3_tags["crc"] = substr($bits, $frame_pos + 15, 1);
				$bitrate_index = bindec(substr($bits, $frame_pos + 16, 4));
				$id3_tags["bitrate"] = $bitrates["V".$id3_tags["version"][0]."L".$id3_tags["layer"]][$bitrate_index];
				$id3_tags["frequency"] = $sample_rates["1"][bindec(substr($bits, $frame_pos + 19, 2))];
				if (preg_match("/^(https?|ftp):\/\//", $file))
					$id3_tags["filesize"] = get_headers($file,1)['Content-Length'];
				else
					$id3_tags["filesize"] = filesize($file);
				$bps = ($id3_tags["bitrate"]*1000)/8;
        		$id3_tags["duration"] = round(($id3_tags["filesize"] - $total_size) / $bps);
				$id3_tags["formatted_time"] = gmdate("i:s", $id3_tags["duration"]);
				break;
			}
		}	
	}
	
	if (!isset($id3_major_version)) {
		$id3_tags["id3_tag_version"] = 1;
		while (!feof($handle)) {
			$data .= stream_get_contents($handle, 128);
		}
		$data = substr($data, -128);
		if(substr($data, 0, 3) == "TAG") {
			$id3_tags["title"] = trim(substr($data, 3, 30));
			$id3_tags["artist"] = trim(substr($data, 33, 30));
			$id3_tags["album"] = trim(substr($data, 63, 30));
			$id3_tags["year"] = trim(substr($data, 93, 4));
			$id3_tags["comment"] = trim(substr($data, 97, 30));
			$id3_tags["genre"] = ord(trim(substr($data, 127, 1)));
		}
	}
	fclose($handle);
	return($id3_tags);
}

function jouele_get_genre_name($genre_id) {
	$genre_names = array("Blues", "Classic Rock", "Country", "Dance", "Disco", "Funk", "Grunge", "Hip-Hop", "Jazz", "Metal", "New Age", "Oldies", "Other", "Pop", "R&B", "Rap", "Reggae", "Rock", "Techno", "Industrial", "Alternative", "Ska", "Death Metal", "Pranks", "Soundtrack", "Euro-Techno", "Ambient", "Trip-Hop", "Vocal", "Jazz+Funk", "Fusion", "Trance", "Classical", "Instrumental", "Acid", "House", "Game", "Sound Clip", "Gospel", "Noise", "Alt. Rock", "Bass", "Soul", "Punk", "Space", "Meditative", "Instrumental Pop", "Instrumental Rock", "Ethnic", "Gothic", "Darkwave", "Techno-Industrial", "Electronic", "Pop-Folk", "Eurodance", "Dream", "Southern Rock", "Comedy", "Cult", "Gangsta Rap", "Top 40", "Christian Rap", "Pop/Funk", "Jungle", "Native American", "Cabaret", "New Wave", "Psychedelic", "Rave", "Showtunes", "Trailer", "Lo-Fi", "Tribal", "Acid Punk", "Acid Jazz", "Polka", "Retro", "Musical", "Rock & Roll", "Hard Rock", "Folk", "Folk-Rock", "National Folk", "Swing", "Fast-Fusion", "Bebop", "Latin", "Revival", "Celtic", "Bluegrass", "Avantgarde", "Gothic Rock", "Progressive Rock", "Psychedelic Rock", "Symphonic Rock", "Slow Rock", "Big Band", "Chorus", "Easy Listening", "Acoustic", "Humour", "Speech", "Chanson", "Opera", "Chamber Music", "Sonata", "Symphony", "Booty Bass", "Primus", "Porn Groove", "Satire", "Slow Jam", "Club", "Tango", "Samba", "Folklore", "Ballad", "Power Ballad", "Rhythmic Soul", "Freestyle", "Duet", "Punk Rock", "Drum Solo", "A Cappella", "Euro-House", "Dance Hall", "Goa", "Drum & Bass", "Club-House", "Hardcore", "Terror", "Indie", "BritPop", "Afro-Punk", "Polsk Punk", "Beat", "Christian Gangsta Rap", "Heavy Metal", "Black Metal", "Crossover", "Contemporary Christian", "Christian Rock", "Merengue", "Salsa", "Thrash Metal", "Anime", "JPop", "Synthpop", "Abstract", "Art Rock", "Baroque", "Bhangra", "Big Beat", "Breakbeat", "Chillout", "Downtempo", "Dub", "EBM", "Eclectic", "Electro", "Electroclash", "Emo", "Experimental", "Garage", "Global", "IDM", "Illbient", "Industro-Goth", "Jam Band", "Krautrock", "Leftfield", "Lounge", "Math Rock", "New Romantic", "Nu-Breakz", "Post-Punk", "Post-Rock", "Psytrance", "Shoegaze", "Space Rock", "Trop Rock", "World Music", "Neoclassical", "Jouelebook", "Jouele Theatre", "Neue Deutsche Welle", "Podcast", "Indie Rock", "G-Funk", "Dubstep", "Garage Rock", "Psybient");
	$genres = explode(")", $genre_id);
	$n = 1;
	foreach($genres as $genre_num) { 
			$genre_num = str_replace("(", "", str_replace(")", "", $genre_num));
			if ($n > 1 && !empty($genre_num))
				$genre_string .= ",";
			if (is_numeric($genre_num))
			{
				if ($genre_num >= 0 && $genre_num <= 191)
					$genre_string .= $genre_names[$genre_num];
				else
					$genre_string .= "None";
			}
			else
				$genre_string .= $genre_num;	
		$n++;
	}
	return($genre_string);
}