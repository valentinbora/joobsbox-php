#!/bin/bash
#The cleanBOM.sh bash script
#written by erik@sankuru.biz This e-mail address is being protected from spambots. You need JavaScript enabled to view it
#12 Jun 2009
#Licensed under the GPL.
#
#Look up the files in a folder that contain the BOM marker
#and are php,ini,html,or js files
#and remove the BOM marker from the file

# -- first command line argument must be a folder name
folder="$1"

#expand the char codes in the regex to their corresponding characters
regex1=$'\xEF\xBB\xBF'
cmd1="grep -rl $regex1 $folder"

#select only php, ini, html, and js files
jregex2='\.\(php\|ini\|html\|js\)$'
cmd2="grep $regex2"

echo "The following files contain the BOM marker:"
$cmd1 | $cmd2

echo "Cleaning up:"
$cmd1 | $cmd2 | \
    while read f; do
        #back up file; only if not yet backed up
        backup="$f.backup"
        if [ ! -e "$backup" ]; then
            cp "$f" "$backup"
        fi
        # the copy operation above could have failed anyway (permissions)
        # don't proceed if there is no backup file
        if [ -e "$backup" ]; then
            #delete the original file
            rm -f "$f"
            #output the backup file without the BOM
            #and write it to the original file location
            sed "s/\xEF\xBB\xBF//" "$backup" > "$f"
        fi
    done 

echo "The following files still contain the BOM marker:"
$cmd1 | $cmd2
