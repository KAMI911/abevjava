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
    mkdir -p ${PACKAGE_DIR}/usr/lib/abevjava
    mkdir -p ${PACKAGE_DIR}/usr/share/abevjava
    mkdir -p ${PACKAGE_DIR}/usr/share/abevjava/nyomtatvanyok
    mkdir -p ${PACKAGE_DIR}/usr/share/doc/abevjava
    mkdir -p ${PACKAGE_DIR}/etc/abevjava/

    echo "[.] Moving files ..."
    mv ${PACKAGE_TEMP}/application/cfg.enyk       ${PACKAGE_DIR}/etc/abevjava/
    mv ${PACKAGE_TEMP}/application/*              ${PACKAGE_DIR}/usr/lib/abevjava/
    mv ${PACKAGE_DIR}/usr/lib/abevjava/segitseg/ ${PACKAGE_DIR}/usr/share/doc/abevjava/segitseg/
    mv ${PACKAGE_DIR}/usr/lib/abevjava/abev/     ${PACKAGE_DIR}/usr/share/abevjava/abev/

    echo "[.] Remove unnecessary files ..."
    find ${PACKAGE_DIR}/ -name ".svn" -type d -exec rm -rf {} \;

    rm -fr ${PACKAGE_DIR}/usr/lib/abevjava/segitseg
    rm -fr ${PACKAGE_DIR}/usr/lib/abevjava/abev
    rm -fr ${PACKAGE_DIR}/usr/lib/abevjava/nyomtatvanyok

    echo "[.] Linking files ..."
    ln -s /usr/share/abevjava/abev          ${PACKAGE_DIR}/usr/lib/abevjava/abev
    ln -s /usr/share/doc/abevjava/segitseg  ${PACKAGE_DIR}/usr/lib/abevjava/segitseg
    ln -s /usr/share/abevjava/nyomtatvanyok ${PACKAGE_DIR}/usr/lib/abevjava/nyomtatvanyok
    ln -s /usr/share/abevjava/eroforrasok   ${PACKAGE_DIR}/usr/lib/abevjava/eroforrasok
    ln -s /etc/abevjava/cfg.enyk            ${PACKAGE_DIR}/usr/lib/abevjava/cfg.enyk

    echo "[.] Coping additional files ..." 
    cp -R ${ADDITIONAL_PATH}/abevjava/etc/    ${PACKAGE_DIR}/
    cp -R ${ADDITIONAL_PATH}/abevjava/usr/    ${PACKAGE_DIR}/
    cp -R ${ADDITIONAL_PATH}/abevjava/DEBIAN/ ${PACKAGE_DIR}/

    echo "[.] Set file rights ..." 
    find ${PACKAGE_DIR}/  -type d -exec chmod 755 {} \;
    find ${PACKAGE_DIR}/  -type f -exec chmod 644 {} \;
    find ${PACKAGE_DIR}/usr/bin/ -name "abev*"  -type f -exec chmod 755 {} \;
    find ${PACKAGE_DIR}/DEBIAN/ -name "*inst"  -type f -exec chmod 755 {} \;
fi