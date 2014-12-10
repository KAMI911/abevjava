<?php
 /**
 * Package class
 *
 * PHP version 5
 *
 * @category Library
 * @package  Package
 * @author   Kálmán „KAMI” Szalai <kami911@gmail.com>
 * @license  ttp://www.gnu.org/licenses/gpl.html GNU General Public License Version 3
 * @version  3.0.4
 * @link     http://ooo.itc.hu/
 */

require_once dirname(__FILE__) . '/file.php';

class Package {

    private $packagedata;
    private $_db;
    private $template;

    public function __construct() {
    }

    public function Open( $database, $externaldata ) {
    $this -> _db = $database;
    $this -> packagedata = $externaldata;
    $this -> template = new Template ( $this -> packagedata['category_id'] );
    $this -> packagedata['filenamewithpath'] = DOWNLOAD_PATH . '/' . File::FileNameWithVersion( $this -> packagedata['filename_1'], $this -> packagedata['version_major'], $this -> packagedata['version_minor'], $this -> packagedata['version_micro'], $this -> packagedata['version_build'] );

    }

    public function View()
    {
        print_r($this -> packagedata);
    }

    public function CreatePackage($target="all", $force = FALSE)
    {
        $this -> packagedata['version_packing'] = $this -> _db -> GetPackingMaxIdFromDownload($this -> packagedata);
        if ( $this -> packagedata['category_id'] == 4)
        {
            echo '[+] Collect resource: ' . $this -> packagedata['short_name'] . ', version: ' .$this -> packagedata['version_major'] . '.' . $this -> packagedata['version_minor'] . '.' . $this -> packagedata['version_micro'] . '.' . $this -> packagedata['version_build'] . PHP_EOL;
            $this->CreateDEB();
        }
        else {
            if ( is_null($this->packagedata['version_packing'])) {
                $this->packagedata['version_packing'] = 0;
                echo '[+] First release: ' . $this->packagedata['short_name'] . ', version: ' .$this -> packagedata['version_major'] . '.' . $this -> packagedata['version_minor'] . '.' . $this -> packagedata['version_micro'] . '.' . $this -> packagedata['version_build'] . ', release: ' . $this->packagedata['version_packing'] . PHP_EOL;
                $this->CreateDEB();
            }
            else {
                $tasklist = $this->_db->GetTaskFromShortName($this->packagedata['short_name'],  $this->packagedata['category_id'], $this->packagedata['company_id']);
                if ( $force || is_array($tasklist) ) {
                    $this->packagedata['version_packing'] = $this->packagedata['version_packing'] + 1;
                    echo '[+] New release: ' . $this->packagedata['short_name'] . ', version: ' .$this -> packagedata['version_major'] . '.' . $this -> packagedata['version_minor'] . '.' . $this -> packagedata['version_micro'] . '.' . $this -> packagedata['version_build'] . ', release: ' . $this->packagedata['version_packing'] . PHP_EOL;
                    if ( $this->CreateDEB() && is_array($tasklist) ) {
                        foreach ( $tasklist as $t ) {
                            if ( $this -> _db -> DeleteTaskFromId ($t) )
                            {
                                echo '[-] Task: ' . $t . '-> |' . $this->packagedata['short_name'] . '|' . $this->packagedata['category_id'] . '|' . $this->packagedata['company_id'] . '| deleted .'. PHP_EOL;
                            }
                            else
                            {
                                echo '[-] Task: ' . $t . '-> |' . $this->packagedata['short_name'] . '|' . $this->packagedata['category_id'] . '|' . $this->packagedata['company_id'] . '| deletion failed .'. PHP_EOL;
                            }
                        }
                    }
                }
                else {
                    $filenamewithpath=OUTPUT_PATH . '/' . File::PackageNameWithVersionPlatform( $this -> packagedata['short_name'] . '.deb', $this -> packagedata['version_major'], $this -> packagedata['version_minor'], $this -> packagedata['version_micro'], $this -> packagedata['version_build'], $this -> packagedata['version_packing'], $this -> packagedata['category_id'] );
                    if ( file_exists( $filenamewithpath ) ) {
                        echo '[.] Old release: ' . $this->packagedata['short_name'] . ', version: ' .$this -> packagedata['version_major'] . '.' . $this -> packagedata['version_minor'] . '.' . $this -> packagedata['version_micro'] . '.' . $this -> packagedata['version_build'] . ', release: ' . $this->packagedata['version_packing'] . PHP_EOL;
                    }
                    else
                    {
                        echo '[+] Renew release: ' . $this->packagedata['short_name'] . ', version: ' .$this -> packagedata['version_major'] . '.' . $this -> packagedata['version_minor'] . '.' . $this -> packagedata['version_micro'] . '.' . $this -> packagedata['version_build'] . ', release: ' . $this->packagedata['version_packing'] . PHP_EOL;
                        $this->CreateDEB();
                    }
                }
            }
        }
    }

