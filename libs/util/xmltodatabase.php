<?php
/**
 * XML to Database class
 *
 * PHP version 5
 *
 * @category Library
 * @package  Xmltodatabase
 * @author   Kálmán „KAMI” Szalai <kami911@gmail.com>
 * @license  ttp://www.gnu.org/licenses/gpl.html GNU General Public License Version 3
 * @version  3.0.6
 * @link     http://ooo.itc.hu/
 */

require_once dirname(__FILE__) . '/database.php';

class XMLtoDataBase {
    
    private $_FormData;
    private $_Controll;    
    private $_Index;
    // company related data
    private $_companies;
    private $_categories;

    // structured array of companies provided package data
    private $_form_data;
    
    // database handler
    private $_db;

    public function __construct($host, $port, $username, $password, $database) {
        try {
            $this->_db = new Database($host, $port, $username, $password, $database);
        }
        catch (Exception $e) {
            echo 'Could not connect to database: ' . $e->getMessage();
            exit(1);
        }
        echo '[.] Loading company info ...' . PHP_EOL;
        $this->_companies = $this->_db->GetCompaniesArray();
        echo '[.] Loading category info ...' . PHP_EOL;
        $this->_categories = $this->_db->GetCategoriesArray();
    }

    public function import() {
        global $_Controll;
        echo '[.] Downloading company XML files ...' . PHP_EOL;
        if ( FORCE_XML_DOWNLOAD === TRUE){
            $this->_DownloadFile();
        }
        else {
            $this->_OfflineFile();
        }
        echo '[.] Parsing XML files ...' . PHP_EOL;
        $this->_ParseXML();
        echo '[.] Prepare data to put into database ...' . PHP_EOL;
        $this->_PrepareData();
        echo '[+] Load data into database ...' . PHP_EOL;
        $this->_LoadToDataBase();
    }

    public function put() {
        echo '[+] Load data into database ...' . PHP_EOL;
        $this->_LoadToDataBase();
    }

/**
 * Returns the company id from company short name
 *
 * @param none
 * @using string of companiy_short_name
 * @return none
 */    
    public function GetCompanyId($company_short_name) {
        return ($this->_db->GetCompanyIdFromCompany($company_short_name));
    }

/**
 * Download companies' XML file to the XML_PATH folder
 *
 * @param none
 * @using array of _companies
 * @return none
 */    
    private function _DownloadFile() {
        foreach ($this->_companies as $Row => $CompanyData)    {
            $CurrentFilename = File::FilenameNormalizer( 'abevjava-' . $this->_companies[$Row]['company_short_name'] . '-' . date('y-m-j-h-i-s') . '.xml');
            $CurrentFilenameWithPath = XML_PATH . '/' . File::FilenameNormalizer( 'abevjava-' . $this->_companies[$Row]['company_short_name'] . '-' . date('y-m-j-h-i-s') . '.xml');
            $GeneralFilenameWithPath = XML_PATH . '/' . File::FilenameNormalizer( 'abevjava-' . $this->_companies[$Row]['company_short_name'] . '.xml');
            
            echo '[+] Save ' . $this->_companies["$Row"]['company_short_name'] . ' company\'s remote file: \'' . $this->_companies["$Row"]['url'] . '\' to \'' . $CurrentFilenameWithPath . '\' file.' . PHP_EOL;
            File::DownloadFile($this->_companies["$Row"]['url'], $CurrentFilenameWithPath );
            // Save copy of file for OfflineFile() usage, the last XML file is always saved twice 
            $err = system('cp ' . $CurrentFilenameWithPath . ' ' . $GeneralFilenameWithPath , $output);
            $this->_companies[$Row]['filename'] = $GeneralFilenameWithPath;
        }
    }

/**
 * Use offline companies' XML file from the XML_PATH folder
 *
 * @param none
 * @using array of _companies
 * @return none
 */    
    private function _OfflineFile()    {
        foreach ($this->_companies as $Row => $CompanyData)    {
            $GeneralFilenameWithPath = XML_PATH . '/' . File::FilenameNormalizer( 'abevjava-' . $this->_companies[$Row]['company_short_name'] . '.xml');
            $this->_companies[$Row]['filename'] = $GeneralFilenameWithPath;
        }
    }
/**
 * Use offline companies' XML file from the XML_PATH folder
 *
 * @param $ArrayToCorrect
 * @return none
 */ 
    private function _IterateArray($ArrayToCorrect)    {
        global $_Controll;
        global $_Index;
        foreach($ArrayToCorrect as $k => $v) {
            if(is_array($v)) {
                XMLtoDataBase::_IterateArray($v);
                if ( is_numeric($k) || $k == "keretprogram" ) {
                    $_Index=$_Index+1;
                };
            }
            else {
                switch ($k) {
                    case "0":
                        $_Controll[$_Index]['file'] = trim(basename($v));
                        $_Controll[$_Index]['url'] .= trim(dirname($v));
                        break;
                    case "1":
                        break;
                    case "files":
                        $_Controll[$_Index]['file'] = trim(basename($v));
                        $_Controll[$_Index]['url'] .= trim(dirname($v));
                        break;
                    case "verzio":
                        $version_all = explode('.', $v);
                        if ( ! isset($version_all['0']) ) {
                            $_Controll[$_Index]['version_major'] = 0;
                        }
                        else {
                            $_Controll[$_Index]['version_major'] = $version_all['0'];
                        }
                        if ( ! isset($version_all['1']) ) {
                            $_Controll[$_Index]['version_minor'] = 0;
                        }
                        else {
                            $_Controll[$_Index]['version_minor'] = $version_all['1'];
                        }
                        if ( ! isset($version_all['2']) ) {
                            $_Controll[$_Index]['version_micro'] = 0;
                        }
                        else {
                            $_Controll[$_Index]['version_micro'] = $version_all['2'];
                        }
                        if ( ! isset($version_all['3']) ) {    
                            $_Controll[$_Index]['version_build'] = 0;
                        }
                        else {
                            $_Controll[$_Index]['version_build'] = $version_all['3'];
                        }
                        break;
                    default:
                        $_Controll[$_Index][$k] = trim($v);
                }
            }
        }
    }
        
