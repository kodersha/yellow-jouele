<?php

class YellowJouele {
    const VERSION = "0.8.22";
    public $yellow; // доступ к API

    // Обработать инициализацию
    public function onLoad($yellow) {
        $this->yellow = $yellow;
		$this->yellow->system->setDefault("JoueleStyle", "");
    }

    // Обработка содержимого страницы по сокращенной записи
    public function onParseContentShortcut($page, $name, $text, $type) {
		$output = null;
		if ($name=="jouele" && ($type=="inline" || $type=="block")) {
			[$pattern, $style] = $this->yellow->toolbox->getTextArguments($text);
		
			if (empty($pattern)) {
				$pattern = "unknown";
				$files = $this->yellow->media->clean();
			} else {
				$joueles = $this->yellow->system->get("coreDownloadLocation");
				$files = $this->yellow->media->index(true, true)->match("#$joueles$pattern#");
			}
		
			if (!empty($files)) {
				$page->setLastModified($files->getModified());
				$joueleStyle = $this->yellow->system->get("joueleStyle");
				
				$playlistClass = count($files) > 1 ? 'jouele-playlist' : '';
				
				$output = '';
				foreach ($files as $file) {
					// Получить теги ID3 из аудиофайла
					$id3 = $this->getID3($file->fileName);
				
					// Разбиваем название на исполнителя и название трека на основе "---"
					$titleParts = explode('---', pathinfo($file->getLocation(), PATHINFO_FILENAME));
					$artist = trim($titleParts[0]);
					$title = trim($titleParts[1]);
				
					// Заменяем тире между словами на пробелы в названии трека и имени исполнителя
					$patterns = array('/(?<=\w)-(?=\w)/u', '/-{2,}/');
					$replacements = array(' ', ' ');
				
					$artist = preg_replace($patterns, $replacements, $artist);
					$title = preg_replace($patterns, $replacements, $title);
				
					$output .= '<a href="'.htmlspecialchars($file->getLocation()).'" class="jouele '.htmlspecialchars($joueleStyle).'" data-length="" data-space-control="true">';
				
					// Если тег заголовка и артиста не пустой, выводим артиста и заголовок, иначе выводим название файла
					if (!empty($id3['artist']) && !empty($id3['title'])) {
						$output .= htmlspecialchars($id3['artist']).' - '.htmlspecialchars($id3['title']);
					} else {
						$output .= mb_convert_case(htmlspecialchars($artist), MB_CASE_TITLE, "UTF-8") . ' - ' . mb_convert_case(htmlspecialchars($title), MB_CASE_TITLE, "UTF-8");
					}
				
					$output .= '</a>';
				}
				if (!empty($output)) {
					$output = '<div class="jouele-player '.$playlistClass.' '.htmlspecialchars($style).'">' . $output . '</div>';
				}
			} else {
				$page->error(500, "Jouele '$pattern' does not exist!");
			}
		}
		return $output;		
    }

    // Получение тегов ID3 из аудиофайла
    public function getID3($url) {
        $tags = array();
        if ($file = fopen($url, "r")) {
            fseek($file, -128, SEEK_END);
            if (fread($file, 3) == "TAG") {
				$titleRaw = trim(fread($file, 30));
				$artistRaw = trim(fread($file, 30));
                fclose($file);
                $encoding = mb_detect_encoding($titleRaw.$artistRaw, "UTF-8, ISO-8859-1");
				$tags['title'] = mb_convert_encoding($titleRaw, "UTF-8", $encoding);
				$tags['artist'] = mb_convert_encoding($artistRaw, "UTF-8", $encoding);
                return $tags;
            }
            fclose($file);
        }
        return $tags;
    }

    // Обработка дополнительных данных страницы
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