<?php

require_once(dirname(__FILE__).'/package.php');

$source_path = dirname(__FILE__).'/../../docs/source/';
$destination_path = dirname(__FILE__).'/../../docs/simpletest.org/';

$languages = array("en/", "fr/", "../../");

foreach ($languages as $language) {
    $dir = opendir($source_path.$language);

    while (($file = readdir($dir)) !== false) {
	    if (is_file($source_path.$language.$file) and preg_match("/\.xml$/", $file)) {
	        $source = simplexml_load_file($source_path.$language.$file, "SimpleTestXMLElement");
	        $destination = $source->destination(dirname(__FILE__).'/map.xml');

			if (!empty($destination)) {
				$page = file_get_contents(dirname(__FILE__).'/template.html');

				$page = str_replace('KEYWORDS', $source->keywords(), $page);
				$page = str_replace('TITLE', $source->title(), $page);
				$page = str_replace('CONTENT', $source->content(), $page);
				$page = str_replace('INTERNAL', $source->internal(), $page);
				$page = str_replace('EXTERNAL', $source->external(), $page);
				
				$links = $source->links(dirname(__FILE__).'/map.xml');
				foreach ($links as $category => $link) {
					$page = str_replace("LINKS_".strtoupper($category), $link, $page);
				}
				
				$destination_dir = dirname($destination_path.$destination);
				if (!is_dir($destination_dir)) {
					mkdir($destination_dir);
				}

				$ok = file_put_contents($destination_path.$destination, $page);
				touch($destination_path.$destination, filemtime($source_path.$language.$file));

				if ($ok) {
					$result = "OK";
				} else {
					$result = "KO";
				}

				$synchronisation = new PackagingSynchronisation($source_path.$language.$file);
				$result .= " ".$synchronisation->result();

				echo $destination_path.$destination." : ".$result."\n";
			}
	    }
	}
	closedir($dir);
}