    private function _ParseXML() {
        global $_Controll;
        foreach ($this->_companies as $Row => $CompanyData) {
            // TODO meg kellene vizsgálni hogy létezik-e a fájl, s ha nem akkor meg kéne próbálni letölteni
            $TemporaryArray[$Row] = XML::xml2array( $this->_companies[$Row]['filename'] );
        }
        $this->_IterateArray($TemporaryArray);
 
        $this->_FormData=$_Controll;
        unset($_Controll);
    }

    public function AddRecord($data) {
        global $_Index;
        $parts = parse_url($data['url']);
        $url = $parts['scheme'].'://'.$parts['host'].$parts['path'];
        $this->_FormData[$_Index]['url'] = $url;
        if ( substr($this->_FormData[$_Index]['url'], 0, 4) != "http" ) {
            $this->_FormData[$_Index]['url'] = "http://" . $this->_FormData[$_Index]['url'];
        }

        $this->_FormData[$_Index]['file'] = basename($data['url']);

        if ( isset($data['description']) ) {
            $this->_FormData[$_Index]["description"] = File::CorrectDescription($data['description']);
        }
        else {
            $this->_FormData[$_Index]["description"] = File::CorrectDescription('');
        }
        $this->_FormData[$_Index]['short_name'] = mb_strtolower(File::normal_chars(File::deaccenter(trim($data['short_name']))),'UTF-8');

        $c = $this->_db->GetCompanyIdFromCompany($data['company']);
        $this->_FormData[$_Index]['company_id'] = $c['coid'];
        $this->_FormData[$_Index]['category_id'] = $data['category_id'];

        $this->_FormData[$_Index]['version_major'] = $data['version_major'];
        $this->_FormData[$_Index]['version_minor'] = $data['version_minor'];
        $this->_FormData[$_Index]['version_micro'] = $data['version_micro'];
        $this->_FormData[$_Index]['version_build'] = $data['version_build'];
        $_Index=$_Index+1;
    }

/*    public function QuickCheckData($CheckData) {
        $error = 0;
         if ( ! is_int($CheckData['category_id']) )
        {
            $error = $error + 1;
        }
        if ( ! is_int($CheckData['company_id']) )
        {
            $error = $error + 2;
        }
        if ( ! is_string($CheckData['short_name']) || empty($CheckData['short_name']) )
        {
            $error = $error + 4; 
        }

        // Fix URL's protocol, when it is not mentioned in the URL.
        if ( substr($v['url'], 0, 4) != "http" ) {
            $this->_FormData[$k]['url'] = "http://" . $v['url'];
        }
        if ( isset($v['elnevezes']) ) {
            $this->_FormData[$k]["description"] = File::CorrectDescription($v['elnevezes']);
        }
        else {
            $this->_FormData[$k]["description"] = File::CorrectDescription('');
        }
        if ( $error > 0 )
        {
            return (false);
        } else
        {
            return (true);
        }
        $record['short_name'] = $v['short_name'];
        $record['company_id'] = $v['company_id'];
        $record['category_id'] = $v['category_id'];

        $record['description'] = $v['description'];

        $record['version_major'] = $v['version_major'];
        $record['version_minor'] = $v['version_minor'];
        $record['version_micro'] = $v['version_micro'];
        $record['version_build'] = $v['version_build'];
    }*/

    public function ViewFormData() {
        print_r($this->_FormData);
    }

