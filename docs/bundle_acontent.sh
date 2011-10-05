#! /bin/csh -f
#########################################################################
# AContent bundle script                                                  #
# ./bundle [VERSION] to specify an optional version number              #
# Author: Greg Gay - IDI, July 2010                              #
#########################################################################


set now = `date +"%Y_%m_%d"`
set acontent_dir = "AContent_$now"
set bundle = "AContent"
set svndir = "http://svn.atutor.ca/repos/transformable2/trunk/docs/"
set svnexec = "svn"

echo "\033[1mAContent Bundle Script [for CVS 1.3.1+] \033[0m"
echo "--------------------"

if ($#argv > 0) then
	set extension = "-$argv[1]"
else 
	echo "\nNo argument given. Run \033[1m./bundle_acontent.sh [VERSION]\033[0m to specify bundle version."
	set extension = ""
endif

if ($#argv == "2") then
	set ignore_mode = true
else
	set ignore_mode = false
endif

echo "\nUsing $acontent_dir as temp bundle directory."
echo "Using $bundle$extension.tar.gz as bundle name."
sleep 1
if (-e $acontent_dir) then
	echo -n "\nDir $acontent_dir exists. Overwrite? (y/q) "

	set ans = $<
	switch ($ans)
	    case q: 
		echo "\n$acontent_dir not touched. Exiting.\n"
	       exit
	    case y:
		echo "\nRemoving old $acontent_dir"
		rm -r $acontent_dir
	endsw
endif
sleep 1

echo "\nExporting from SVN/ to $acontent_dir"
mkdir $acontent_dir
$svnexec --force export $svndir
mv 'docs' $acontent_dir/AContent
sleep 1

echo "\nDumping language_text"
rm $acontent_dir/AContent/install/db/language_text.sql
echo "DROP TABLE language_text;" > $acontent_dir/AContent/install/db/language_text.sql
wget --output-document=- http://atutor.ca/atutor/translate/dump_lang_acontent.php >> $acontent_dir/AContent/install/db/language_text.sql

sleep 1

echo "\nRemoving $acontent_dir/AContent/include/config.inc.php"
rm -f $acontent_dir/AContent/include/config.inc.php
echo -n "<?php /* This file is a placeholder. Do not delete. Use the automated installer. */ ?>" > $acontent_dir/AContent/include/config.inc.php
sleep 1



echo "\nDisabling TR_DEVEL if enabled."
sed "s/define('TR_DEVEL', 1);/define('TR_DEVEL', 0);/" $acontent_dir/AContent/include/vitals.inc.php > $acontent_dir/vitals.inc.php
rm $acontent_dir/AContent/include/vitals.inc.php
echo "\nDisabling AT_DEVEL_TRANSLATE if enabled."
sed "s/define('AT_DEVEL_TRANSLATE', 1);/define('AT_DEVEL_TRANSLATE', 0);/" $acontent_dir/vitals.inc.php > $acontent_dir/AContent/include/vitals.inc.php
sleep 1

echo -n "<?php "'$svn_data = '"'" >> $acontent_dir/AContent/svn.php
$svnexec log  -q -r HEAD http://svn.atutor.ca/repos/transformable2/trunk/  >> $acontent_dir/AContent/svn.php
echo -n "';?>" >> $acontent_dir/AContent/svn.php

echo "\nTargz'ing $bundle${extension}.tar.gz $acontent_dir/AContent/"
sleep 1

if (-f "$bundle${extension}.tar.gz") then
	echo -n "\nBundle $bundle$extension.tar.gz exists. Overwrite? (y/n/q) "

	set ans = $<

	switch ($ans)
	    case q:
		echo "\n$bundle$extension.tar.gz not touched."
		exit
	    case y:
		echo "\nRemoving old $bundle$extension.tar.gz"
		set final_name = "$bundle$extension.tar.gz"
		rm -r "$bundle$extension.tar.gz"
		breaksw
	    case n: 
		set time = `date +"%k_%M_%S"`
		set extension = "${extension}-${time}"
		echo "\nSaving as $bundle$extension.tar.gz instead.\n"
		set final_name = "$bundle$extension.tar.gz"
		breaksw
	endsw
else
	set final_name = "$bundle$extension.tar.gz"
endif	

echo "Creating \033[1m$final_name\033[0m"
cd $acontent_dir
tar -zcf $final_name AContent/
mv $final_name ..
cd ..
sleep 1

if ($ignore_mode == true) then
	set ans = "y"
else 
	echo -n "\nRemove temp $acontent_dir directory? (y/n) "
	set ans = $<
endif

if ($ans == "y") then
	echo "\nRemoving temp $acontent_dir directory"
	rm -r $acontent_dir
endif

echo "\n\033[1m >> Did you update check_acontent_version.php ?? << \033[0m"

echo "\n\033[1mBundle complete. Enjoy.\n\nExiting.\033[0m"


exit 1
