#!/bin/sh

##############################################################################
# Switch between Apache and LiteSpeed Web Server under control panel environment
# @Author:   LiteSpeed Technologies, Inc. (https://www.litespeedtech.com)
# @Copyright: (c) 2016
##############################################################################

check_errs()
{
  if [ "${1}" -ne "0" ] ; then
    echo "[ERROR] ${2}" 
    exit ${1}
  fi
}

INST_USER=`id`
INST_USER=`expr "$INST_USER" : 'uid=.*(\(.*\)) gid=.*'`
if [ $INST_USER != "root" ]; then
    check_errs 1 "Only root user can switch web server!"
fi

display_usage()
{
    cat <<EOF
cp_switch_ws.sh - Switch between Apache and LiteSpeed under control panel environment.
Currently support cPanel and Plesk. LiteSpeed plugin is required to be installed first.

Usage: cp_switch_ws.sh apache|lsws
Param: targeted web server. Fixed value either "apache" or "lsws"

EOF
    exit 1
}

detect_control_panel()
{
    # detect cPanel
    if [ -d "/usr/local/cpanel/whostmgr" ] ; then
	CP="WHM"
	CPCMD="/usr/local/cpanel/whostmgr/docroot/cgi/lsws/bin/lsws_cmd.sh"
	if [ -f "$CPCMD" ] ; then
	    echo "Detect cPanel WHM environment"
	else
	    check_errs 1 "cPanel environment detected, but LiteSpeed WHM plugin not installed."
	fi

    elif [ -e "/opt/psa/version" ] || [ -e "/usr/local/psa/version" ] ; then
	# detect Plesk
	CP="PSA"
	if [ -e "/usr/local/psa/version" ] ; then
	    PSA_BASE="/usr/local/psa"
	else
	    PSA_BASE="/opt/psa"
	fi

	CPCMD="$PSA_BASE/admin/sbin/modules/litespeed/lsws_cmd"
	if [ -f "$CPCMD" ] ; then
	    echo "Detect Plesk environment"
	else
	    check_errs 1 "Plesk environment detected, but LiteSpeed Plesk plugin not installed."
	fi

    elif [ -d "/usr/local/directadmin" ] ; then
	# detect DirectAdmin
	CP="DA"
	DA_DIR="/usr/local/directadmin/custombuild"

	if [[ `/usr/local/directadmin/custombuild/build version` =~ ^2* ]] ; then
	    echo "Detect DirectAdmin Environment"
	else
	    check_errs 1 "DirectAdmin environment detected, but CustomBuild 2.0 not available"
	fi

    else
	check_errs 1 "Cannot detect control panel environment. Only cPanel WHM, Plesk, DirectAdmin are checked for now."
    fi
}

switch_cp()
{
    if [ "x$1" = "xapache" ] ; then
	# param value SWITCH_TO_APACHE is hard-coded in plugin
	$CPCMD $LSWSHOME "SWITCH_TO_APACHE"
    elif [ "x$1" = "xlsws" ] ; then
	# param value SWITCH_TO_LSWS is hard-coded in plugin
	$CPCMD $LSWSHOME "SWITCH_TO_LSWS"
    fi
}

switch_da()
{
    cd ${DA_DIR}

    pmod1=`grep "php1_mode" /usr/local/directadmin/custombuild/options.conf | cut -d = -f 2 | xargs`
    pmod2=`grep "php2_mode" /usr/local/directadmin/custombuild/options.conf | cut -d = -f 2 | xargs`


    if [ "x$1" = "xapache" ] ; then
	DAWS=apache
        if [ "x$pmod1" = "xlsphp" ]; then
          ./build set php1_mode fastcgi
          ./build set mod_ruid2 no
    	  ./build php n
          ./build rewrite_confs
        fi
    else
	DAWS=litespeed
        if [ "x$pmod1" != "xlsphp" ]; then
          ./build set php1_mode lsphp
          if [ "x$pmod2" != "xlsphp" ]; then
            ./build set php2_mode lsphp
          fi
    	  ./build php n
          ./build rewrite_confs
        fi 
    fi

    ./build set webserver $DAWS
    ./build $DAWS
}

if [ $# -ne 1 ] ; then
    echo "Illegal parameters = $# !"
    display_usage
fi

# set LSWSHOME
cd `dirname "$0"`
CURDIR=`pwd`
LSWSHOME=`dirname $CURDIR`
LSWSHOME=`dirname $LSWSHOME`


if [ "x$1" != "xapache" ] && [ "x$1" != "xlsws" ] ; then
    display_usage
fi

detect_control_panel

if [ "$CP" = "WHM" ] || [ "$CP" = "PSA" ] ; then
    switch_cp $1
elif [ "$CP" = "DA" ] ; then
    switch_da $1
fi