    private function _PrepareData() {
        foreach ($this->_FormData as $k => $v) {
            $error = 0;
            if ( ! empty($v['kategoria']) )
            {
                $c = $this->_db->GetCategoryIdFromCategory($v['kategoria']);
                if ( ! empty($c) && ( $c !== false && (int)$c['caid'] >= 0 ) )
                {
                    $this->_FormData[$k]['category_id'] = (int)$c['caid'];
                }
                else
                {
                    $error = $error + 1;
                }
            }
            else
            {
                $error = $error + 64;
            }
            if ( ! empty($v['szervezet']) )
            {
                $c = $this->_db->GetCompanyIdFromCompany($v['szervezet']);
                if ( ! empty($c) && ( $c !== false && (int)$c['coid'] >= 0 ) )
                {
                    $this->_FormData[$k]['company_id'] = (int)$c['coid'];
                }
                else
                {
                    $error = $error + 2; 
                }
            }
            else
            {
                $error = $error + 128;
            }
            if ( ! empty($v['rovidnev']) )
            {
                $this->_FormData[$k]['short_name'] = File::ShortnameNormalizer($v['rovidnev']);
            }
            else
            {
                $error = $error + 4; 
            }
            
            // Fix URL's protocol, when it is not mentioned in the URL.
            if ( ! empty($v['url']) )
            {
                if ( substr($v['url'], 0, 4) != "http" ) {
                    $this->_FormData[$k]['url'] = "http://" . $v['url'];
                }
            }
            else
            {
                $error = $error + 8; 
            }
            if ( ! empty($v['elnevezes']) ) {
                $this->_FormData[$k]["description"] = File::CorrectDescription($v['elnevezes']);
            }
            else {
                $this->_FormData[$k]["description"] = File::CorrectDescription('');
            }
            if ( $error > 0 )
            {
                echo '[-] Skipping errorous dataset (' . $error . '): ';
                print_r ($this->_FormData[$k]);
                unset ($this->_FormData[$k]);
            }
        }
    }

    private function _LoadToDataBase() {
        foreach ($this->_FormData as $k => $v) {
            $record['short_name'] = $v['short_name'];
            $record['company_id'] = $v['company_id'];
            $record['category_id'] = $v['category_id'];

            $record['description'] = $v['description'];

            $record['version_major'] = $v['version_major'];
            $record['version_minor'] = $v['version_minor'];
            $record['version_micro'] = $v['version_micro'];
            $record['version_build'] = $v['version_build'];

            $c = $this->_db->GetPackageIdFromPackage($record['short_name'], $record['category_id'], $record['company_id']);
            if ( ! isset($c['paid']) ) {
                echo '[+] New Package -  short name: ' . $record['short_name'] . ', categotry: ' . $record['category_id'] . ', company: ' . $record['company_id'] . PHP_EOL;

                $d = $this->_db->InsertPackage($record);
            }
            unset($c);
            $c = $this->_db->GetPackageIdFromPackage($record['short_name'], $record['category_id'], $record['company_id']);

            $record['package_id'] = $c['paid'];

            $d = $this->_db->GetDownloadIdFromDownload($c['paid'], $record['category_id'], $record['company_id'], $record['version_major'], $record['version_minor'], $record['version_micro'], $record['version_build']);
            
            // jar filename is: filename-x.y.z.v.jar
            $path_parts = pathinfo($v['file']);
            $filenamewithpath=DOWNLOAD_PATH . '/' . File::FileNameWithVersion( $v['file'], $v['version_major'], $v['version_minor'], $v['version_micro'], $v['version_build'] );
            $fullURL = $v['url'] . '/' . $v['file'];
            $parts = parse_url($fullURL);
            $url = $parts['scheme'].'://'.$parts['host'].$parts['path'];
            $record['url_1'] = $url;
            $record['filename_1'] = basename($url);
            // Never downloaded before
            if ( ! isset($d['doid']) ) {
                echo '[+] New Download - short name: ' . $record["short_name"] . ', categotry: ' . $record["category_id"] . ', company: ' . 
                      $record["company_id"] . ', version: ' . $record['version_major'] . '.' . $record['version_minor'] .  '.' . 
                      $record['version_micro'] .  '.' . $record['version_build'] . ', package id: ' . $record["package_id"] . PHP_EOL;
                if ( FORCE_DOWNLOAD === FALSE && file_exists( $filenamewithpath ) ) {
                    echo '[+] Skipping     - short name: ' . $record['short_name'] . PHP_EOL;
                }
                else {
                    echo '[+] Downloading  - short name: ' . $record['short_name'] . ', from: ' . $record['url_1'] . ', to: ' . $record['filename_1'] . PHP_EOL;
                    if (File::DownloadFile($url, $filenamewithpath) === false ){
                        $err = system('rm  ' . $filenamewithpath, $output);
                        echo '[+] Download ERROR - short name: ' . $record['short_name'] . ', from: ' . $record['url_1'] . PHP_EOL;
                    }
                }
                $d = $this->_db->InsertDownload($record);
                unset($record);
            }
            else {
                if ( ! file_exists( $filenamewithpath )) {
                    echo '[+] Downloading (e) - short name: ' . $record['short_name'] . ', from: ' . $record['url_1'] . ', to: ' . $record['filename_1'] . PHP_EOL;
                    if (File::DownloadFile($url, $filenamewithpath) === FALSE ){
                        $err = system('rm  ' . $filenamewithpath, $output);
                        echo '[+] Download ERROR - short name: ' . $record['short_name'] . ', from: ' . $record['url_1'] . PHP_EOL;
                    }
                }
            }
        }
    }

    public function Close() {
        $this -> __destruct();
    }

    public function __destruct() {
    }
}

?>

