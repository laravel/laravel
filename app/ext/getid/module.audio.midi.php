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
// module.audio.midi.php                                       //
// module for Midi Audio files                                 //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////

define('GETID3_MIDI_MAGIC_MTHD', 'MThd'); // MIDI file header magic
define('GETID3_MIDI_MAGIC_MTRK', 'MTrk'); // MIDI track header magic

class getid3_midi extends getid3_handler
{
	public $scanwholefile = true;

	public function Analyze() {
		$info = &$this->getid3->info;

		// shortcut
		$info['midi']['raw'] = array();
		$thisfile_midi               = &$info['midi'];
		$thisfile_midi_raw           = &$thisfile_midi['raw'];

		$info['fileformat']          = 'midi';
		$info['audio']['dataformat'] = 'midi';

		$this->fseek($info['avdataoffset']);
		$MIDIdata = $this->fread($this->getid3->fread_buffer_size());
		$offset = 0;
		$MIDIheaderID = substr($MIDIdata, $offset, 4); // 'MThd'
		if ($MIDIheaderID != GETID3_MIDI_MAGIC_MTHD) {
			$info['error'][] = 'Expecting "'.getid3_lib::PrintHexBytes(GETID3_MIDI_MAGIC_MTHD).'" at offset '.$info['avdataoffset'].', found "'.getid3_lib::PrintHexBytes($MIDIheaderID).'"';
			unset($info['fileformat']);
			return false;
		}
		$offset += 4;
		$thisfile_midi_raw['headersize']    = getid3_lib::BigEndian2Int(substr($MIDIdata, $offset, 4));
		$offset += 4;
		$thisfile_midi_raw['fileformat']    = getid3_lib::BigEndian2Int(substr($MIDIdata, $offset, 2));
		$offset += 2;
		$thisfile_midi_raw['tracks']        = getid3_lib::BigEndian2Int(substr($MIDIdata, $offset, 2));
		$offset += 2;
		$thisfile_midi_raw['ticksperqnote'] = getid3_lib::BigEndian2Int(substr($MIDIdata, $offset, 2));
		$offset += 2;

		for ($i = 0; $i < $thisfile_midi_raw['tracks']; $i++) {
			while ((strlen($MIDIdata) - $offset) < 8) {
				if ($buffer = $this->fread($this->getid3->fread_buffer_size())) {
					$MIDIdata .= $buffer;
				} else {
					$info['warning'][] = 'only processed '.($i - 1).' of '.$thisfile_midi_raw['tracks'].' tracks';
					$info['error'][] = 'Unabled to read more file data at '.$this->ftell().' (trying to seek to : '.$offset.'), was expecting at least 8 more bytes';
					return false;
				}
			}
			$trackID = substr($MIDIdata, $offset, 4);
			$offset += 4;
			if ($trackID == GETID3_MIDI_MAGIC_MTRK) {
				$tracksize = getid3_lib::BigEndian2Int(substr($MIDIdata, $offset, 4));
				$offset += 4;
				//$thisfile_midi['tracks'][$i]['size'] = $tracksize;
				$trackdataarray[$i] = substr($MIDIdata, $offset, $tracksize);
				$offset += $tracksize;
			} else {
				$info['error'][] = 'Expecting "'.getid3_lib::PrintHexBytes(GETID3_MIDI_MAGIC_MTRK).'" at '.($offset - 4).', found "'.getid3_lib::PrintHexBytes($trackID).'" instead';
				return false;
			}
		}

		if (!isset($trackdataarray) || !is_array($trackdataarray)) {
			$info['error'][] = 'Cannot find MIDI track information';
			unset($thisfile_midi);
			unset($info['fileformat']);
			return false;
		}

		if ($this->scanwholefile) { // this can take quite a long time, so have the option to bypass it if speed is very important
			$thisfile_midi['totalticks']      = 0;
			$info['playtime_seconds'] = 0;
			$CurrentMicroSecondsPerBeat       = 500000; // 120 beats per minute;  60,000,000 microseconds per minute -> 500,000 microseconds per beat
			$CurrentBeatsPerMinute            = 120;    // 120 beats per minute;  60,000,000 microseconds per minute -> 500,000 microseconds per beat
			$MicroSecondsPerQuarterNoteAfter  = array ();

			foreach ($trackdataarray as $tracknumber => $trackdata) {

				$eventsoffset               = 0;
				$LastIssuedMIDIcommand      = 0;
				$LastIssuedMIDIchannel      = 0;
				$CumulativeDeltaTime        = 0;
				$TicksAtCurrentBPM = 0;
				while ($eventsoffset < strlen($trackdata)) {
					$eventid = 0;
					if (isset($MIDIevents[$tracknumber]) && is_array($MIDIevents[$tracknumber])) {
						$eventid = count($MIDIevents[$tracknumber]);
					}
					$deltatime = 0;
					for ($i = 0; $i < 4; $i++) {
						$deltatimebyte = ord(substr($trackdata, $eventsoffset++, 1));
						$deltatime = ($deltatime << 7) + ($deltatimebyte & 0x7F);
						if ($deltatimebyte & 0x80) {
							// another byte follows
						} else {
							break;
						}
					}
					$CumulativeDeltaTime += $deltatime;
					$TicksAtCurrentBPM   += $deltatime;
					$MIDIevents[$tracknumber][$eventid]['deltatime'] = $deltatime;
					$MIDI_event_channel                                  = ord(substr($trackdata, $eventsoffset++, 1));
					if ($MIDI_event_channel & 0x80) {
						// OK, normal event - MIDI command has MSB set
						$LastIssuedMIDIcommand = $MIDI_event_channel >> 4;
						$LastIssuedMIDIchannel = $MIDI_event_channel & 0x0F;
					} else {
						// running event - assume last command
						$eventsoffset--;
					}
					$MIDIevents[$tracknumber][$eventid]['eventid']   = $LastIssuedMIDIcommand;
					$MIDIevents[$tracknumber][$eventid]['channel']   = $LastIssuedMIDIchannel;
					if ($MIDIevents[$tracknumber][$eventid]['eventid'] == 0x08) { // Note off (key is released)

						$notenumber = ord(substr($trackdata, $eventsoffset++, 1));
						$velocity   = ord(substr($trackdata, $eventsoffset++, 1));

					} elseif ($MIDIevents[$tracknumber][$eventid]['eventid'] == 0x09) { // Note on (key is pressed)

						$notenumber = ord(substr($trackdata, $eventsoffset++, 1));
						$velocity   = ord(substr($trackdata, $eventsoffset++, 1));

					} elseif ($MIDIevents[$tracknumber][$eventid]['eventid'] == 0x0A) { // Key after-touch

						$notenumber = ord(substr($trackdata, $eventsoffset++, 1));
						$velocity   = ord(substr($trackdata, $eventsoffset++, 1));

					} elseif ($MIDIevents[$tracknumber][$eventid]['eventid'] == 0x0B) { // Control Change

						$controllernum = ord(substr($trackdata, $eventsoffset++, 1));
						$newvalue      = ord(substr($trackdata, $eventsoffset++, 1));

					} elseif ($MIDIevents[$tracknumber][$eventid]['eventid'] == 0x0C) { // Program (patch) change

						$newprogramnum = ord(substr($trackdata, $eventsoffset++, 1));

						$thisfile_midi_raw['track'][$tracknumber]['instrumentid'] = $newprogramnum;
						if ($tracknumber == 10) {
							$thisfile_midi_raw['track'][$tracknumber]['instrument'] = $this->GeneralMIDIpercussionLookup($newprogramnum);
						} else {
							$thisfile_midi_raw['track'][$tracknumber]['instrument'] = $this->GeneralMIDIinstrumentLookup($newprogramnum);
						}

					} elseif ($MIDIevents[$tracknumber][$eventid]['eventid'] == 0x0D) { // Channel after-touch

						$channelnumber = ord(substr($trackdata, $eventsoffset++, 1));

					} elseif ($MIDIevents[$tracknumber][$eventid]['eventid'] == 0x0E) { // Pitch wheel change (2000H is normal or no change)

						$changeLSB = ord(substr($trackdata, $eventsoffset++, 1));
						$changeMSB = ord(substr($trackdata, $eventsoffset++, 1));
						$pitchwheelchange = (($changeMSB & 0x7F) << 7) & ($changeLSB & 0x7F);

					} elseif (($MIDIevents[$tracknumber][$eventid]['eventid'] == 0x0F) && ($MIDIevents[$tracknumber][$eventid]['channel'] == 0x0F)) {

						$METAeventCommand = ord(substr($trackdata, $eventsoffset++, 1));
						$METAeventLength  = ord(substr($trackdata, $eventsoffset++, 1));
						$METAeventData    = substr($trackdata, $eventsoffset, $METAeventLength);
						$eventsoffset += $METAeventLength;
						switch ($METAeventCommand) {
							case 0x00: // Set track sequence number
								$track_sequence_number = getid3_lib::BigEndian2Int(substr($METAeventData, 0, $METAeventLength));
								//$thisfile_midi_raw['events'][$tracknumber][$eventid]['seqno'] = $track_sequence_number;
								break;

							case 0x01: // Text: generic
								$text_generic = substr($METAeventData, 0, $METAeventLength);
								//$thisfile_midi_raw['events'][$tracknumber][$eventid]['text'] = $text_generic;
								$thisfile_midi['comments']['comment'][] = $text_generic;
								break;

							case 0x02: // Text: copyright
								$text_copyright = substr($METAeventData, 0, $METAeventLength);
								//$thisfile_midi_raw['events'][$tracknumber][$eventid]['copyright'] = $text_copyright;
								$thisfile_midi['comments']['copyright'][] = $text_copyright;
								break;

							case 0x03: // Text: track name
								$text_trackname = substr($METAeventData, 0, $METAeventLength);
								$thisfile_midi_raw['track'][$tracknumber]['name'] = $text_trackname;
								break;

							case 0x04: // Text: track instrument name
								$text_instrument = substr($METAeventData, 0, $METAeventLength);
								//$thisfile_midi_raw['events'][$tracknumber][$eventid]['instrument'] = $text_instrument;
								break;

							case 0x05: // Text: lyrics
								$text_lyrics  = substr($METAeventData, 0, $METAeventLength);
								//$thisfile_midi_raw['events'][$tracknumber][$eventid]['lyrics'] = $text_lyrics;
								if (!isset($thisfile_midi['lyrics'])) {
									$thisfile_midi['lyrics'] = '';
								}
								$thisfile_midi['lyrics'] .= $text_lyrics."\n";
								break;

							case 0x06: // Text: marker
								$text_marker = substr($METAeventData, 0, $METAeventLength);
								//$thisfile_midi_raw['events'][$tracknumber][$eventid]['marker'] = $text_marker;
								break;

							case 0x07: // Text: cue point
								$text_cuepoint = substr($METAeventData, 0, $METAeventLength);
								//$thisfile_midi_raw['events'][$tracknumber][$eventid]['cuepoint'] = $text_cuepoint;
								break;

							case 0x2F: // End Of Track
								//$thisfile_midi_raw['events'][$tracknumber][$eventid]['EOT'] = $CumulativeDeltaTime;
								break;

							case 0x51: // Tempo: microseconds / quarter note
								$CurrentMicroSecondsPerBeat = getid3_lib::BigEndian2Int(substr($METAeventData, 0, $METAeventLength));
								if ($CurrentMicroSecondsPerBeat == 0) {
									$info['error'][] = 'Corrupt MIDI file: CurrentMicroSecondsPerBeat == zero';
									return false;
								}
								$thisfile_midi_raw['events'][$tracknumber][$CumulativeDeltaTime]['us_qnote'] = $CurrentMicroSecondsPerBeat;
								$CurrentBeatsPerMinute = (1000000 / $CurrentMicroSecondsPerBeat) * 60;
								$MicroSecondsPerQuarterNoteAfter[$CumulativeDeltaTime] = $CurrentMicroSecondsPerBeat;
								$TicksAtCurrentBPM = 0;
								break;

							case 0x58: // Time signature
								$timesig_numerator   = getid3_lib::BigEndian2Int($METAeventData{0});
								$timesig_denominator = pow(2, getid3_lib::BigEndian2Int($METAeventData{1})); // $02 -> x/4, $03 -> x/8, etc
								$timesig_32inqnote   = getid3_lib::BigEndian2Int($METAeventData{2});         // number of 32nd notes to the quarter note
								//$thisfile_midi_raw['events'][$tracknumber][$eventid]['timesig_32inqnote']   = $timesig_32inqnote;
								//$thisfile_midi_raw['events'][$tracknumber][$eventid]['timesig_numerator']   = $timesig_numerator;
								//$thisfile_midi_raw['events'][$tracknumber][$eventid]['timesig_denominator'] = $timesig_denominator;
								//$thisfile_midi_raw['events'][$tracknumber][$eventid]['timesig_text']        = $timesig_numerator.'/'.$timesig_denominator;
								$thisfile_midi['timesignature'][] = $timesig_numerator.'/'.$timesig_denominator;
								break;

							case 0x59: // Keysignature
								$keysig_sharpsflats = getid3_lib::BigEndian2Int($METAeventData{0});
								if ($keysig_sharpsflats & 0x80) {
									// (-7 -> 7 flats, 0 ->key of C, 7 -> 7 sharps)
									$keysig_sharpsflats -= 256;
								}

								$keysig_majorminor  = getid3_lib::BigEndian2Int($METAeventData{1}); // 0 -> major, 1 -> minor
								$keysigs = array(-7=>'Cb', -6=>'Gb', -5=>'Db', -4=>'Ab', -3=>'Eb', -2=>'Bb', -1=>'F', 0=>'C', 1=>'G', 2=>'D', 3=>'A', 4=>'E', 5=>'B', 6=>'F#', 7=>'C#');
								//$thisfile_midi_raw['events'][$tracknumber][$eventid]['keysig_sharps'] = (($keysig_sharpsflats > 0) ? abs($keysig_sharpsflats) : 0);
								//$thisfile_midi_raw['events'][$tracknumber][$eventid]['keysig_flats']  = (($keysig_sharpsflats < 0) ? abs($keysig_sharpsflats) : 0);
								//$thisfile_midi_raw['events'][$tracknumber][$eventid]['keysig_minor']  = (bool) $keysig_majorminor;
								//$thisfile_midi_raw['events'][$tracknumber][$eventid]['keysig_text']   = $keysigs[$keysig_sharpsflats].' '.($thisfile_midi_raw['events'][$tracknumber][$eventid]['keysig_minor'] ? 'minor' : 'major');

								// $keysigs[$keysig_sharpsflats] gets an int key (correct) - $keysigs["$keysig_sharpsflats"] gets a string key (incorrect)
								$thisfile_midi['keysignature'][] = $keysigs[$keysig_sharpsflats].' '.((bool) $keysig_majorminor ? 'minor' : 'major');
								break;

							case 0x7F: // Sequencer specific information
								$custom_data = substr($METAeventData, 0, $METAeventLength);
								break;

							default:
								$info['warning'][] = 'Unhandled META Event Command: '.$METAeventCommand;
								break;
						}

					} else {

						$info['warning'][] = 'Unhandled MIDI Event ID: '.$MIDIevents[$tracknumber][$eventid]['eventid'].' + Channel ID: '.$MIDIevents[$tracknumber][$eventid]['channel'];

					}
				}
				if (($tracknumber > 0) || (count($trackdataarray) == 1)) {
					$thisfile_midi['totalticks'] = max($thisfile_midi['totalticks'], $CumulativeDeltaTime);
				}
			}
			$previoustickoffset = null;

			ksort($MicroSecondsPerQuarterNoteAfter);
			foreach ($MicroSecondsPerQuarterNoteAfter as $tickoffset => $microsecondsperbeat) {
				if (is_null($previoustickoffset)) {
					$prevmicrosecondsperbeat = $microsecondsperbeat;
					$previoustickoffset = $tickoffset;
					continue;
				}
				if ($thisfile_midi['totalticks'] > $tickoffset) {

					if ($thisfile_midi_raw['ticksperqnote'] == 0) {
						$info['error'][] = 'Corrupt MIDI file: ticksperqnote == zero';
						return false;
					}

					$info['playtime_seconds'] += (($tickoffset - $previoustickoffset) / $thisfile_midi_raw['ticksperqnote']) * ($prevmicrosecondsperbeat / 1000000);

					$prevmicrosecondsperbeat = $microsecondsperbeat;
					$previoustickoffset = $tickoffset;
				}
			}
			if ($thisfile_midi['totalticks'] > $previoustickoffset) {

				if ($thisfile_midi_raw['ticksperqnote'] == 0) {
					$info['error'][] = 'Corrupt MIDI file: ticksperqnote == zero';
					return false;
				}

				$info['playtime_seconds'] += (($thisfile_midi['totalticks'] - $previoustickoffset) / $thisfile_midi_raw['ticksperqnote']) * ($microsecondsperbeat / 1000000);

			}
		}


		if (!empty($info['playtime_seconds'])) {
			$info['bitrate'] = (($info['avdataend'] - $info['avdataoffset']) * 8) / $info['playtime_seconds'];
		}

		if (!empty($thisfile_midi['lyrics'])) {
			$thisfile_midi['comments']['lyrics'][] = $thisfile_midi['lyrics'];
		}

		return true;
	}

