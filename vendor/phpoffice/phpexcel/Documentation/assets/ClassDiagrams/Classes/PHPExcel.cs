using System;
using System.Collections.Generic;
using System.Text;

namespace ClassDiagrams
{
    public class PHPExcel
    {
        /// <remarks></remarks>
        public Worksheet Worksheets
        {
            get
            {
                throw new System.NotImplementedException();
            }
            set
            {
            }
        }
    }

    public class PHPExcel_Writer_PDF : PHPExcel_Writer_IWriter
    {
        #region PHPExcel_Writer_IWriter Members

        public PHPExcel writes
        {
            get
            {
                throw new Exception("The method or operation is not implemented.");
            }
            set
            {
                throw new Exception("The method or operation is not implemented.");
            }
        }

        #endregion
    }
}
