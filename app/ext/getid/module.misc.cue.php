<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <info@getid3.org>               //
//  available at http://getid3.sourceforge.net                 //
//            or http://www.getid3.org                         //
//          also https://github.com/JamesHeinrich/getID3       //
/////////////////////////////////////////////////////////////////
// See readme.txt for more details                             //
/////////////////////////////////////////////////////////////////
//                                                             //
// module.misc.cue.php                                         //
// module for analyzing CUEsheet files                         //
// dependencies: NONE                                          //
//                                                             //
/////////////////////////////////////////////////////////////////
//                                                             //
// Module originally written [2009-Mar-25] by                  //
//      Nigel Barnes <ngbarnesØhotmail*com>                    //
// Minor reformatting and similar small changes to integrate   //
//   into getID3 by James Heinrich <info@getid3.org>           //
//                                                            ///
/////////////////////////////////////////////////////////////////

/*
 * CueSheet parser by Nigel Barnes.
 *
 * This is a PHP conversion of CueSharp 0.5 by Wyatt O'Day (wyday.com/cuesharp)
 */

/**
 * A CueSheet class used to open and parse cuesheets.
 *
 */
class getid3_cue extends getid3_handler
{
	public $cuesheet = array();

	public function Analyze() {
		$info = &$this->getid3->info;

		$info['fileformat'] = 'cue';
		$this->readCueSheetFilename($info['filenamepath']);
		$info['cue'] = $this->cuesheet;
		return true;
	}



	public function readCueSheetFilename($filename)
	{
		$filedata = file_get_contents($filename);
		return $this->readCueSheet($filedata);
	}
	/**
	* Parses a cue sheet file.
	*
	* @param string $filename - The filename for the cue sheet to open.
	*/
	public function readCueSheet(&$filedata)
	{
		$cue_lines = array();
		foreach (explode("\n", str_replace("\r", null, $filedata)) as $line)
		{
			if ( (strlen($line) > 0) && ($line[0] != '#'))
			{
				$cue_lines[] = trim($line);
			}
		}
		$this->parseCueSheet($cue_lines);

		return $this->cuesheet;
	}

	/**
	* Parses the cue sheet array.
	*
	* @param array $file - The cuesheet as an array of each line.
	*/
	public function parseCueSheet($file)
	{
		//-1 means still global, all others are track specific
		$track_on = -1;

		for ($i=0; $i < count($file); $i++)
		{
			list($key) = explode(' ', strtolower($file[$i]), 2);
			switch ($key)
			{
				case 'catalog':
				case 'cdtextfile':
				case 'isrc':
				case 'performer':
				case 'songwriter':
				case 'title':
					$this->parseString($file[$i], $track_on);
					break;
				case 'file':
					$currentFile = $this->parseFile($file[$i]);
					break;
				case 'flags':
					$this->parseFlags($file[$i], $track_on);
					break;
				case 'index':
				case 'postgap':
				case 'pregap':
					$this->parseIndex($file[$i], $track_on);
					break;
				case 'rem':
					$this->parseComment($file[$i], $track_on);
					break;
				case 'track':
					$track_on++;
					$this->parseTrack($file[$i], $track_on);
					if (isset($currentFile)) // if there's a file
					{
						$this->cuesheet['tracks'][$track_on]['datafile'] = $currentFile;
					}
					break;
				default:
					//save discarded junk and place string[] with track it was found in
					$this->parseGarbage($file[$i], $track_on);
					break;
			}
		}
	}

	/**
	* Parses the REM command.
	*
	* @param string $line - The line in the cue file that contains the TRACK command.
	* @param integer $track_on - The track currently processing.
	*/
	public function parseComment($line, $track_on)
	{
		$explodedline = explode(' ', $line, 3);
		$comment_REM  = (isset($explodedline[0]) ? $explodedline[0] : '');
		$comment_type = (isset($explodedline[1]) ? $explodedline[1] : '');
		$comment_data = (isset($explodedline[2]) ? $explodedline[2] : '');
		if (($comment_REM == 'REM') && $comment_type) {
			$comment_type  = strtolower($comment_type);
			$commment_data = trim($comment_data, ' "');
			if ($track_on != -1) {
				$this->cuesheet['tracks'][$track_on]['comments'][$comment_type][] = $comment_data;
			} else {
				$this->cuesheet['comments'][$comment_type][] = $comment_data;
			}
		}
	}

