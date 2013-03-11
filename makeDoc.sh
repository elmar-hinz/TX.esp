#! /usr/bin/bash

(test -e ext_emconf.php) || exit 'wrong directory, usage: bash ./makeDoc.sh' 

soffice='/Applications/LibreOffice.app/Contents/MacOS/soffice'
baseDir=`pwd`

cd $baseDir/Documentation/_siteMake/
make clean
make html
rm -rf $baseDir/MANUAL
mv $baseDir/Documentation/_siteMake/_build/html/ $baseDir/MANUAL/
cd $baseDir/Documentation/_siteMake/
make clean


cd $baseDir/Documentation/_manualMake/
make clean
make singlehtml 
cd $baseDir/Documentation/_manualMake/_build/singlehtml/
$soffice --headless --convert-to odt Index.html
mv Index.odt $baseDir/doc/manual.odt
cd $baseDir/Documentation/_manualMake/
make clean







