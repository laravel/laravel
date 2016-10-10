<?php

$transform = "onpk.xslt";
$source_path = "../../docs/source/fr/";
$destination_path = "../../docs/onpk/";

$dir = opendir($source_path);
while (($file = readdir($dir)) !== false) {
	if (! preg_match('/\.xml$/', $file)) {
		continue;
	}
	$source = $source_path.$file;
	$destination = $destination_path.preg_replace('/\.xml$/', '.php', basename($source));

	$xsltProcessor = xslt_create();
	$fileBase = 'file://'.getcwd().'/';
	xslt_set_base($xsltProcessor, $fileBase);
	$result = xslt_process ($xsltProcessor, $source, $transform);

	if ( $result ) {
		$handle = fopen($destination, "w+");
		fwrite($handle, $result);
		fclose($handle);
		echo "succès pour ".$destination."<br />";
	} else {
	   echo "erreur pour ".$destination." : ".xslt_error($xh)."<br />";
	}

	xslt_free($xsltProcessor);
}
closedir($dir);
?>