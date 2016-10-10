<?php
/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2014 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared_OLE
 * @copyright  Copyright (c) 2006 - 2007 Christian Schmidt
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version 1.8.0, 2014-03-02
 */

/**
 * PHPExcel_Shared_OLE_ChainedBlockStream
 *
 * Stream wrapper for reading data stored in an OLE file. Implements methods
 * for PHP's stream_wrapper_register(). For creating streams using this
 * wrapper, use PHPExcel_Shared_OLE_PPS_File::getStream().
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared_OLE
 */
class PHPExcel_Shared_OLE_ChainedBlockStream
{
	/**
	 * The OLE container of the file that is being read.
	 * @var OLE
	 */
	public $ole;

	/**
	 * Parameters specified by fopen().
	 * @var array
	 */
	public $params;

	/**
	 * The binary data of the file.
	 * @var  string
	 */
	public $data;

	/**
	 * The file pointer.
	 * @var  int  byte offset
	 */
	public $pos;

	/**
	 * Implements support for fopen().
	 * For creating streams using this wrapper, use OLE_PPS_File::getStream().
	 *
	 * @param	string	$path			resource name including scheme, e.g.
	 *									ole-chainedblockstream://oleInstanceId=1
	 * @param	string	$mode			only "r" is supported
	 * @param	int		$options		mask of STREAM_REPORT_ERRORS and STREAM_USE_PATH
	 * @param	string  &$openedPath	absolute path of the opened stream (out parameter)
	 * @return	bool    true on success
	 */
	public function stream_open($path, $mode, $options, &$openedPath)
	{
		if ($mode != 'r') {
			if ($options & STREAM_REPORT_ERRORS) {
				trigger_error('Only reading is supported', E_USER_WARNING);
			}
			return false;
		}

		// 25 is length of "ole-chainedblockstream://"
		parse_str(substr($path, 25), $this->params);
		if (!isset($this->params['oleInstanceId'],
				   $this->params['blockId'],
				   $GLOBALS['_OLE_INSTANCES'][$this->params['oleInstanceId']])) {

			if ($options & STREAM_REPORT_ERRORS) {
				trigger_error('OLE stream not found', E_USER_WARNING);
			}
			return false;
		}
		$this->ole = $GLOBALS['_OLE_INSTANCES'][$this->params['oleInstanceId']];

		$blockId = $this->params['blockId'];
		$this->data = '';
		if (isset($this->params['size']) &&
			$this->params['size'] < $this->ole->bigBlockThreshold &&
			$blockId != $this->ole->root->_StartBlock) {

			// Block id refers to small blocks
			$rootPos = $this->ole->_getBlockOffset($this->ole->root->_StartBlock);
			while ($blockId != -2) {
				$pos = $rootPos + $blockId * $this->ole->bigBlockSize;
				$blockId = $this->ole->sbat[$blockId];
				fseek($this->ole->_file_handle, $pos);
				$this->data .= fread($this->ole->_file_handle, $this->ole->bigBlockSize);
			}
		} else {
			// Block id refers to big blocks
			while ($blockId != -2) {
				$pos = $this->ole->_getBlockOffset($blockId);
				fseek($this->ole->_file_handle, $pos);
				$this->data .= fread($this->ole->_file_handle, $this->ole->bigBlockSize);
				$blockId = $this->ole->bbat[$blockId];
			}
		}
		if (isset($this->params['size'])) {
			$this->data = substr($this->data, 0, $this->params['size']);
		}

		if ($options & STREAM_USE_PATH) {
			$openedPath = $path;
		}

		return true;
	}

	/**
	 * Implements support for fclose().
	 *
	 */
	public function stream_close()
	{
		$this->ole = null;
		unset($GLOBALS['_OLE_INSTANCES']);
	}

	/**
	 * Implements support for fread(), fgets() etc.
	 *
	 * @param   int		$count	maximum number of bytes to read
	 * @return  string
	 */
	public function stream_read($count)
	{
		if ($this->stream_eof()) {
			return false;
		}
		$s = substr($this->data, $this->pos, $count);
		$this->pos += $count;
		return $s;
	}

	/**
	 * Implements support for feof().
	 *
	 * @return  bool  TRUE if the file pointer is at EOF; otherwise FALSE
	 */
	public function stream_eof()
	{
//		As we don't support below 5.2 anymore, this is simply redundancy and overhead
//		$eof = $this->pos >= strlen($this->data);
//		// Workaround for bug in PHP 5.0.x: http://bugs.php.net/27508
//		if (version_compare(PHP_VERSION, '5.0', '>=') &&
//			version_compare(PHP_VERSION, '5.1', '<')) {
//		   $eof = !$eof;
//		}
//		return $eof;
		return $this->pos >= strlen($this->data);
	}

	/**
	 * Returns the position of the file pointer, i.e. its offset into the file
	 * stream. Implements support for ftell().
	 *
	 * @return  int
	 */
	public function stream_tell()
	{
		return $this->pos;
	}

	/**
	 * Implements support for fseek().
	 *
	 * @param	int		$offset	byte offset
	 * @param	int		$whence	SEEK_SET, SEEK_CUR or SEEK_END
	 * @return	bool
	 */
	public function stream_seek($offset, $whence)
	{
		if ($whence == SEEK_SET && $offset >= 0) {
			$this->pos = $offset;
		} elseif ($whence == SEEK_CUR && -$offset <= $this->pos) {
			$this->pos += $offset;
		} elseif ($whence == SEEK_END && -$offset <= sizeof($this->data)) {
			$this->pos = strlen($this->data) + $offset;
		} else {
			return false;
		}
		return true;
	}

	/**
	 * Implements support for fstat(). Currently the only supported field is
	 * "size".
	 * @return  array
	 */
	public function stream_stat()
	{
		return array(
			'size' => strlen($this->data),
			);
	}

	// Methods used by stream_wrapper_register() that are not implemented:
	// bool stream_flush ( void )
	// int stream_write ( string data )
	// bool rename ( string path_from, string path_to )
	// bool mkdir ( string path, int mode, int options )
	// bool rmdir ( string path, int options )
	// bool dir_opendir ( string path, int options )
	// array url_stat ( string path, int flags )
	// string dir_readdir ( void )
	// bool dir_rewinddir ( void )
	// bool dir_closedir ( void )
}
