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
// module.archive.tar.php                                      //
// module for analyzing TAR files                              //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////
//                                                             //
// Module originally written by                                //
//      Mike Mozolin <teddybearØmail*ru>                       //
//                                                             //
/////////////////////////////////////////////////////////////////


class getid3_tar extends getid3_handler
{

	public function Analyze() {
		$info = &$this->getid3->info;

		$info['fileformat'] = 'tar';
		$info['tar']['files'] = array();

		$unpack_header = 'a100fname/a8mode/a8uid/a8gid/a12size/a12mtime/a8chksum/a1typflag/a100lnkname/a6magic/a2ver/a32uname/a32gname/a8devmaj/a8devmin/a155prefix';
		$null_512k = str_repeat("\x00", 512); // end-of-file marker

		$this->fseek(0);
		while (!feof($this->getid3->fp)) {
			$buffer = $this->fread(512);
			if (strlen($buffer) < 512) {
				break;
			}

			// check the block
			$checksum = 0;
			for ($i = 0; $i < 148; $i++) {
				$checksum += ord($buffer{$i});
			}
			for ($i = 148; $i < 156; $i++) {
				$checksum += ord(' ');
			}
			for ($i = 156; $i < 512; $i++) {
				$checksum += ord($buffer{$i});
			}
			$attr    = unpack($unpack_header, $buffer);
			$name    =       (isset($attr['fname']  ) ? trim($attr['fname']  ) : '');
			$mode    = octdec(isset($attr['mode']   ) ? trim($attr['mode']   ) : '');
			$uid     = octdec(isset($attr['uid']    ) ? trim($attr['uid']    ) : '');
			$gid     = octdec(isset($attr['gid']    ) ? trim($attr['gid']    ) : '');
			$size    = octdec(isset($attr['size']   ) ? trim($attr['size']   ) : '');
			$mtime   = octdec(isset($attr['mtime']  ) ? trim($attr['mtime']  ) : '');
			$chksum  = octdec(isset($attr['chksum'] ) ? trim($attr['chksum'] ) : '');
			$typflag =       (isset($attr['typflag']) ? trim($attr['typflag']) : '');
			$lnkname =       (isset($attr['lnkname']) ? trim($attr['lnkname']) : '');
			$magic   =       (isset($attr['magic']  ) ? trim($attr['magic']  ) : '');
			$ver     =       (isset($attr['ver']    ) ? trim($attr['ver']    ) : '');
			$uname   =       (isset($attr['uname']  ) ? trim($attr['uname']  ) : '');
			$gname   =       (isset($attr['gname']  ) ? trim($attr['gname']  ) : '');
			$devmaj  = octdec(isset($attr['devmaj'] ) ? trim($attr['devmaj'] ) : '');
			$devmin  = octdec(isset($attr['devmin'] ) ? trim($attr['devmin'] ) : '');
			$prefix  =       (isset($attr['prefix'] ) ? trim($attr['prefix'] ) : '');
			if (($checksum == 256) && ($chksum == 0)) {
				// EOF Found
				break;
			}
			if ($prefix) {
				$name = $prefix.'/'.$name;
			}
			if ((preg_match('#/$#', $name)) && !$name) {
				$typeflag = 5;
			}
			if ($buffer == $null_512k) {
				// it's the end of the tar-file...
				break;
			}

			// Read to the next chunk
			$this->fseek($size, SEEK_CUR);

			$diff = $size % 512;
			if ($diff != 0) {
				// Padding, throw away
				$this->fseek((512 - $diff), SEEK_CUR);
			}
			// Protect against tar-files with garbage at the end
			if ($name == '') {
				break;
			}
			$info['tar']['file_details'][$name] = array (
				'name'     => $name,
				'mode_raw' => $mode,
				'mode'     => self::display_perms($mode),
				'uid'      => $uid,
				'gid'      => $gid,
				'size'     => $size,
				'mtime'    => $mtime,
				'chksum'   => $chksum,
				'typeflag' => self::get_flag_type($typflag),
				'linkname' => $lnkname,
				'magic'    => $magic,
				'version'  => $ver,
				'uname'    => $uname,
				'gname'    => $gname,
				'devmajor' => $devmaj,
				'devminor' => $devmin
			);
			$info['tar']['files'] = getid3_lib::array_merge_clobber($info['tar']['files'], getid3_lib::CreateDeepArray($info['tar']['file_details'][$name]['name'], '/', $size));
		}
		return true;
	}

	// Parses the file mode to file permissions
	public function display_perms($mode) {
		// Determine Type
		if     ($mode & 0x1000) $type='p'; // FIFO pipe
		elseif ($mode & 0x2000) $type='c'; // Character special
		elseif ($mode & 0x4000) $type='d'; // Directory
		elseif ($mode & 0x6000) $type='b'; // Block special
		elseif ($mode & 0x8000) $type='-'; // Regular
		elseif ($mode & 0xA000) $type='l'; // Symbolic Link
		elseif ($mode & 0xC000) $type='s'; // Socket
		else                    $type='u'; // UNKNOWN

		// Determine permissions
		$owner['read']    = (($mode & 00400) ? 'r' : '-');
		$owner['write']   = (($mode & 00200) ? 'w' : '-');
		$owner['execute'] = (($mode & 00100) ? 'x' : '-');
		$group['read']    = (($mode & 00040) ? 'r' : '-');
		$group['write']   = (($mode & 00020) ? 'w' : '-');
		$group['execute'] = (($mode & 00010) ? 'x' : '-');
		$world['read']    = (($mode & 00004) ? 'r' : '-');
		$world['write']   = (($mode & 00002) ? 'w' : '-');
		$world['execute'] = (($mode & 00001) ? 'x' : '-');

		// Adjust for SUID, SGID and sticky bit
		if ($mode & 0x800) $owner['execute'] = ($owner['execute'] == 'x') ? 's' : 'S';
		if ($mode & 0x400) $group['execute'] = ($group['execute'] == 'x') ? 's' : 'S';
		if ($mode & 0x200) $world['execute'] = ($world['execute'] == 'x') ? 't' : 'T';

		$s  = sprintf('%1s', $type);
		$s .= sprintf('%1s%1s%1s',      $owner['read'], $owner['write'], $owner['execute']);
		$s .= sprintf('%1s%1s%1s',      $group['read'], $group['write'], $group['execute']);
		$s .= sprintf('%1s%1s%1s'."\n", $world['read'], $world['write'], $world['execute']);
		return $s;
	}

	// Converts the file type
	public function get_flag_type($typflag) {
		static $flag_types = array(
			'0' => 'LF_NORMAL',
			'1' => 'LF_LINK',
			'2' => 'LF_SYNLINK',
			'3' => 'LF_CHR',
			'4' => 'LF_BLK',
			'5' => 'LF_DIR',
			'6' => 'LF_FIFO',
			'7' => 'LF_CONFIG',
			'D' => 'LF_DUMPDIR',
			'K' => 'LF_LONGLINK',
			'L' => 'LF_LONGNAME',
			'M' => 'LF_MULTIVOL',
			'N' => 'LF_NAMES',
			'S' => 'LF_SPARSE',
			'V' => 'LF_VOLHDR'
		);
		return (isset($flag_types[$typflag]) ? $flag_types[$typflag] : '');
	}

}