    public function CreateDEB() {
        $this -> packagedata['temppackagefile'] = TEMP_PATH . 'deb/' . File::PackageName( $this -> packagedata['short_name'], $this -> packagedata['category_id']);
            if ($this -> packagedata['temppackagefile'] == "") { die ("Veszélyesek lennénk a rendszerre..."); }
            $unziperror=255;
            $err2=255;
            $packagetemp = $this -> packagedata['temppackagefile'] . '/_UNPACK_/';
            $packagedir  = $this -> packagedata['temppackagefile'];
            switch ($this -> packagedata['category_id']) {
                case "1":
                    $unziperror = File::UnZip( $this -> packagedata['filenamewithpath'],  $packagetemp);
                    // TODO: ez kiszervezhetnénk egy letöltőfüggvénybe
                    if ( $unziperror != 0) {
                        print_r($this -> packagedata);
                        echo '[+] Redownloading  - short name: ' . $this -> packagedata['short_name'] . ', from: ' . $this -> packagedata['url_1'] . ', to: ' . $this -> packagedata['filenamewithpath'] . PHP_EOL;
                        if (File::DownloadFile( $this -> packagedata['url_1'], $this -> packagedata['filenamewithpath'] ) === false ){
                            $err = system('rm  ' . $this -> packagedata['filenamewithpath'], $output);
                            echo '[+] Redownload ERROR - short name: ' . $this -> packagedata['short_name'] . ', from: ' . $this -> packagedata['url_1'] . PHP_EOL;
                        }
                        else
                        {
                            echo '[+] Redownload SUCCESS - short name: ' . $this -> packagedata['short_name'] . ', from: ' . $this -> packagedata['url_1'] . PHP_EOL;
                            $unziperror = File::UnZip( $this -> packagedata['filenamewithpath'],  $packagetemp);
                            if ( $unziperror != 0) {
                                continue;
                            }
                        }
                    } else {
                        system( dirname(__FILE__) . '/../script/copy-abevresource.sh "' . $packagedir . '" "' . $packagetemp . '" "' . ADDITIONAL_PATH . '"' ,$output);
                        $err2 = system( dirname(__FILE__) . '/../script/create-abevjava.sh "' . $packagedir . '" "' . $packagetemp . '" "' . ADDITIONAL_PATH . '"' ,$output);
                    }
                    echo "$output";
                    $err1 = system('rm -fr ' . $packagetemp  ,$output);
                    break;
                case "2":
                    $unziperror = File::UnZip( $this -> packagedata['filenamewithpath'],  $packagetemp);
                    // TODO: ez kiszervezhetnénk egy letöltőfüggvénybe
                    if ( $unziperror != 0) {
                        print_r($this -> packagedata);
                        echo '[+] Redownloading  - short name: ' . $this -> packagedata['short_name'] . ', from: ' . $this -> packagedata['url_1'] . ', to: ' . $this -> packagedata['filenamewithpath'] . PHP_EOL;
                        if (File::DownloadFile( $this -> packagedata['url_1'], $this -> packagedata['filenamewithpath'] ) === false ){
                            $err = system('rm  ' . $this -> packagedata['filenamewithpath'], $output);
                            echo '[+] Redownload ERROR - short name: ' . $this -> packagedata['short_name'] . ', from: ' . $this -> packagedata['url_1'] . PHP_EOL;
                        }
                        else
                        {
                            echo '[+] Redownload SUCCESS - short name: ' . $this -> packagedata['short_name'] . ', from: ' . $this -> packagedata['url_1'] . PHP_EOL;
                            $unziperror = File::UnZip( $this -> packagedata['filenamewithpath'],  $packagetemp);
                            if ( $unziperror != 0) {
                                continue;
                            }
                        }
                    } else {
                        system( dirname(__FILE__) . '/../script/copy-abevresource.sh "' . $packagedir . '" "' . $packagetemp . '" "' . ADDITIONAL_PATH . '"' ,$output);
                        $err2 = system( dirname(__FILE__) . '/../script/create-abevtemplate.sh "' . $packagedir . '" "' . $packagetemp . '" "' . ADDITIONAL_PATH . '"' ,$output);
                    }
                    echo "$output";
                    $err1 = system('rm -fr ' . $packagetemp  ,$output);
                    break;
                case "3":
                    $unziperror = File::UnZip( $this -> packagedata['filenamewithpath'],  $packagetemp);
                    // TODO: ez kiszervezhetnénk egy letöltőfüggvénybe
                    if ( $unziperror != 0) {
                        print_r($this -> packagedata);
                        echo '[+] Redownloading  - short name: ' . $this -> packagedata['short_name'] . ', from: ' . $this -> packagedata['url_1'] . ', to: ' . $this -> packagedata['filenamewithpath'] . PHP_EOL;
                        if (File::DownloadFile( $this -> packagedata['url_1'], $this -> packagedata['filenamewithpath'] ) === false ){
                            $err = system('rm  ' . $this -> packagedata['filenamewithpath'], $output);
                            echo '[+] Redownload ERROR - short name: ' . $this -> packagedata['short_name'] . ', from: ' . $this -> packagedata['url_1'] . PHP_EOL;
                        }
                        else
                        {
                            echo '[+] Redownload SUCCESS - short name: ' . $this -> packagedata['short_name'] . ', from: ' . $this -> packagedata['url_1'] . PHP_EOL;
                            $unziperror = File::UnZip( $this -> packagedata['filenamewithpath'],  $packagetemp);
                            if ( $unziperror != 0) {
                                continue;
                            }
                        }
                    } else {
                        system( dirname(__FILE__) . '/../script/copy-abevresource.sh "' . $packagedir . '" "' . $packagetemp . '" "' . ADDITIONAL_PATH . '"' ,$output);
                        $err2 = system( dirname(__FILE__) . '/../script/create-abevhelp.sh "' . $packagedir . '" "' . $packagetemp . '" "' . ADDITIONAL_PATH . '"' ,$output);
                    }
                    echo "$output";
                    $err1 = system('rm -fr ' . $packagetemp  ,$output);
                    break;
                case "4":
                    $err2=0;
                    $unziperror = File::UnZip( $this -> packagedata['filenamewithpath'],  $packagetemp);
                    if ( $unziperror != 0) {
                        echo '[+] Redownloading  - short name: ' . $this -> packagedata['short_name'] . ', from: ' . $this -> packagedata['url'] . ', to: ' . $this -> packagedata['file'] . PHP_EOL;
                        $filenamewithpath=DOWNLOAD_PATH . '/' . File::FileNameWithVersion( $this -> packagedata['file'], $this -> packagedata['version_major'], $this -> packagedata['version_minor'], $this -> packagedata['version_micro'], $this -> packagedata['version_build'] );
                        $fullURL = $v['url'] . '/' . $v['file'];
                        $parts = parse_url($fullURL);
                        $url = $parts['scheme'].'://'.$parts['host'].$parts['path'];
                        if (File::DownloadFile($url, $filenamewithpath) === false ){
                            $err = system('rm  ' . $filenamewithpath, $output);
                            echo '[+] Redownload ERROR - short name: ' . $record['short_name'] . ', from: ' . $record['url'] . PHP_EOL;
                        } else {
                            echo '[+] Redownload SUCCESS - short name: ' . $record['short_name'] . ', from: ' . $record['url'] . PHP_EOL;
                            $unziperror = File::UnZip( $this -> packagedata['filenamewithpath'],  $packagetemp);
                            if ( $unziperror != 0) {
                                continue;
                            }
                        }
                    } else {
                        system( dirname(__FILE__) . '/../script/copy-abevresource.sh "' . $packagedir . '" "' . $packagetemp . '" "' . ADDITIONAL_PATH . '"' ,$output);
                    }
                    echo "$output";
                    $err1 = system('rm -fr ' . $packagetemp  ,$output);
                    break;
                case "2147483647":
                    $unziperror = 0;
                    $err2 = system( dirname(__FILE__) . '/../script/create-resource.sh "' . $packagedir . '" "' . $packagetemp . '" "' . ADDITIONAL_PATH . '"' ,$output);
                    break;
                default:
                    $err2=254;
                    $unziperror = 254;
                }
        if ( $unziperror == 0 && $err2 == 0 ) {
            if ( $this -> packagedata['category_id'] != "4" ) {
                if ( $this -> packagedata["description"] == '' ) $this -> packagedata["description"] = '.';
                $this -> packagedata['size'] = ceil((File::DirectorySizeUnix($packagedir)+1024));
                $findlist = array( '{short_name}', '{version_major}', '{version_minor}', '{version_micro}', '{version_build}',
                                   '{version_packing}', '{package_size}', '{base_url}', '{description}', '{company_name}',
                                   '{maintainer_name}', '{maintainer_email}' );
                $replacelist = array( $this -> packagedata['short_name']   , $this -> packagedata['version_major'],
                              $this -> packagedata['version_minor'], $this -> packagedata['version_micro'],
                              $this -> packagedata['version_build'], $this -> packagedata['version_packing'],
                              $this -> packagedata['size']         , $this -> packagedata['url_1'],
                              $this -> packagedata['description'] , $this -> packagedata['company_name'],
                              MAINTAINER_NAME, MAINTAINER_EMAIL );

                echo '[+] DEB Release: ' . $this->packagedata['short_name'] . ', version: ' .$this -> packagedata['version_major'] . '.' . $this -> packagedata['version_minor'] . '.' . $this -> packagedata['version_micro'] . '.' . $this -> packagedata['version_build'] . ', release: ' . $this -> packagedata['version_packing'] . PHP_EOL;

                $this -> template -> ParseVariables( $findlist, $replacelist );
                // $this -> template -> RenderToConsole();
                $this -> template -> RenderToFile(  $packagedir . '/DEBIAN/control' );

                $err = system('fakeroot dpkg-deb --build ' . $packagedir, $output);
                system('rm -fr ' . $packagedir ,$output);                
                $err = system('mv ' . TEMP_PATH . 'deb/' . File::PackageNameWithExtension( $this -> packagedata['short_name'] . '.deb', $this -> packagedata['category_id']) . ' ' . OUTPUT_PATH . '/' . File::PackageNameWithVersionPlatform( $this -> packagedata['short_name'] . '.deb', $this -> packagedata['version_major'], $this -> packagedata['version_minor'], $this -> packagedata['version_micro'], $this -> packagedata['version_build'], $this -> packagedata['version_packing'], $this -> packagedata['category_id']), $output);
                if ( $err !== false ) {
                    if ( $this->_db->InsertPackageRelease($this -> packagedata) ) {
                        return true;
                    }
                    else {
                        echo '[!] Error occured during ' . $this -> packagedata['short_name'] . ' package insertion to the database. Skipping ...';
                        return false;
                    }
                }
                else {
                    echo '[!] Error occured during ' . $this -> packagedata['short_name'] . ' package creation. Removing package ...';
                    $err = system('rm ' . OUTPUT_PATH . '/' . File::PackageNameWithVersionPlatform( $this -> packagedata['short_name'] . '.deb', $this -> packagedata['version_major'], $this -> packagedata['version_minor'], $this -> packagedata['version_micro'], $this -> packagedata['version_build'], $this -> packagedata['version_packing'], $this -> packagedata['category_id']), $output);
                    return false;
                }
            }
        }
        else {
            echo '[+] UnZIP or package preparation error: ' . $this->packagedata['short_name'] . ', version: ' .$this -> packagedata['version_major'] . '.' . $this -> packagedata['version_minor'] . '.' . $this -> packagedata['version_micro'] . '.' . $this -> packagedata['version_build'] . ', release: ' . $this -> packagedata['version_packing'] . PHP_EOL;                
            return false;
        }
    }

    public function Close() {
        $this -> __destruct();
    }    

    public function __destruct() {
    }

}

?>

