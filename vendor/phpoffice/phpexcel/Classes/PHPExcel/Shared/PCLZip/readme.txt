// --------------------------------------------------------------------------------
// PclZip 2.8.2 - readme.txt
// --------------------------------------------------------------------------------
// License GNU/LGPL - August 2009
// Vincent Blavet - vincent@phpconcept.net
// http://www.phpconcept.net
// --------------------------------------------------------------------------------
// $Id: readme.txt,v 1.60 2009/09/30 20:35:21 vblavet Exp $
// --------------------------------------------------------------------------------



0 - Sommaire
============
    1 - Introduction
    2 - What's new
    3 - Corrected bugs
    4 - Known bugs or limitations
    5 - License
    6 - Warning
    7 - Documentation
    8 - Author
    9 - Contribute

1 - Introduction
================

  PclZip is a library that allow you to manage a Zip archive.

  Full documentation about PclZip can be found here : http://www.phpconcept.net/pclzip

2 - What's new
==============

  Version 2.8.2 :
    - PCLZIP_CB_PRE_EXTRACT and PCLZIP_CB_POST_EXTRACT are now supported with 
      extraction as a string (PCLZIP_OPT_EXTRACT_AS_STRING). The string
      can also be modified in the post-extract call back.
    **Bugs correction :
    - PCLZIP_OPT_REMOVE_ALL_PATH was not working correctly    
    - Remove use of eval() and do direct call to callback functions
    - Correct support of 64bits systems (Thanks to WordPress team)

  Version 2.8.1 :
    - Move option PCLZIP_OPT_BY_EREG to PCLZIP_OPT_BY_PREG because ereg() is
      deprecated in PHP 5.3. When using option PCLZIP_OPT_BY_EREG, PclZip will
      automatically replace it by PCLZIP_OPT_BY_PREG.
  
  Version 2.8 :
    - Improve extraction of zip archive for large files by using temporary files
      This feature is working like the one defined in r2.7.
      Options are renamed : PCLZIP_OPT_TEMP_FILE_ON, PCLZIP_OPT_TEMP_FILE_OFF,
      PCLZIP_OPT_TEMP_FILE_THRESHOLD
    - Add a ratio constant PCLZIP_TEMPORARY_FILE_RATIO to configure the auto
      sense of temporary file use.
    - Bug correction : Reduce filepath in returned file list to remove ennoying
      './/' preambule in file path.

  Version 2.7 :
    - Improve creation of zip archive for large files :
      PclZip will now autosense the configured memory and use temporary files
      when large file is suspected.
      This feature can also ne triggered by manual options in create() and add()
      methods. 'PCLZIP_OPT_ADD_TEMP_FILE_ON' force the use of temporary files,
      'PCLZIP_OPT_ADD_TEMP_FILE_OFF' disable the autosense technic, 
      'PCLZIP_OPT_ADD_TEMP_FILE_THRESHOLD' allow for configuration of a size
      threshold to use temporary files.
      Using "temporary files" rather than "memory" might take more time, but
      might give the ability to zip very large files :
      Tested on my win laptop with a 88Mo file :
        Zip "in-memory" : 18sec (max_execution_time=30, memory_limit=180Mo)
        Zip "tmporary-files" : 23sec (max_execution_time=30, memory_limit=30Mo)
    - Replace use of mktime() by time() to limit the E_STRICT error messages.
    - Bug correction : When adding files with full windows path (drive letter)
      PclZip is now working. Before, if the drive letter is not the default
      path, PclZip was not able to add the file.

  Version 2.6 :
    - Code optimisation
    - New attributes PCLZIP_ATT_FILE_COMMENT gives the ability to
      add a comment for a specific file. (Don't really know if this is usefull)
    - New attribute PCLZIP_ATT_FILE_CONTENT gives the ability to add a string 
      as a file.
    - New attribute PCLZIP_ATT_FILE_MTIME modify the timestamp associated with
      a file.
    - Correct a bug. Files archived with a timestamp with 0h0m0s were extracted
      with current time
    - Add CRC value in the informations returned back for each file after an
      action.
    - Add missing closedir() statement.
    - When adding a folder, and removing the path of this folder, files were
      incorrectly added with a '/' at the beginning. Which means files are 
      related to root in unix systems. Corrected.
    - Add conditional if before constant definition. This will allow users
      to redefine constants without changing the file, and then improve
      upgrade of pclzip code for new versions.
  
  Version 2.5 :
    - Introduce the ability to add file/folder with individual properties (file descriptor).
      This gives for example the ability to change the filename of a zipped file.
      . Able to add files individually
      . Able to change full name
      . Able to change short name
      . Compatible with global options
    - New attributes : PCLZIP_ATT_FILE_NAME, PCLZIP_ATT_FILE_NEW_SHORT_NAME, PCLZIP_ATT_FILE_NEW_FULL_NAME
    - New error code : PCLZIP_ERR_INVALID_ATTRIBUTE_VALUE
    - Add a security control feature. PclZip can extract any file in any folder
      of a system. People may use this to upload a zip file and try to override
      a system file. The PCLZIP_OPT_EXTRACT_DIR_RESTRICTION will give the
      ability to forgive any directory transversal behavior.
    - New PCLZIP_OPT_EXTRACT_DIR_RESTRICTION : check extraction path
    - New error code : PCLZIP_ERR_DIRECTORY_RESTRICTION
    - Modification in PclZipUtilPathInclusion() : dir and path beginning with ./ will be prepend
      by current path (getcwd())
  
  Version 2.4 :
    - Code improvment : try to speed up the code by removing unusefull call to pack()
    - Correct bug in delete() : delete() should be called with no argument. This was not
      the case in 2.3. This is corrected in 2.4.
    - Correct a bug in path_inclusion function. When the path has several '../../', the
      result was bad.
    - Add a check for magic_quotes_runtime configuration. If enabled, PclZip will 
      disable it while working and det it back to its original value.
      This resolve a lots of bad formated archive errors.
    - Bug correction : PclZip now correctly unzip file in some specific situation,
      when compressed content has same size as uncompressed content.
    - Bug correction : When selecting option 'PCLZIP_OPT_REMOVE_ALL_PATH', 
      directories are not any more created.
    - Code improvment : correct unclosed opendir(), better handling of . and .. in
      loops.


  Version 2.3 :
    - Correct a bug with PHP5 : affecting the value 0xFE49FFE0 to a variable does not
      give the same result in PHP4 and PHP5 ....

  Version 2.2 :
    - Try development of PCLZIP_OPT_CRYPT .....
      However this becomes to a stop. To crypt/decrypt I need to multiply 2 long integers,
      the result (greater than a long) is not supported by PHP. Even the use of bcmath
      functions does not help. I did not find yet a solution ...;
    - Add missing '/' at end of directory entries
    - Check is a file is encrypted or not. Returns status 'unsupported_encryption' and/or
      error code PCLZIP_ERR_UNSUPPORTED_ENCRYPTION.
    - Corrected : Bad "version need to extract" field in local file header
    - Add private method privCheckFileHeaders() in order to check local and central
      file headers. PclZip is now supporting purpose bit flag bit 3. Purpose bit flag bit 3 gives
      the ability to have a local file header without size, compressed size and crc filled.
    - Add a generic status 'error' for file status
    - Add control of compression type. PclZip only support deflate compression method.
      Before v2.2, PclZip does not check the compression method used in an archive while
      extracting. With v2.2 PclZip returns a new error status for a file using an unsupported
      compression method. New status is "unsupported_compression". New error code is
      PCLZIP_ERR_UNSUPPORTED_COMPRESSION.
    - Add optional attribute PCLZIP_OPT_STOP_ON_ERROR. This will stop the extract of files
      when errors like 'a folder with same name exists' or 'a newer file exists' or
      'a write protected file' exists, rather than set a status for the concerning file
      and resume the extract of the zip.
    - Add optional attribute PCLZIP_OPT_REPLACE_NEWER. This will force, during an extract' the
      replacement of the file, even if a  newer version of the file exists.
      Note that today if a file with the same name already exists but is older it will be
      replaced by the extracted one.
    - Improve PclZipUtilOption()
    - Support of zip archive with trailing bytes. Before 2.2, PclZip checks that the central
      directory structure is the last data in the archive. Crypt encryption/decryption of
      zip archive put trailing 0 bytes after decryption. PclZip is now supporting this.

  Version 2.1 :
    - Add the ability to abort the extraction by using a user callback function.
      The user can now return the value '2' in its callback which indicates to stop the
      extraction. For a pre call-back extract is stopped before the extration of the current
      file. For a post call back, the extraction is stopped after.
    - Add the ability to extract a file (or several files) directly in the standard output.
      This is done by the new parameter PCLZIP_OPT_EXTRACT_IN_OUTPUT with method extract().
    - Add support for parameters PCLZIP_OPT_COMMENT, PCLZIP_OPT_ADD_COMMENT,
      PCLZIP_OPT_PREPEND_COMMENT. This will create, replace, add, or prepend comments
      in the zip archive.
    - When merging two archives, the comments are not any more lost, but merged, with a 
      blank space separator.
    - Corrected bug : Files are not deleted when all files are asked to be deleted.
    - Corrected bug : Folders with name '0' made PclZip to abort the create or add feature.


  Version 2.0 :
    ***** Warning : Some new features may break the backward compatibility for your scripts.
                    Please carefully read the readme file.
    - Add the ability to delete by Index, name and regular expression. This feature is 
      performed by the method delete(), which uses the optional parameters
      PCLZIP_OPT_BY_INDEX, PCLZIP_OPT_BY_NAME, PCLZIP_OPT_BY_EREG or PCLZIP_OPT_BY_PREG.
    - Add the ability to extract by regular expression. To extract by regexp you must use the method
      extract(), with the option PCLZIP_OPT_BY_EREG or PCLZIP_OPT_BY_PREG 
      (depending if you want to use ereg() or preg_match() syntax) followed by the 
      regular expression pattern.
    - Add the ability to extract by index, directly with the extract() method. This is a
      code improvment of the extractByIndex() method.
    - Add the ability to extract by name. To extract by name you must use the method
      extract(), with the option PCLZIP_OPT_BY_NAME followed by the filename to
      extract or an array of filenames to extract. To extract all a folder, use the folder
      name rather than the filename with a '/' at the end.
    - Add the ability to add files without compression. This is done with a new attribute
      which is PCLZIP_OPT_NO_COMPRESSION.
    - Add the attribute PCLZIP_OPT_EXTRACT_AS_STRING, which allow to extract a file directly
      in a string without using any file (or temporary file).
    - Add constant PCLZIP_SEPARATOR for static configuration of filename separators in a single string.
      The default separator is now a comma (,) and not any more a blank space.
      THIS BREAK THE BACKWARD COMPATIBILITY : Please check if this may have an impact with
      your script.
    - Improve algorythm performance by removing the use of temporary files when adding or 
      extracting files in an archive.
    - Add (correct) detection of empty filename zipping. This can occurs when the removed
      path is the same
      as a zipped dir. The dir is not zipped (['status'] = filtered), only its content.
    - Add better support for windows paths (thanks for help from manus@manusfreedom.com).
    - Corrected bug : When the archive file already exists with size=0, the add() method
      fails. Corrected in 2.0.
    - Remove the use of OS_WINDOWS constant. Use php_uname() function rather.
    - Control the order of index ranges in extract by index feature.
    - Change the internal management of folders (better handling of internal flag).


  Version 1.3 :
    - Removing the double include check. This is now done by include_once() and require_once()
      PHP directives.
    - Changing the error handling mecanism : Remove the use of an external error library.
      The former PclError...() functions are replaced by internal equivalent methods.
      By changing the environment variable PCLZIP_ERROR_EXTERNAL you can still use the former library.
      Introducing the use of constants for error codes rather than integer values. This will help
      in futur improvment.
      Introduction of error handling functions like errorCode(), errorName() and errorInfo().
    - Remove the deprecated use of calling function with arguments passed by reference.
    - Add the calling of extract(), extractByIndex(), create() and add() functions
      with variable options rather than fixed arguments.
    - Add the ability to remove all the file path while extracting or adding,
      without any need to specify the path to remove.
      This is available for extract(), extractByIndex(), create() and add() functionS by using
      the new variable options parameters :
      - PCLZIP_OPT_REMOVE_ALL_PATH : by indicating this option while calling the fct.
    - Ability to change the mode of a file after the extraction (chmod()).
      This is available for extract() and extractByIndex() functionS by using
      the new variable options parameters.
      - PCLZIP_OPT_SET_CHMOD : by setting the value of this option.
    - Ability to definition call-back options. These call-back will be called during the adding,
      or the extracting of file (extract(), extractByIndex(), create() and add() functions) :
      - PCLZIP_CB_PRE_EXTRACT : will be called before each extraction of a file. The user
        can trigerred the change the filename of the extracted file. The user can triggered the
        skip of the extraction. This is adding a 'skipped' status in the file list result value.
      - PCLZIP_CB_POST_EXTRACT : will be called after each extraction of a file.
        Nothing can be triggered from that point.
      - PCLZIP_CB_PRE_ADD : will be called before each add of a file. The user
        can trigerred the change the stored filename of the added file. The user can triggered the
        skip of the add. This is adding a 'skipped' status in the file list result value.
      - PCLZIP_CB_POST_ADD : will be called after each add of a file.
        Nothing can be triggered from that point.
    - Two status are added in the file list returned as function result : skipped & filename_too_long
      'skipped' is used when a call-back function ask for skipping the file.
      'filename_too_long' is used while adding a file with a too long filename to archive (the file is
      not added)
    - Adding the function PclZipUtilPathInclusion(), that check the inclusion of a path into
      a directory.
    - Add a check of the presence of the archive file before some actions (like list, ...)
    - Add the initialisation of field "index" in header array. This means that by
      default index will be -1 when not explicitly set by the methods.

  Version 1.2 :
    - Adding a duplicate function.
    - Adding a merge function. The merge function is a "quick merge" function,
      it just append the content of an archive at the end of the first one. There
      is no check for duplicate files or more recent files.
    - Improve the search of the central directory end.

  Version 1.1.2 :

    - Changing the license of PclZip. PclZip is now released under the GNU / LGPL license
      (see License section).
    - Adding the optional support of a static temporary directory. You will need to configure
      the constant PCLZIP_TEMPORARY_DIR if you want to use this feature.
    - Improving the rename() function. In some cases rename() does not work (different
      Filesystems), so it will be replaced by a copy() + unlink() functions.

  Version 1.1.1 :

    - Maintenance release, no new feature.

  Version 1.1 :

    - New method Add() : adding files in the archive
    - New method ExtractByIndex() : partial extract of the archive, files are identified by
      their index in the archive
    - New method DeleteByIndex() : delete some files/folder entries from the archive,
      files are identified by their index in the archive.
    - Adding a test of the zlib extension presence. If not present abort the script.

  Version 1.0.1 :

    - No new feature


3 - Corrected bugs
==================

  Corrected in Version 2.0 :
    - Corrected : During an extraction, if a call-back fucntion is used and try to skip
                  a file, all the extraction process is stopped. 

  Corrected in Version 1.3 :
    - Corrected : Support of static synopsis for method extract() is broken.
    - Corrected : invalid size of archive content field (0xFF) should be (0xFFFF).
    - Corrected : When an extract is done with a remove_path parameter, the entry for
      the directory with exactly the same path is not skipped/filtered.
    - Corrected : extractByIndex() and deleteByIndex() were not managing index in the
      right way. For example indexes '1,3-5,11' will only extract files 1 and 11. This
      is due to a sort of the index resulting table that puts 11 before 3-5 (sort on
      string and not interger). The sort is temporarilly removed, this means that
      you must provide a sorted list of index ranges.

  Corrected in Version 1.2 :

    - Nothing.

  Corrected in Version 1.1.2 :

    - Corrected : Winzip is unable to delete or add new files in a PclZip created archives.

  Corrected in Version 1.1.1 :

    - Corrected : When archived file is not compressed (0% compression), the
      extract method fails.

  Corrected in Version 1.1 :

    - Corrected : Adding a complete tree of folder may result in a bad archive
      creation.

  Corrected in Version 1.0.1 :

    - Corrected : Error while compressing files greater than PCLZIP_READ_BLOCK_SIZE (default=1024).


4 - Known bugs or limitations
=============================

  Please publish bugs reports in SourceForge :
    http://sourceforge.net/tracker/?group_id=40254&atid=427564

  In Version 2.x :
    - PclZip does only support file uncompressed or compressed with deflate (compression method 8)
    - PclZip does not support password protected zip archive
    - Some concern were seen when changing mtime of a file while archiving. 
      Seems to be linked to Daylight Saving Time (PclTest_changing_mtime).

  In Version 1.2 :

    - merge() methods does not check for duplicate files or last date of modifications.

  In Version 1.1 :

    - Limitation : Using 'extract' fields in the file header in the zip archive is not supported.
    - WinZip is unable to delete a single file in a PclZip created archive. It is also unable to
      add a file in a PclZip created archive. (Corrected in v.1.2)

  In Version 1.0.1 :

    - Adding a complete tree of folder may result in a bad archive
      creation. (Corrected in V.1.1).
    - Path given to methods must be in the unix format (/) and not the Windows format (\).
      Workaround : Use only / directory separators.
    - PclZip is using temporary files that are sometime the name of the file with a .tmp or .gz
      added suffix. Files with these names may already exist and may be overwritten.
      Workaround : none.
    - PclZip does not check if the zlib extension is present. If it is absent, the zip
      file is not created and the lib abort without warning.
      Workaround : enable the zlib extension on the php install

  In Version 1.0 :

    - Error while compressing files greater than PCLZIP_READ_BLOCK_SIZE (default=1024).
      (Corrected in v.1.0.1)
    - Limitation : Multi-disk zip archive are not supported.


5 - License
===========

  Since version 1.1.2, PclZip Library is released under GNU/LGPL license.
  This library is free, so you can use it at no cost.

  HOWEVER, if you release a script, an application, a library or any kind of
  code using PclZip library (or a part of it), YOU MUST :
  - Indicate in the documentation (or a readme file), that your work
    uses PclZip Library, and make a reference to the author and the web site
    http://www.phpconcept.net
  - Gives the ability to the final user to update the PclZip libary.

  I will also appreciate that you send me a mail (vincent@phpconcept.net), just to
  be aware that someone is using PclZip.

  For more information about GNU/LGPL license : http://www.gnu.org

6 - Warning
=================

  This library and the associated files are non commercial, non professional work.
  It should not have unexpected results. However if any damage is caused by this software
  the author can not be responsible.
  The use of this software is at the risk of the user.

7 - Documentation
=================
  PclZip User Manuel is available in English on PhpConcept : http://www.phpconcept.net/pclzip/man/en/index.php
  A Russian translation was done by Feskov Kuzma : http://php.russofile.ru/ru/authors/unsort/zip/

8 - Author
==========

  This software was written by Vincent Blavet (vincent@phpconcept.net) on its leasure time.

9 - Contribute
==============
  If you want to contribute to the development of PclZip, please contact vincent@phpconcept.net.
  If you can help in financing PhpConcept hosting service, please go to
  http://www.phpconcept.net/soutien.php