	public function GeneralMIDIinstrumentLookup($instrumentid) {

		$begin = __LINE__;

		/** This is not a comment!

			0	Acoustic Grand
			1	Bright Acoustic
			2	Electric Grand
			3	Honky-Tonk
			4	Electric Piano 1
			5	Electric Piano 2
			6	Harpsichord
			7	Clavier
			8	Celesta
			9	Glockenspiel
			10	Music Box
			11	Vibraphone
			12	Marimba
			13	Xylophone
			14	Tubular Bells
			15	Dulcimer
			16	Drawbar Organ
			17	Percussive Organ
			18	Rock Organ
			19	Church Organ
			20	Reed Organ
			21	Accordian
			22	Harmonica
			23	Tango Accordian
			24	Acoustic Guitar (nylon)
			25	Acoustic Guitar (steel)
			26	Electric Guitar (jazz)
			27	Electric Guitar (clean)
			28	Electric Guitar (muted)
			29	Overdriven Guitar
			30	Distortion Guitar
			31	Guitar Harmonics
			32	Acoustic Bass
			33	Electric Bass (finger)
			34	Electric Bass (pick)
			35	Fretless Bass
			36	Slap Bass 1
			37	Slap Bass 2
			38	Synth Bass 1
			39	Synth Bass 2
			40	Violin
			41	Viola
			42	Cello
			43	Contrabass
			44	Tremolo Strings
			45	Pizzicato Strings
			46	Orchestral Strings
			47	Timpani
			48	String Ensemble 1
			49	String Ensemble 2
			50	SynthStrings 1
			51	SynthStrings 2
			52	Choir Aahs
			53	Voice Oohs
			54	Synth Voice
			55	Orchestra Hit
			56	Trumpet
			57	Trombone
			58	Tuba
			59	Muted Trumpet
			60	French Horn
			61	Brass Section
			62	SynthBrass 1
			63	SynthBrass 2
			64	Soprano Sax
			65	Alto Sax
			66	Tenor Sax
			67	Baritone Sax
			68	Oboe
			69	English Horn
			70	Bassoon
			71	Clarinet
			72	Piccolo
			73	Flute
			74	Recorder
			75	Pan Flute
			76	Blown Bottle
			77	Shakuhachi
			78	Whistle
			79	Ocarina
			80	Lead 1 (square)
			81	Lead 2 (sawtooth)
			82	Lead 3 (calliope)
			83	Lead 4 (chiff)
			84	Lead 5 (charang)
			85	Lead 6 (voice)
			86	Lead 7 (fifths)
			87	Lead 8 (bass + lead)
			88	Pad 1 (new age)
			89	Pad 2 (warm)
			90	Pad 3 (polysynth)
			91	Pad 4 (choir)
			92	Pad 5 (bowed)
			93	Pad 6 (metallic)
			94	Pad 7 (halo)
			95	Pad 8 (sweep)
			96	FX 1 (rain)
			97	FX 2 (soundtrack)
			98	FX 3 (crystal)
			99	FX 4 (atmosphere)
			100	FX 5 (brightness)
			101	FX 6 (goblins)
			102	FX 7 (echoes)
			103	FX 8 (sci-fi)
			104	Sitar
			105	Banjo
			106	Shamisen
			107	Koto
			108	Kalimba
			109	Bagpipe
			110	Fiddle
			111	Shanai
			112	Tinkle Bell
			113	Agogo
			114	Steel Drums
			115	Woodblock
			116	Taiko Drum
			117	Melodic Tom
			118	Synth Drum
			119	Reverse Cymbal
			120	Guitar Fret Noise
			121	Breath Noise
			122	Seashore
			123	Bird Tweet
			124	Telephone Ring
			125	Helicopter
			126	Applause
			127	Gunshot

		*/

		return getid3_lib::EmbeddedLookup($instrumentid, $begin, __LINE__, __FILE__, 'GeneralMIDIinstrument');
	}

