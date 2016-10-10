#!/bin/bash

#DEST_DIR=../docs/pkg
if [ ! -d ../tutorials ] 
then
	mkdir ../tutorials
fi
if [ ! -d ../tutorials/SimpleTest ]
then
	mkdir ../tutorials/SimpleTest
fi
DEST_DIR=../tutorials/SimpleTest

rm ${DEST_DIR}/*.pkg
cp ../docs/pkg/SimpleTest.pkg.ini ${DEST_DIR}

#Xalan -o ${DEST_DIR}/QuickStart.pkg ../docs/source/en/simple_test.xml phpdoc_docs.xslt
Xalan -o ${DEST_DIR}/SimpleTest.pkg ../docs/source/en/overview.xml phpdoc_docs.xslt
Xalan -o ${DEST_DIR}/UnitTestCase.pkg ../docs/source/en/unit_test_documentation.xml phpdoc_docs.xslt
Xalan -o ${DEST_DIR}/GroupTests.pkg ../docs/source/en/group_test_documentation.xml phpdoc_docs.xslt
Xalan -o ${DEST_DIR}/MockObjects.pkg ../docs/source/en/mock_objects_documentation.xml phpdoc_docs.xslt
Xalan -o ${DEST_DIR}/PartialMock.pkg ../docs/source/en/partial_mocks_documentation.xml phpdoc_docs.xslt
Xalan -o ${DEST_DIR}/Reporting.pkg ../docs/source/en/reporter_documentation.xml phpdoc_docs.xslt
Xalan -o ${DEST_DIR}/Expectations.pkg ../docs/source/en/expectation_documentation.xml phpdoc_docs.xslt
Xalan -o ${DEST_DIR}/WebTester.pkg ../docs/source/en/web_tester_documentation.xml phpdoc_docs.xslt
Xalan -o ${DEST_DIR}/FormTesting.pkg ../docs/source/en/form_testing_documentation.xml phpdoc_docs.xslt
Xalan -o ${DEST_DIR}/Authentication.pkg ../docs/source/en/authentication_documentation.xml phpdoc_docs.xslt
Xalan -o ${DEST_DIR}/Browser.pkg ../docs/source/en/browser_documentation.xml phpdoc_docs.xslt

# some cleanup work
cd $DEST_DIR

# remove XML declaration
for f in $(ls *.pkg --color=none)
do
	grep -v -e '^<?xml' $f > tmp.pkg
	mv tmp.pkg $f
done

# fix overview title
cat SimpleTest.pkg | sed -e 's/<refname>Overview/<refname>Simple Test PHP Unit Test Framework/g;s/<\([A-Za-z0-9]*\)\/>/<\1><\/\1>/g' > tmp.pkg
mv tmp.pkg SimpleTest.pkg
