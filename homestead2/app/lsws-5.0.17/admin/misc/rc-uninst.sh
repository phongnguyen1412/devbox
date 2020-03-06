#!/bin/sh
CURDIR=`dirname "$0"`
cd $CURDIR
CURDIR=`pwd`

INST_USER=`id`
INST_USER=`expr "$INST_USER" : 'uid=.*(\(.*\)) gid=.*'`
if [ $INST_USER != "root" ]; then
    cat <<EOF
[ERROR] Only root user can uninstall the rc script!
EOF
    exit 1
fi

if [ "x`uname -s`" = "xDarwin" ]; then

    STARTUP_ITEM=/System/Library/StartupItems/lsws
    if [ -d ${STARTUP_ITEM} ]; then
        rm -rf ${STARTUP_ITEM}
        echo "[OK] The startup script has been successfully uninstalled!"
    fi
    exit 0
fi

AP_PROC=httpd
if [ -e /etc/debian_version ]; then
    AP_PROC=apache2
fi

if [ "x`uname -s`" = "xFreeBSD" ]; then
    if [ -e "/etc/rc.d/lsws" ]; then
	rm -f /etc/rc.d/lsws
	echo "[OK] The startup script has been successfully uninstalled!"
    fi
    if [ -e "/usr/local/etc/rc.d/lsws.sh" ]; then
        rm -f /usr/local/etc/rc.d/lsws.sh
    fi
    if [ -e "/etc/rc.d/${AP_PROC}.ls_bak" ] ; then
	mv -f /etc/rc.d/${AP_PROC}.ls_bak /etc/rc.d/${AP_PROC}
    fi
    if [ -e "/usr/local/etc/rc.d/${AP_PROC}.ls_bak" ] ; then
	mv -f /usr/local/etc/rc.d/${AP_PROC}.ls_bak /usr/local/etc/rc.d/${AP_PROC}
    fi
    exit 0
fi 

INIT_DIR=""
for path in /etc/init.d /etc/rc.d/init.d 
do
    if [ "x$INIT_DIR" = "x" ]; then
        if [ -d "$path" ]; then
            INIT_DIR=$path
        fi
    fi
done

for SYSTEMDDIR in /etc/systemd/system /usr/lib/systemd/system
do
    if [ -d ${SYSTEMDDIR} ] && [ -e ${SYSTEMDDIR}/lshttpd.service ] ; then
	systemctl disable lshttpd.service
        rm -f ${SYSTEMDDIR}/lshttpd.service
        if [ -e ${SYSTEMDDIR}/${AP_PROC}.service.ls_bak ] ; then
           mv -f ${SYSTEMDDIR}/${AP_PROC}.service.ls_bak ${SYSTEMDDIR}/${AP_PROC}.service  
        fi

        systemctl daemon-reload
        echo "[OK] The startup script has been successfully uninstalled from systemd!"
    fi
done

# clean from both systemd and init.d


if [ "${INIT_DIR}" = "" ]; then
    exit 0
fi

if [ -f "/etc/gentoo-release" ]; then
    if [ -e ${INIT_DIR}/lsws ] ; then
    rc-update del lsws default
    rm -f ${INIT_DIR}/lsws
    echo "[OK] The startup script has been successfully uninstalled!"
    fi
    exit 0
fi

if [ -e /etc/debian_version ]; then
    if [ -e ${INIT_DIR}/lsws ] ; then
    update-rc.d lsws remove
    rm -f ${INIT_DIR}/lsws
    echo "[OK] The startup script has been successfully uninstalled!"
    if [ -e "${INIT_DIR}/${AP_PROC}.ls_bak" ] ; then
        mv -f ${INIT_DIR}/${AP_PROC}.ls_bak ${INIT_DIR}/${AP_PROC}
    fi

    fi
    exit 0
fi

if [ -f "${INIT_DIR}/lsws" ]; then
    rm -f ${INIT_DIR}/lsws
    echo "[OK] The startup script has been successfully uninstalled!"
    if [ -e "${INIT_DIR}/${AP_PROC}.ls_bak" ] ; then
	mv -f ${INIT_DIR}/${AP_PROC}.ls_bak ${INIT_DIR}/${AP_PROC}
    fi
fi

if [ -d "$INIT_DIR/rc2.d" ]; then
    INIT_BASE_DIR=$INIT_DIR
else
    INIT_BASE_DIR=`dirname $INIT_DIR`
fi


if [ -d "${INIT_BASE_DIR}/runlevel/default" ]; then
    if [ -e "${INIT_BASE_DIR}/runlevel/default/S88lsws" ] ; then
    rm -f ${INIT_BASE_DIR}/runlevel/default/S88lsws
    fi
    if [ -e "${INIT_BASE_DIR}/runlevel/default/K12lsws" ] ; then
    rm -f ${INIT_BASE_DIR}/runlevel/default/K12lsws
    fi
fi


if [ -d "${INIT_BASE_DIR}/rc2.d" ]; then
    if [ -e "${INIT_BASE_DIR}/rc2.d/S88lsws" ] ; then
    rm -f ${INIT_BASE_DIR}/rc2.d/S88lsws
    fi
    if [ -e "${INIT_BASE_DIR}/rc2.d/K12lsws" ] ; then
    rm -f ${INIT_BASE_DIR}/rc2.d/K12lsws
    fi
fi

if [ -d "${INIT_BASE_DIR}/rc3.d" ]; then
    if [ -e "${INIT_BASE_DIR}/rc3.d/S88lsws" ] ; then
    rm -f ${INIT_BASE_DIR}/rc3.d/S88lsws
    fi
    if [ -e "${INIT_BASE_DIR}/rc3.d/K12lsws" ] ; then
    rm -f ${INIT_BASE_DIR}/rc3.d/K12lsws
    fi
fi

if [ -d "${INIT_BASE_DIR}/rc5.d" ]; then
    if [ -e "${INIT_BASE_DIR}/rc5.d/S88lsws" ] ; then
    rm -f ${INIT_BASE_DIR}/rc5.d/S88lsws
    fi
    if [ -e "${INIT_BASE_DIR}/rc5.d/K12lsws" ] ; then
    rm -f ${INIT_BASE_DIR}/rc5.d/K12lsws
    fi
fi

if [ -d "${INIT_BASE_DIR}/rc0.d" ]; then
    if [ -e "${INIT_BASE_DIR}/rc0.d/K12lsws" ] ; then
    rm -f ${INIT_BASE_DIR}/rc0.d/K12lsws
    fi
fi

if [ -d "${INIT_BASE_DIR}/rc1.d" ]; then
    if [ -e "${INIT_BASE_DIR}/rc1.d/K12lsws" ] ; then
    rm -f ${INIT_BASE_DIR}/rc1.d/K12lsws
    fi
fi

if [ -d "${INIT_BASE_DIR}/rc6.d" ]; then
    if [ -e "${INIT_BASE_DIR}/rc6.d/K12lsws" ] ; then
    rm -f ${INIT_BASE_DIR}/rc6.d/K12lsws
    fi
fi

exit 0
