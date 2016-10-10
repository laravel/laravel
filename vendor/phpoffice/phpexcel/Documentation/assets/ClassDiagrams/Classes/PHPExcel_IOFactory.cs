using System;
using System.Collections.Generic;
using System.Text;

namespace ClassDiagrams
{
    public class PHPExcel_IOFactory
    {
        public PHPExcel_Reader_IReader createsReader
        {
            get
            {
                throw new System.NotImplementedException();
            }
            set
            {
            }
        }

        public PHPExcel_Writer_IWriter createsWriter
        {
            get
            {
                throw new System.NotImplementedException();
            }
            set
            {
            }
        }
    
        public PHPExcel_Writer_IWriter createWriter()
        {
            throw new System.NotImplementedException();
        }

        public PHPExcel_Reader_IReader createReader()
        {
            throw new System.NotImplementedException();
        }
    }
}
