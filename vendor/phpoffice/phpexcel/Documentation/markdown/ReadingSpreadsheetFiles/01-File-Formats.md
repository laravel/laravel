# PHPExcel User Documentation – Reading Spreadsheet Files


## Spreadsheet File Formats

PHPExcel can read a number of different spreadsheet and file formats, although not all features are supported by all of the readers. Check the Functionality Cross-Reference document (Functionality Cross-Reference.xls) for a list that identifies which features are supported by which readers.

Currently, PHPExcel supports the following File Types for Reading:

### Excel5

The Microsoft Excel™ Binary file format (BIFF5 and BIFF8) is a binary file format that was used by Microsoft Excel™ between versions 95 and 2003. The format is supported (to various extents) by most spreadsheet programs. BIFF files normally have an extension of .xls. Documentation describing the format can be found online at [http://msdn.microsoft.com/en-us/library/cc313154(v=office.12).aspx][1] or from [http://download.microsoft.com/download/2/4/8/24862317-78F0-4C4B-B355-C7B2C1D997DB/[MS-XLS].pdf][2] (as a downloadable PDF).

### Excel2003XML

Microsoft Excel™ 2003 included options for a file format called SpreadsheetML. This file is a zipped XML document. It is not very common, but its core features are supported. Documentation for the format can be found at [http://msdn.microsoft.com/en-us/library/aa140066%28office.10%29.aspx][3] though it’s sadly rather sparse in its detail.

### Excel2007

Microsoft Excel™ 2007 shipped with a new file format, namely Microsoft Office Open XML SpreadsheetML, and Excel 2010 extended this still further with its new features such as sparklines. These files typically have an extension of .xlsx. This format is based around a zipped collection of eXtensible Markup Language (XML) files. Microsoft Office Open XML SpreadsheetML is mostly standardized in ECMA 376 ([http://www.ecma-international.org/news/TC45_current_work/TC45_available_docs.htm][4]) and ISO 29500.

### OOCalc

aka Open Document Format (ODF) or OASIS, this is the OpenOffice.org XML File Format for spreadsheets. It comprises a zip archive including several components all of which are text files, most of these with markup in the eXtensible Markup Language (XML). It is the standard file format for OpenOffice.org Calc and StarCalc, and files typically have an extension of .ods. The published specification for the file format is available from the OASIS Open Office XML Format Technical Committee web page ([http://www.oasis-open.org/committees/tc_home.php?wg_abbrev=office#technical][5]). Other information is available from the OpenOffice.org XML File Format web page ([http://xml.openoffice.org/general.html][6]), part of the OpenOffice.org project.

### SYLK

This is the Microsoft Multiplan Symbolic Link Interchange (SYLK) file format. Multiplan was a predecessor to Microsoft Excel™. Files normally have an extension of .slk. While not common, there are still a few applications that generate SYLK files as a cross-platform option, because (despite being limited to a single worksheet) it is a simple format to implement, and supports some basic data and cell formatting options (unlike CSV files).

### Gnumeric

The Gnumeric file format is used by the Gnome Gnumeric spreadsheet application, and typically files have an extension of .gnumeric. The file contents are stored using eXtensible Markup Language (XML) markup, and the file is then compressed using the GNU project's gzip compression library. [http://projects.gnome.org/gnumeric/doc/file-format-gnumeric.shtml][7]

### CSV

Comma Separated Value (CSV) file format is a common structuring strategy for text format files. In CSV flies, each line in the file represents a row of data and (within each line of the file) the different data fields (or columns) are separated from one another using a comma (","). If a data field contains a comma, then it should be enclosed (typically in quotation marks ("). Sometimes tabs "\t", or the pipe symbol ("|"), or a semi-colon (";") are used as separators instead of a comma, although other symbols can be used. Because CSV is a text-only format, it doesn't support any data formatting options.

"CSV" is not a single, well-defined format (although see RFC 4180 for one definition that is commonly used). Rather, in practice the term "CSV" refers to any file that:
 - is plain text using a character set such as ASCII, Unicode, EBCDIC, or Shift JIS,
 - consists of records (typically one record per line),
 - with the records divided into fields separated by delimiters (typically a single reserved character such as comma, semicolon, or tab,
 - where every record has the same sequence of fields.

Within these general constraints, many variations are in use. Therefore "CSV" files are not entirely portable. Nevertheless, the variations are fairly small, and many implementations allow users to glance at the file (which is feasible because it is plain text), and then specify the delimiter character(s), quoting rules, etc.

**Warning:** Microsoft Excel™ will open .csv files, but depending on the system's regional settings, it may expect a semicolon as a separator instead of a comma, since in some languages the comma is used as the decimal separator. Also, many regional versions of Excel will not be able to deal with Unicode characters in a CSV file.

### HTML

HyperText Markup Language (HTML) is the main markup language for creating web pages and other information that can be displayed in a web browser. Files typically have an extension of .html or .htm.  HTML markup provides a means to create structured documents by denoting structural semantics for text such as headings, paragraphs, lists, links, quotes and other items. Since 1996, the HTML specifications have been maintained, with input from commercial software vendors, by the World Wide Web Consortium (W3C). However, in 2000, HTML also became an international standard (ISO/IEC 15445:2000). HTML 4.01 was published in late 1999, with further errata published through 2001. In 2004 development began on HTML5 in the Web Hypertext Application Technology Working Group (WHATWG), which became a joint deliverable with the W3C in 2008.



  [1]: http://msdn.microsoft.com/en-us/library/cc313154(v=office.12).aspx
  [2]: http://download.microsoft.com/download/2/4/8/24862317-78F0-4C4B-B355-C7B2C1D997DB/%5bMS-XLS%5d.pdf
  [3]: http://msdn.microsoft.com/en-us/library/aa140066%28office.10%29.aspx
  [4]: http://www.ecma-international.org/news/TC45_current_work/TC45_available_docs.htm
  [5]: http://www.oasis-open.org/committees/tc_home.php?wg_abbrev=office
  [6]: http://xml.openoffice.org/general.html
  [7]: http://projects.gnome.org/gnumeric/doc/file-format-gnumeric.shtml
