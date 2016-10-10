# PHPExcel User Documentation – Reading Spreadsheet Files


## Security

XML-based formats such as OfficeOpen XML, Excel2003 XML, OASIS and Gnumeric are susceptible to XML External Entity Processing (XXE) injection attacks (for an explanation of XXE injection see http://websec.io/2012/08/27/Preventing-XEE-in-PHP.html) when reading spreadsheet files. This can lead to:

 - Disclosure whether a file is existent
 - Server Side Request Forgery
 - Command Execution (depending on the installed PHP wrappers)
 

To prevent this, PHPExcel sets `libxml_disable_entity_loader` to `true` for the XML-based Readers by default. 