	public function GeneralMIDIpercussionLookup($instrumentid) {

		$begin = __LINE__;

		/** This is not a comment!

			35	Acoustic Bass Drum
			36	Bass Drum 1
			37	Side Stick
			38	Acoustic Snare
			39	Hand Clap
			40	Electric Snare
			41	Low Floor Tom
			42	Closed Hi-Hat
			43	High Floor Tom
			44	Pedal Hi-Hat
			45	Low Tom
			46	Open Hi-Hat
			47	Low-Mid Tom
			48	Hi-Mid Tom
			49	Crash Cymbal 1
			50	High Tom
			51	Ride Cymbal 1
			52	Chinese Cymbal
			53	Ride Bell
			54	Tambourine
			55	Splash Cymbal
			56	Cowbell
			57	Crash Cymbal 2
			59	Ride Cymbal 2
			60	Hi Bongo
			61	Low Bongo
			62	Mute Hi Conga
			63	Open Hi Conga
			64	Low Conga
			65	High Timbale
			66	Low Timbale
			67	High Agogo
			68	Low Agogo
			69	Cabasa
			70	Maracas
			71	Short Whistle
			72	Long Whistle
			73	Short Guiro
			74	Long Guiro
			75	Claves
			76	Hi Wood Block
			77	Low Wood Block
			78	Mute Cuica
			79	Open Cuica
			80	Mute Triangle
			81	Open Triangle

		*/

		return getid3_lib::EmbeddedLookup($instrumentid, $begin, __LINE__, __FILE__, 'GeneralMIDIpercussion');
	}

}