	/**
	* Parses the FILE command.
	*
	* @param string $line - The line in the cue file that contains the FILE command.
	* @return array - Array of FILENAME and TYPE of file..
	*/
	public function parseFile($line)
	{
		$line =            substr($line, strpos($line, ' ') + 1);
		$type = strtolower(substr($line, strrpos($line, ' ')));

		//remove type
		$line = substr($line, 0, strrpos($line, ' ') - 1);

		//if quotes around it, remove them.
		$line = trim($line, '"');

		return array('filename'=>$line, 'type'=>$type);
	}

	/**
	* Parses the FLAG command.
	*
	* @param string $line - The line in the cue file that contains the TRACK command.
	* @param integer $track_on - The track currently processing.
	*/
	public function parseFlags($line, $track_on)
	{
		if ($track_on != -1)
		{
			foreach (explode(' ', strtolower($line)) as $type)
			{
				switch ($type)
				{
					case 'flags':
						// first entry in this line
						$this->cuesheet['tracks'][$track_on]['flags'] = array(
							'4ch'  => false,
							'data' => false,
							'dcp'  => false,
							'pre'  => false,
							'scms' => false,
						);
						break;
					case 'data':
					case 'dcp':
					case '4ch':
					case 'pre':
					case 'scms':
						$this->cuesheet['tracks'][$track_on]['flags'][$type] = true;
						break;
					default:
						break;
				}
			}
		}
	}

	/**
	* Collect any unidentified data.
	*
	* @param string $line - The line in the cue file that contains the TRACK command.
	* @param integer $track_on - The track currently processing.
	*/
	public function parseGarbage($line, $track_on)
	{
		if ( strlen($line) > 0 )
		{
			if ($track_on == -1)
			{
				$this->cuesheet['garbage'][] = $line;
			}
			else
			{
				$this->cuesheet['tracks'][$track_on]['garbage'][] = $line;
			}
		}
	}

	/**
	* Parses the INDEX command of a TRACK.
	*
	* @param string $line - The line in the cue file that contains the TRACK command.
	* @param integer $track_on - The track currently processing.
	*/
	public function parseIndex($line, $track_on)
	{
		$type = strtolower(substr($line, 0, strpos($line, ' ')));
		$line =            substr($line, strpos($line, ' ') + 1);

		if ($type == 'index')
		{
			//read the index number
			$number = intval(substr($line, 0, strpos($line, ' ')));
			$line   =        substr($line, strpos($line, ' ') + 1);
		}

		//extract the minutes, seconds, and frames
		$explodedline = explode(':', $line);
		$minutes = (isset($explodedline[0]) ? $explodedline[0] : '');
		$seconds = (isset($explodedline[1]) ? $explodedline[1] : '');
		$frames  = (isset($explodedline[2]) ? $explodedline[2] : '');

		switch ($type) {
			case 'index':
				$this->cuesheet['tracks'][$track_on][$type][$number] = array('minutes'=>intval($minutes), 'seconds'=>intval($seconds), 'frames'=>intval($frames));
				break;
			case 'pregap':
			case 'postgap':
				$this->cuesheet['tracks'][$track_on][$type]          = array('minutes'=>intval($minutes), 'seconds'=>intval($seconds), 'frames'=>intval($frames));
				break;
		}
	}

	public function parseString($line, $track_on)
	{
		$category = strtolower(substr($line, 0, strpos($line, ' ')));
		$line     =            substr($line, strpos($line, ' ') + 1);

		//get rid of the quotes
		$line = trim($line, '"');

		switch ($category)
		{
			case 'catalog':
			case 'cdtextfile':
			case 'isrc':
			case 'performer':
			case 'songwriter':
			case 'title':
				if ($track_on == -1)
				{
					$this->cuesheet[$category] = $line;
				}
				else
				{
					$this->cuesheet['tracks'][$track_on][$category] = $line;
				}
				break;
			default:
				break;
		}
	}

	/**
	* Parses the TRACK command.
	*
	* @param string $line - The line in the cue file that contains the TRACK command.
	* @param integer $track_on - The track currently processing.
	*/
	public function parseTrack($line, $track_on)
	{
		$line = substr($line, strpos($line, ' ') + 1);
		$track = ltrim(substr($line, 0, strpos($line, ' ')), '0');

		//find the data type.
		$datatype = strtolower(substr($line, strpos($line, ' ') + 1));

		$this->cuesheet['tracks'][$track_on] = array('track_number'=>$track, 'datatype'=>$datatype);
	}

}

