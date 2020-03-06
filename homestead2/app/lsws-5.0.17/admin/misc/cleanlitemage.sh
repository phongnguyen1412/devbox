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


if [ "x$1" = 'x' ]; then
echo "Usage:"
echo "   cleanlitemage.sh [-priv <age_mins>] [-pub <age_mins>] <litemage_cache_dir> ... "
echo ""
echo "Note:"
echo "   private cache max_age default is 60 minutes."
echo "   private cache max_age must be > 0 if set."
echo "   public cache max_age default is 0 minutes, meaning public cache will not be purged."
echo "   public cache max_age must be > 10 if set."
echo "   <litemage_cache_dir> is the root directory of LiteMage cache storage"
echo "      and should contain the 'priv' directory."
echo "      multiple cache root directories can be added." 
exit 1
fi

CUR_DIR=`dirname "$0"`
cd $CUR_DIR
CUR_DIR=`pwd`

private_mins=60
public_mins=0

if [ "x$1" == "x-priv" ]; then
    shift
    if [ "$1" -eq "$1" ] 2>/dev/null
    then
        echo "private max age is $1" 1>/dev/null
    else  
        echo "'-priv' must be followed by max_age for private cache in minutes."
        exit 1
    fi
    private_mins=$1
    shift
fi

if [ "x$1" == "x-pub" ]; then
    shift
    if [ "$1" -eq "$1" ] 2>/dev/null
    then
        echo "public max age is $1" 1>/dev/null
    else
        echo "'-pub' must be followed by max_age for public cache in minutes."
        exit 1
    fi
    public_mins=$1
    shift
fi

if [ "x$1" = 'x' ]; then
    echo "ERROR: no cache root directory provided."
    exit 1    
fi


while [ $# -gt 0 ]
do
    root_dir=$1
    shift
    if [ ! -d "$root_dir" ]; then
        echo "ERROR: $root_dir directory does not exists."    
        continue 
    fi
    if [ $public_mins -gt 10 ]; then
        clean_cache_dir "$root_dir" $public_mins
    fi

    if [ $private_mins -gt 0 ]; then
        if [ ! -d "$root_dir/priv" ]; then
            echo "NOTICE: '$root_dir/priv' directory does not exist, skip."
            continue
        fi
        clean_cache_dir "$root_dir/priv" $private_mins
    fi
done

