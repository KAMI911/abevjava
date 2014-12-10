#!/bin/sh

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
    mkdir -p ${ADDITIONAL_PATH}/abevjava-resource/eroforrasok/

    echo "[.] Coping resource files ..."
    cp -n ${PACKAGE_TEMP}/application/eroforrasok/*    ${ADDITIONAL_PATH}/abevjava-resource/eroforrasok/ > /dev/null

    echo "[.] Remove resource folder ..."
    rm -fr ${PACKAGE_TEMP}/application/eroforrasok/

fi
