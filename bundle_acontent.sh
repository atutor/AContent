#! /bin/csh -f
#########################################################################
# AContent bundle script                                                  #
# ./bundle_acontent.sh [VERSION] to specify an optional version number              #
# Author: Greg Gay - IDI, July 2010                              #
#########################################################################
# Updated Oct 16 2011 for GitHub GG
#
#
set now = `date +"%Y_%m_%d"`
set acontent_dir = "AContent_$now"
set bundle = "AContent"
set gitdir = "git://github.com/atutor/AContent.git"
set gitexec = "git"

echo "AContent Bundle Script [for GitHub]"
echo "--------------------"

if ($#argv > 0) then
	set extension = "-$argv[1]"
else 
	echo "No argument given. Runb./bundle_acontent.sh [VERSION] to specify bundle version."
	set extension = ""
endif

if ($#argv == "2") then
	set ignore_mode = true
else
	set ignore_mode = false
endif

echo "Using $acontent_dir as temp bundle directory."
echo "Using $bundle$extension.tar.gz as bundle name."
sleep 1
if (-e $acontent_dir) then
	echo -n "Dir $acontent_dir exists. Overwrite? (y/q) "

	set ans = $<
	switch ($ans)
	    case q: 
		echo "$acontent_dir not touched. Exiting.\n"
	       exit
	    case y:
		echo "Removing old $acontent_dir"
		rm -r $acontent_dir
	endsw
endif
sleep 1

echo "Exporting from GitHub/ to $acontent_dir"
mkdir $acontent_dir
$gitexec clone $gitdir
mv 'AContent' $acontent_dir/AContent
sleep 1

echo "Dumping language_text"
rm $acontent_dir/AContent/install/db/language_text.sql
echo "DROP TABLE language_text;" > $acontent_dir/AContent/install/db/language_text.sql
wget --output-document=- http://atutor.ca/atutor/translate/dump_lang_acontent.php >> $acontent_dir/AContent/install/db/language_text.sql

sleep 1

echo "Removing $acontent_dir/AContent/include/config.inc.php"
rm -f $acontent_dir/AContent/include/config.inc.php
echo -n "<?php /* This file is a placeholder. Do not delete. Use the automated installer. */ ?>" > $acontent_dir/AContent/include/config.inc.php
sleep 1



echo "Disabling TR_DEVEL if enabled."
sed "s/define('TR_DEVEL', 1);/define('TR_DEVEL', 0);/" $acontent_dir/AContent/include/vitals.inc.php > $acontent_dir/vitals.inc.php
rm $acontent_dir/AContent/include/vitals.inc.php
echo "Disabling AT_DEVEL_TRANSLATE if enabled."
sed "s/define('AT_DEVEL_TRANSLATE', 1);/define('AT_DEVEL_TRANSLATE', 0);/" $acontent_dir/vitals.inc.php > $acontent_dir/AContent/include/vitals.inc.php
sleep 1

set date = `date`
echo -n "<?php "'$svn_data = '"'" >> $acontent_dir/AContent/svn.php
echo $date  >> $acontent_dir/AContent/svn.php
echo -n "';?>" >> $acontent_dir/AContent/svn.php
echo "Removing GIT related directories"
rm -Rf $acontent_dir/AContent/.git*
echo "Targz'ing $bundle${extension}.tar.gz $acontent_dir/AContent/"
sleep 1

if (-f "$bundle${extension}.tar.gz") then
	echo -n "Bundle $bundle$extension.tar.gz exists. Overwrite? (y/n/q) "

	set ans = $<

	switch ($ans)
	    case q:
		echo "$bundle$extension.tar.gz not touched."
		exit
	    case y:
		echo "Removing old $bundle$extension.tar.gz"
		set final_name = "$bundle$extension.tar.gz"
		rm -r "$bundle$extension.tar.gz"
		breaksw
	    case n: 
		set time = `date +"%k_%M_%S"`
		set extension = "${extension}-${time}"
		echo "Saving as $bundle$extension.tar.gz instead.\n"
		set final_name = "$bundle$extension.tar.gz"
		breaksw
	endsw
else
	set final_name = "$bundle$extension.tar.gz"
endif	

echo "Creating $final_name"
cd $acontent_dir
tar -zcf $final_name AContent/
mv $final_name ..
cd ..
sleep 1

if ($ignore_mode == true) then
	set ans = "y"
else 
	echo -n "Remove temp $acontent_dir directory? (y/n) "
	set ans = $<
endif

if ($ans == "y") then
	echo "Removing temp $acontent_dir directory"
	rm -rf $acontent_dir
endif

echo ">> Did you update check_acontent_version.php ?? << "

echo "mBundle complete. Enjoy. Exiting."


exit 1
