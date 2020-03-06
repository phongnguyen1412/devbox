#!/bin/sh
CURDIR=`dirname "$0"`
cd $CURDIR
CURDIR=`pwd`
PIDFILE=/tmp/lshttpd/lshttpd.pid


INST_USER=`id`
INST_USER=`expr "$INST_USER" : 'uid=.*(\(.*\)) gid=.*'`
if [ $INST_USER != "root" ]; then
    cat <<EOF
[ERROR] Only root user can install the rc script!
EOF
    exit 1
fi


test_running()
{
RUNNING=0
if [ -f $PIDFILE ] ; then
    FPID=`cat $PIDFILE`
    if [ "x$FPID" != "x" ]; then
        kill -0 $FPID 2>/dev/null
        if [ $? -eq 0 ] ; then
            RUNNING=1
            PID=$FPID
        fi
    fi
fi
}


if [ "x`uname -s`" = "xDarwin" ]; then

    STARTUP_ITEM=/System/Library/StartupItems/lsws
    if [ ! -d $STARTUP_ITEM ]; then
        mkdir $STARTUP_ITEM
    fi
    cp -f "$CURDIR/lsws.rc" $STARTUP_ITEM/lsws
    cat <<EOF >$STARTUP_ITEM/StartupParameters.plist
{
  Description     = "LiteSpeed web server";
  Provides        = ("Web Server");
  Requires        = ("DirectoryServices");
  Uses            = ("Disks", "NFS");
  OrderPreference = "None";
}

EOF


    exit 0
fi

if [ "x`uname -s`" = "xFreeBSD" ]; then
    if [ -d "/etc/rc.d" ]; then
        if [ -e "/usr/local/etc/rc.d/lsws.sh" ]; then
            rm -f /usr/local/etc/rc.d/lsws.sh
        fi
    
        cp -f "$CURDIR/lsws.rc" /etc/rc.d/lsws
        chmod 755 /etc/rc.d/lsws
        echo "[OK] The startup script has been successfully installed!"
        exit 0
    else
        cat <<EOF
[ERROR] /etc/rc.d/ does not exit in this FreeBSD system!

EOF
        exit 1
    fi
fi

INIT_DIR=""
# actually only FreeBSD use /etc/rc.d
for path in /etc/init.d /etc/rc.d/init.d 
do
    if [ "${INIT_DIR}" = "" ]; then
        if [ -d "$path" ]; then
            INIT_DIR=$path
        fi
    fi
done

AP_PROC=httpd
if [ -e /etc/debian_version ]; then
    AP_PROC=apache2
fi

# use systemd if possible, need to use same method as apache
SYSTEMDDIR=""
for path in /etc/systemd/system /usr/lib/systemd/system
do
	if [ "${SYSTEMDDIR}" = "" ] ; then
		if [ -d "$path" ] && [ -e ${path}/${AP_PROC}.service ] ; then
			SYSTEMDDIR=$path
		fi
	fi
done

if [ "${SYSTEMDDIR}" != "" ] ; then
    if [ "${INIT_DIR}" != "" ] && [ -e ${INIT_DIR}/lsws ] ; then
        echo "Removing ${INIT_DIR}/lsws"
        rm -f ${INIT_DIR}/lsws
    fi

    cp -f ${CURDIR}/lshttpd.service ${SYSTEMDDIR}/lshttpd.service
    chmod 644 ${SYSTEMDDIR}/lshttpd.service
    if [ -d /usr/local/cpanel ] && [ -f ${SYSTEMDDIR}/httpd.service ] && [ ! -f ${SYSTEMDDIR}/httpd.service.ls_bak ] ; then
        test_running
        if [ $RUNNING -eq 1 ]; then
            mv ${SYSTEMDDIR}/httpd.service ${SYSTEMDDIR}/httpd.service.ls_bak
            ln -sf ${SYSTEMDDIR}/lshttpd.service ${SYSTEMDDIR}/httpd.service
        fi
    fi
    systemctl daemon-reload
    systemctl enable lshttpd.service 
    if [ $? -eq 0  ]; then
            echo "[OK] lshttpd.service has been successfully installed!"
            exit 0
    else
        echo "[ERROR] failed to enable lshttpd.service in systemd!"
        exit 1
    fi
fi


if [ "${INIT_DIR}" = "" ]; then
    echo "[ERROR] failed to find the init.d directory!"
    exit 1
fi

if [ -f ${INIT_DIR}/lsws ]; then
    rm -f ${INIT_DIR}/lsws
fi

if [ -f "/etc/gentoo-release" ]; then
    cp "${CURDIR}/lsws.rc.gentoo" ${INIT_DIR}/lsws
    chmod 755 ${INIT_DIR}/lsws
    rc-update add lsws default
    echo "[OK] The startup script has been successfully installed!"
    exit 0
fi

if [ -e /etc/debian_version ]; then
    cp "$CURDIR/lsws.rc" ${INIT_DIR}/lsws
    chmod 755 ${INIT_DIR}/lsws
    update-rc.d lsws defaults
    echo "[OK] The startup script has been successfully installed!"
    exit 0
fi

if [ -d "${INIT_DIR}/rc2.d" ]; then
        INIT_BASE_DIR=${INIT_DIR}
else
        INIT_BASE_DIR=`dirname ${INIT_DIR}`
fi

cp "$CURDIR/lsws.rc" ${INIT_DIR}/lsws
chmod 755 ${INIT_DIR}/lsws


if [ -d "${INIT_BASE_DIR}/runlevel/default" ]; then
    ln -fs ${INIT_DIR}/lsws ${INIT_BASE_DIR}/runlevel/default/S88lsws
    ln -fs ${INIT_DIR}/lsws ${INIT_BASE_DIR}/runlevel/default/K12lsws
fi


if [ -d "${INIT_BASE_DIR}/rc2.d" ]; then
    ln -fs ${INIT_DIR}/lsws ${INIT_BASE_DIR}/rc2.d/S88lsws
    ln -fs ${INIT_DIR}/lsws ${INIT_BASE_DIR}/rc2.d/K12lsws
fi

if [ -d "${INIT_BASE_DIR}/rc3.d" ]; then
    ln -fs ${INIT_DIR}/lsws ${INIT_BASE_DIR}/rc3.d/S88lsws
    ln -fs ${INIT_DIR}/lsws ${INIT_BASE_DIR}/rc3.d/K12lsws
fi

if [ -d "${INIT_BASE_DIR}/rc5.d" ]; then
    ln -fs ${INIT_DIR}/lsws ${INIT_BASE_DIR}/rc5.d/S88lsws
    ln -fs ${INIT_DIR}/lsws ${INIT_BASE_DIR}/rc5.d/K12lsws
fi

if [ -d "${INIT_BASE_DIR}/rc0.d" ]; then
    ln -fs ${INIT_DIR}/lsws ${INIT_BASE_DIR}/rc0.d/K12lsws
fi

if [ -d "${INIT_BASE_DIR}/rc1.d" ]; then
    ln -fs ${INIT_DIR}/lsws ${INIT_BASE_DIR}/rc1.d/K12lsws
fi

if [ -d "${INIT_BASE_DIR}/rc6.d" ]; then
    ln -fs ${INIT_DIR}/lsws ${INIT_BASE_DIR}/rc6.d/K12lsws
fi

echo "[OK] The startup script has been successfully installed!"

exit 0
