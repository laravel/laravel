# Calculation Engine - Formula Function Reference

## General Introduction

### Function that are not Supported in Excel5

Not all functions are supported by the Excel 5 Writer. Use of these functions within your workbooks will result in an error when trying to write to Excel5.

The following is the list of those functions that are implemented within PHPExcel, but that cannot currently be written to Excel 5.

#### Cube Functions

    Excel Function      | Notes
    --------------------|---------
	CUBEKPIMEMBER       | Not yet Implemented
	CUBEMEMBER          | Not yet Implemented
	CUBEMEMBERPROPERTY  | Not yet Implemented
	CUBERANKEDMEMBER    | Not yet Implemented
	CUBESET             | Not yet Implemented
	CUBESETCOUNT        | Not yet Implemented
	CUBEVALUE           | Not yet Implemented


#### Database Functions

    Excel Function | Notes
    ---------------|---------


#### Date and Time Functions

    Excel Function | Notes
    ---------------|---------
    EDATE          | Not a standard function within Excel 5, but an add-in from the Analysis ToolPak.  
    EOMONTH        | Not a standard function within Excel 5, but an add-in from the Analysis ToolPak.

