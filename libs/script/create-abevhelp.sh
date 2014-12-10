#!/bin/sh
set -e

if [ $# -ne 3 ]
then
    echo "[!] You have to call this script with 3 parameters!"
    echo "    First  is PACKAGE_DIR"
    echo "    Second is PACKAGE_TEMP"
    echo "    Third  is ADDITIONAL_DIR"
    exit 1
else
    PACKAGE_DIR="${1}"
    PACKAGE_TEMP="${2}"
    ADDITIONAL_PATH="${3}"

    echo "[.] Creating directories ..."
    mkdir -p ${PACKAGE_DIR}/usr/share/doc/abevjava

    echo "[.] Moving files ..."
    mv ${PACKAGE_TEMP}/application/* ${PACKAGE_DIR}/usr/share/doc/abevjava/

    echo "[.] Remove unnecessary files ..."
    find ${PACKAGE_DIR}/ -name ".svn" -type d -exec rm -rf {} \;

    echo "[.] Set file rights ..." 
    find ${PACKAGE_DIR}/  -type d -exec chmod 755 {} \;
    find ${PACKAGE_DIR}/  -type f -exec chmod 644 {} \;
fi
