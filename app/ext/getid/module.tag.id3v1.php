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
// module.tag.id3v1.php                                        //
// module for analyzing ID3v1 tags                             //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_id3v1 extends getid3_handler
{

	public function Analyze() {
		$info = &$this->getid3->info;

		if (!getid3_lib::intValueSupported($info['filesize'])) {
			$info['warning'][] = 'Unable to check for ID3v1 because file is larger than '.round(PHP_INT_MAX / 1073741824).'GB';
			return false;
		}

		$this->fseek(-256, SEEK_END);
		$preid3v1 = $this->fread(128);
		$id3v1tag = $this->fread(128);

		if (substr($id3v1tag, 0, 3) == 'TAG') {

			$info['avdataend'] = $info['filesize'] - 128;

			$ParsedID3v1['title']   = $this->cutfield(substr($id3v1tag,   3, 30));
			$ParsedID3v1['artist']  = $this->cutfield(substr($id3v1tag,  33, 30));
			$ParsedID3v1['album']   = $this->cutfield(substr($id3v1tag,  63, 30));
			$ParsedID3v1['year']    = $this->cutfield(substr($id3v1tag,  93,  4));
			$ParsedID3v1['comment'] =                 substr($id3v1tag,  97, 30);  // can't remove nulls yet, track detection depends on them
			$ParsedID3v1['genreid'] =             ord(substr($id3v1tag, 127,  1));

			// If second-last byte of comment field is null and last byte of comment field is non-null
			// then this is ID3v1.1 and the comment field is 28 bytes long and the 30th byte is the track number
			if (($id3v1tag{125} === "\x00") && ($id3v1tag{126} !== "\x00")) {
				$ParsedID3v1['track']   = ord(substr($ParsedID3v1['comment'], 29,  1));
				$ParsedID3v1['comment'] =     substr($ParsedID3v1['comment'],  0, 28);
			}
			$ParsedID3v1['comment'] = $this->cutfield($ParsedID3v1['comment']);

			$ParsedID3v1['genre'] = $this->LookupGenreName($ParsedID3v1['genreid']);
			if (!empty($ParsedID3v1['genre'])) {
				unset($ParsedID3v1['genreid']);
			}
			if (isset($ParsedID3v1['genre']) && (empty($ParsedID3v1['genre']) || ($ParsedID3v1['genre'] == 'Unknown'))) {
				unset($ParsedID3v1['genre']);
			}

			foreach ($ParsedID3v1 as $key => $value) {
				$ParsedID3v1['comments'][$key][0] = $value;
			}

			// ID3v1 data is supposed to be padded with NULL characters, but some taggers pad with spaces
			$GoodFormatID3v1tag = $this->GenerateID3v1Tag(
											$ParsedID3v1['title'],
											$ParsedID3v1['artist'],
											$ParsedID3v1['album'],
											$ParsedID3v1['year'],
											(isset($ParsedID3v1['genre']) ? $this->LookupGenreID($ParsedID3v1['genre']) : false),
											$ParsedID3v1['comment'],
											(!empty($ParsedID3v1['track']) ? $ParsedID3v1['track'] : ''));
			$ParsedID3v1['padding_valid'] = true;
			if ($id3v1tag !== $GoodFormatID3v1tag) {
				$ParsedID3v1['padding_valid'] = false;
				$info['warning'][] = 'Some ID3v1 fields do not use NULL characters for padding';
			}

			$ParsedID3v1['tag_offset_end']   = $info['filesize'];
			$ParsedID3v1['tag_offset_start'] = $ParsedID3v1['tag_offset_end'] - 128;

			$info['id3v1'] = $ParsedID3v1;
		}

		if (substr($preid3v1, 0, 3) == 'TAG') {
			// The way iTunes handles tags is, well, brain-damaged.
			// It completely ignores v1 if ID3v2 is present.
			// This goes as far as adding a new v1 tag *even if there already is one*

			// A suspected double-ID3v1 tag has been detected, but it could be that
			// the "TAG" identifier is a legitimate part of an APE or Lyrics3 tag
			if (substr($preid3v1, 96, 8) == 'APETAGEX') {
				// an APE tag footer was found before the last ID3v1, assume false "TAG" synch
			} elseif (substr($preid3v1, 119, 6) == 'LYRICS') {
				// a Lyrics3 tag footer was found before the last ID3v1, assume false "TAG" synch
			} else {
				// APE and Lyrics3 footers not found - assume double ID3v1
				$info['warning'][] = 'Duplicate ID3v1 tag detected - this has been known to happen with iTunes';
				$info['avdataend'] -= 128;
			}
		}

		return true;
	}

	public static function cutfield($str) {
		return trim(substr($str, 0, strcspn($str, "\x00")));
	}

	public static function ArrayOfGenres($allowSCMPXextended=false) {
		static $GenreLookup = array(
			0    => 'Blues',
			1    => 'Classic Rock',
			2    => 'Country',
			3    => 'Dance',
			4    => 'Disco',
			5    => 'Funk',
			6    => 'Grunge',
			7    => 'Hip-Hop',
			8    => 'Jazz',
			9    => 'Metal',
			10   => 'New Age',
			11   => 'Oldies',
			12   => 'Other',
			13   => 'Pop',
			14   => 'R&B',
			15   => 'Rap',
			16   => 'Reggae',
			17   => 'Rock',
			18   => 'Techno',
			19   => 'Industrial',
			20   => 'Alternative',
			21   => 'Ska',
			22   => 'Death Metal',
			23   => 'Pranks',
			24   => 'Soundtrack',
			25   => 'Euro-Techno',
			26   => 'Ambient',
			27   => 'Trip-Hop',
			28   => 'Vocal',
			29   => 'Jazz+Funk',
			30   => 'Fusion',
			31   => 'Trance',
			32   => 'Classical',
			33   => 'Instrumental',
			34   => 'Acid',
			35   => 'House',
			36   => 'Game',
			37   => 'Sound Clip',
			38   => 'Gospel',
			39   => 'Noise',
			40   => 'Alt. Rock',
			41   => 'Bass',
			42   => 'Soul',
			43   => 'Punk',
			44   => 'Space',
			45   => 'Meditative',
			46   => 'Instrumental Pop',
			47   => 'Instrumental Rock',
			48   => 'Ethnic',
			49   => 'Gothic',
			50   => 'Darkwave',
			51   => 'Techno-Industrial',
			52   => 'Electronic',
			53   => 'Pop-Folk',
			54   => 'Eurodance',
			55   => 'Dream',
			56   => 'Southern Rock',
			57   => 'Comedy',
			58   => 'Cult',
			59   => 'Gangsta Rap',
			60   => 'Top 40',
			61   => 'Christian Rap',
			62   => 'Pop/Funk',
			63   => 'Jungle',
			64   => 'Native American',
			65   => 'Cabaret',
			66   => 'New Wave',
			67   => 'Psychedelic',
			68   => 'Rave',
			69   => 'Showtunes',
			70   => 'Trailer',
			71   => 'Lo-Fi',
			72   => 'Tribal',
			73   => 'Acid Punk',
			74   => 'Acid Jazz',
			75   => 'Polka',
			76   => 'Retro',
			77   => 'Musical',
			78   => 'Rock & Roll',
			79   => 'Hard Rock',
			80   => 'Folk',
			81   => 'Folk/Rock',
			82   => 'National Folk',
			83   => 'Swing',
			84   => 'Fast-Fusion',
			85   => 'Bebob',
			86   => 'Latin',
			87   => 'Revival',
			88   => 'Celtic',
			89   => 'Bluegrass',
			90   => 'Avantgarde',
			91   => 'Gothic Rock',
			92   => 'Progressive Rock',
			93   => 'Psychedelic Rock',
			94   => 'Symphonic Rock',
			95   => 'Slow Rock',
			96   => 'Big Band',
			97   => 'Chorus',
			98   => 'Easy Listening',
			99   => 'Acoustic',
			100  => 'Humour',
			101  => 'Speech',
			102  => 'Chanson',
			103  => 'Opera',
			104  => 'Chamber Music',
			105  => 'Sonata',
			106  => 'Symphony',
			107  => 'Booty Bass',
			108  => 'Primus',
			109  => 'Porn Groove',
			110  => 'Satire',
			111  => 'Slow Jam',
			112  => 'Club',
			113  => 'Tango',
			114  => 'Samba',
			115  => 'Folklore',
			116  => 'Ballad',
			117  => 'Power Ballad',
			118  => 'Rhythmic Soul',
			119  => 'Freestyle',
			120  => 'Duet',
			121  => 'Punk Rock',
			122  => 'Drum Solo',
			123  => 'A Cappella',
			124  => 'Euro-House',
			125  => 'Dance Hall',
			126  => 'Goa',
			127  => 'Drum & Bass',
			128  => 'Club-House',
			129  => 'Hardcore',
			130  => 'Terror',
			131  => 'Indie',
			132  => 'BritPop',
			133  => 'Negerpunk',
			134  => 'Polsk Punk',
			135  => 'Beat',
			136  => 'Christian Gangsta Rap',
			137  => 'Heavy Metal',
			138  => 'Black Metal',
			139  => 'Crossover',
			140  => 'Contemporary Christian',
			141  => 'Christian Rock',
			142  => 'Merengue',
			143  => 'Salsa',
			144  => 'Thrash Metal',
			145  => 'Anime',
			146  => 'JPop',
			147  => 'Synthpop',

			255  => 'Unknown',

			'CR' => 'Cover',
			'RX' => 'Remix'
		);

		static $GenreLookupSCMPX = array();
		if ($allowSCMPXextended && empty($GenreLookupSCMPX)) {
			$GenreLookupSCMPX = $GenreLookup;
			// http://www.geocities.co.jp/SiliconValley-Oakland/3664/alittle.html#GenreExtended
			// Extended ID3v1 genres invented by SCMPX
			// Note that 255 "Japanese Anime" conflicts with standard "Unknown"
			$GenreLookupSCMPX[240] = 'Sacred';
			$GenreLookupSCMPX[241] = 'Northern Europe';
			$GenreLookupSCMPX[242] = 'Irish & Scottish';
			$GenreLookupSCMPX[243] = 'Scotland';
			$GenreLookupSCMPX[244] = 'Ethnic Europe';
			$GenreLookupSCMPX[245] = 'Enka';
			$GenreLookupSCMPX[246] = 'Children\'s Song';
			$GenreLookupSCMPX[247] = 'Japanese Sky';
			$GenreLookupSCMPX[248] = 'Japanese Heavy Rock';
			$GenreLookupSCMPX[249] = 'Japanese Doom Rock';
			$GenreLookupSCMPX[250] = 'Japanese J-POP';
			$GenreLookupSCMPX[251] = 'Japanese Seiyu';
			$GenreLookupSCMPX[252] = 'Japanese Ambient Techno';
			$GenreLookupSCMPX[253] = 'Japanese Moemoe';
			$GenreLookupSCMPX[254] = 'Japanese Tokusatsu';
			//$GenreLookupSCMPX[255] = 'Japanese Anime';
		}

		return ($allowSCMPXextended ? $GenreLookupSCMPX : $GenreLookup);
	}

	public static function LookupGenreName($genreid, $allowSCMPXextended=true) {
		switch ($genreid) {
			case 'RX':
			case 'CR':
				break;
			default:
				if (!is_numeric($genreid)) {
					return false;
				}
				$genreid = intval($genreid); // to handle 3 or '3' or '03'
				break;
		}
		$GenreLookup = self::ArrayOfGenres($allowSCMPXextended);
		return (isset($GenreLookup[$genreid]) ? $GenreLookup[$genreid] : false);
	}

	public static function LookupGenreID($genre, $allowSCMPXextended=false) {
		$GenreLookup = self::ArrayOfGenres($allowSCMPXextended);
		$LowerCaseNoSpaceSearchTerm = strtolower(str_replace(' ', '', $genre));
		foreach ($GenreLookup as $key => $value) {
			if (strtolower(str_replace(' ', '', $value)) == $LowerCaseNoSpaceSearchTerm) {
				return $key;
			}
		}
		return false;
	}

	public static function StandardiseID3v1GenreName($OriginalGenre) {
		if (($GenreID = self::LookupGenreID($OriginalGenre)) !== false) {
			return self::LookupGenreName($GenreID);
		}
		return $OriginalGenre;
	}

	public static function GenerateID3v1Tag($title, $artist, $album, $year, $genreid, $comment, $track='') {
		$ID3v1Tag  = 'TAG';
		$ID3v1Tag .= str_pad(trim(substr($title,  0, 30)), 30, "\x00", STR_PAD_RIGHT);
		$ID3v1Tag .= str_pad(trim(substr($artist, 0, 30)), 30, "\x00", STR_PAD_RIGHT);
		$ID3v1Tag .= str_pad(trim(substr($album,  0, 30)), 30, "\x00", STR_PAD_RIGHT);
		$ID3v1Tag .= str_pad(trim(substr($year,   0,  4)),  4, "\x00", STR_PAD_LEFT);
		if (!empty($track) && ($track > 0) && ($track <= 255)) {
			$ID3v1Tag .= str_pad(trim(substr($comment, 0, 28)), 28, "\x00", STR_PAD_RIGHT);
			$ID3v1Tag .= "\x00";
			if (gettype($track) == 'string') {
				$track = (int) $track;
			}
			$ID3v1Tag .= chr($track);
		} else {
			$ID3v1Tag .= str_pad(trim(substr($comment, 0, 30)), 30, "\x00", STR_PAD_RIGHT);
		}
		if (($genreid < 0) || ($genreid > 147)) {
			$genreid = 255; // 'unknown' genre
		}
		switch (gettype($genreid)) {
			case 'string':
			case 'integer':
				$ID3v1Tag .= chr(intval($genreid));
				break;
			default:
				$ID3v1Tag .= chr(255); // 'unknown' genre
				break;
		}

		return $ID3v1Tag;
	}

}
