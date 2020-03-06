#!/bin/sh

#if [ -f '/usr/bin/ionice' ]; then
#    echo "ionice:" `ionice` 1>&2
#fi 

clean_cache_dir()
{
    for subdir in '0' '1' '2' '3' '4' '5' '6' '7' '8' '9' 'a' 'b' 'c' 'd' 'e' 'f'
    do
        find "$1/$subdir" -type f -mmin +$2 -delete 2>/dev/null
        if [ $? -ne 0 ]; then
            find "$1/$subdir" -type f -mmin +$2 2>/dev/null | xargs rm -f
        fi 
        
        # clean directory
        find "$1/$subdir" -empty -delete 2>/dev/null
        if [ $? -ne 0 ]; then
            find "$1/$subdir" -empty 2>/dev/null | xargs rm -rf
        fi
    #fi
    done

}


CUR_DIR=`dirname "$0"`
cd $CUR_DIR
CUR_DIR=`pwd`

find "$CUR_DIR/../../admin/tmp" -type s -atime +1 -delete 2>/dev/null
if [ $? -ne 0 ]; then
    find "$CUR_DIR/../../admin/tmp" -type s -atime +1 2>/dev/null | xargs rm -f
fi 

find "/tmp/lshttpd" -type s -atime +1 -delete 2>/dev/null
if [ $? -ne 0 ]; then
    find "/tmp/lshttpd" -type s -atime +1 2>/dev/null | xargs rm -f
fi 

#if [ -f '/usr/bin/ionice' ]; then
#    echo "ionice:" `ionice` 1>&2
#fi 
max_age_mins=1440
while [ $# -gt 0 ]
do
    root_dir=$1
    shift
    if [ "x$root_dir" = 'x' ]; then
        exit 1
    fi
    if [ ! -d "$root_dir" ]; then
        exit 2
    fi

    clean_cache_dir "$root_dir" 1440
    clean_cache_dir "$root_dir/priv" 60
    
done

