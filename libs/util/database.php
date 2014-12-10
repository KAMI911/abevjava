<?php
 /**
 * Database handler class
 *
 * PHP version 5
 *
 * @category Library
 * @package  Database
 * @author   Kálmán „KAMI” Szalai <kami911@gmail.com>
 * @license  ttp://www.gnu.org/licenses/gpl.html GNU General Public License Version 3
 * @version  3.0.4
 * @link     http://ooo.itc.hu/
 */

class Database {

    /**
     * Database connection
     *
     * @var array[PDOConnection]
     */

const DB_CHARSET = 'utf8';

    protected $conn;

    public function __construct( $dbhost , $dbport , $dbusername , $dbpassword , $dbdatabase ) {
        try {
            echo '[.] Connecting to database ' . $dbdatabase . ' ...' . PHP_EOL;
            $dsn = "mysql:host=$dbhost;dbname=$dbdatabase";
            $opt = array
            (
            // any occurring errors wil be thrown as PDOException
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // an SQL command to execute when connecting
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                PDO::ATTR_EMULATE_PREPARES => FALSE
            );
            $this->conn = new PDO( $dsn , $dbusername , $dbpassword , $opt );
        }  catch (PDOException $error) {
            throw new Exception('[!] Could not connect to the database ' . $dbdatabase . ': ' . $error->getMessage() . PHP_EOL);
        }
    }
    
    public function GetCompaniesArray() {
        try {
            $sql = '
                SELECT `coid`, `company_short_name`, `url` 
                FROM `company` 
                WHERE `state` = "enable"';
            $select = $this->conn->prepare($sql);
            $select->execute();
            $companies = $select->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $error) {
            throw new Exception('[!] Could not get the list of companies: ' . $error->getMessage() . PHP_EOL);
        }
        return($companies);
    }

    public function GetCategoriesArray() {
        try {
            $sql = '
                SELECT `caid`, `category_name` 
                FROM `category`';
            $select = $this->conn->prepare($sql);
            $select->execute();
            $categories = $select->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $error) {
            throw new Exception('[!] Could not get the list of categories: ' . $error->getMessage() . PHP_EOL);
        }

        foreach($categories as $k => $v) {
            if ( $v == "category_name") {
                $categories[$k]['category_name'] = mb_strtolower($categories[$k]['category_name'], 'UTF-8');
            }
        }
        return($categories);
    }

    public function GetCategoryIdFromCategory($category)
    {
        try
        {
            $sql = '
                SELECT `caid` FROM `category` 
                WHERE `category_name` = :category_name';
            $select = $this -> conn -> prepare($sql);
            $select -> bindParam( ':category_name' , mb_strtolower(trim($category),'UTF-8') , PDO::PARAM_STR , 32 );
            $select -> execute();
            $categoryid = $select->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $error)
        {
            throw new Exception('[!] Could not get the category id: ' . $error->getMessage() . PHP_EOL);
        }
        return($categoryid);
    }

    public function GetCompanyIdFromCompany($company)
    {
        try
        {
            $sql = '
                SELECT `coid` FROM `company` 
                WHERE `company_short_name` = :company_short_name';
            $select = $this->conn->prepare($sql);
            $select -> bindParam( ':company_short_name' , mb_strtolower(trim($company),'UTF-8') , PDO::PARAM_STR , 32 );
            $select->execute();
            $companyid = $select->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $error)
        {
            throw new Exception('[!] Could not get the company id: ' . $error->getMessage() . PHP_EOL);
        }
        return($companyid);
    }
    
    public function GetPackageIdFromPackage($package, $category, $company)
    {
        try
        {
            $sql = '
                SELECT `paid` FROM `package` 
                WHERE `short_name` = :short_name
                AND `category_id`  = :category_id
                AND `company_id`   = :company_id;';


            $select = $this->conn->prepare($sql);
            $select -> bindParam( ':short_name'  , mb_strtolower(trim($package),'UTF-8') , PDO::PARAM_STR , 64 );
            $select -> bindParam( ':category_id' , $category , PDO::PARAM_INT);
            $select -> bindParam( ':company_id'  , $company  , PDO::PARAM_INT);
            $select -> execute();
            $packageid = $select -> fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $error)
        {
            throw new Exception('[!] Could not get the package id: ' . $error->getMessage() . PHP_EOL);
        }
        return($packageid);
    }

    public function GetDownloadIdFromDownload($package, $category, $company, $version_major, $version_minor, $version_micro, $version_build)
    {
        try
        {
            $sql = '
                SELECT `doid` FROM `download` 
                WHERE `package_id`  = :package_id 
                AND `category_id`   = :category_id 
                AND `company_id`    = :company_id 
                AND `version_major` = :version_major 
                AND `version_minor` = :version_minor 
                AND `version_micro` = :version_micro 
                AND `version_build` = :version_build';
            $select = $this->conn->prepare($sql);
            $select -> bindParam( ':package_id'     , $package        , PDO::PARAM_INT);
            $select -> bindParam( ':category_id'    , $category       , PDO::PARAM_INT);
            $select -> bindParam( ':company_id'     , $company        , PDO::PARAM_INT);
            $select -> bindParam( ':version_major'  , $version_major  , PDO::PARAM_INT);
            $select -> bindParam( ':version_minor'  , $version_minor  , PDO::PARAM_INT);
            $select -> bindParam( ':version_micro'  , $version_micro  , PDO::PARAM_INT);
            $select -> bindParam( ':version_build'  , $version_build  , PDO::PARAM_INT);
            $select->execute();
            $downloadid = $select->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $error)
        {
            throw new Exception('[!] Could not get the download id: ' . $error->getMessage() . PHP_EOL);
        }
        return($downloadid);
    }

    public function GetPackingMaxIdFromDownload($record)
    {
        try
        {
            $sql = '
                SELECT MAX(`version_packing`) AS `version_packing` FROM `package_release` 
                WHERE `download_id` = :download_id 
                AND `package_id`    = :package_id 
                AND `category_id`   = :category_id 
                AND `company_id`    = :company_id 
                AND `version_major` = :version_major 
                AND `version_minor` = :version_minor 
                AND `version_micro` = :version_micro 
                AND `version_build` = :version_build';
            $select = $this->conn->prepare($sql);
            $select -> bindParam( ':download_id'   , $record['doid']           , PDO::PARAM_INT);
            $select -> bindParam( ':package_id'    , $record['package_id']     , PDO::PARAM_INT);
            $select -> bindParam( ':category_id'   , $record['category_id']    , PDO::PARAM_INT);
            $select -> bindParam( ':company_id'    , $record['company_id']     , PDO::PARAM_INT);
            $select -> bindParam( ':version_major' , $record['version_major']  , PDO::PARAM_INT);
            $select -> bindParam( ':version_minor' , $record['version_minor']  , PDO::PARAM_INT);
            $select -> bindParam( ':version_micro' , $record['version_micro']  , PDO::PARAM_INT);
            $select -> bindParam( ':version_build' , $record['version_build']  , PDO::PARAM_INT);
            $select->execute();
            $packageid = $select->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $error)
        {
            throw new Exception('[!] Could not get the max packing id: ' . $error->getMessage() . PHP_EOL);
        }
        return($packageid['version_packing']);
    }
    
    public function GetDownloadArray()
    {
        try
        {
            $sql = '
                SELECT `download`.`doid` , `download`.`company_id` , `company`.`company_name` , `company`.`company_name` , 
                    `company`.`url` , `download`.`package_id` , `download`.`category_id` , `download`.`version_major` , 
                    `download`.`version_minor` , `download`.`version_micro` , `download`.`version_build` , 
                    `download`.`url_1` , `download`.`filename_1` , `package`.`short_name` , `package`.`description` 
                FROM `download` 
                INNER JOIN `package` ON `download`.`package_id` = `package`.`paid` 
                INNER JOIN `company` ON `download`.`company_id` = `company`.`coid`
                WHERE `doid` = (
                    SELECT   `doid`
                    FROM     download AS lookup
                    WHERE    lookup.`package_id` = download.`package_id` 
                    ORDER BY `version_major` DESC, `version_minor` DESC, `version_micro` DESC, `version_build` DESC
                    LIMIT    1
                )
                ORDER BY `download`.`package_id`';
            $select = $this->conn->prepare($sql);
            $select->execute();
            $download = $select->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $error)
        {
            throw new Exception('[!] Could not get the downloads: ' . $error->getMessage() . PHP_EOL);
        }
        return($download);
    }

    public function GetDownloadFromShortName($package, $category, $company)
    {
        $packageid = $this->GetPackageIdFromPackage($package, $category, $company);
        try
        {
            $sql = '
                SELECT `download`.`doid` , `download`.`company_id` , `company`.`company_name` , `company`.`company_name` , 
                    `company`.`url` , `download`.`package_id` , `download`.`category_id` , `download`.`version_major` , 
                    `download`.`version_minor` , `download`.`version_micro` , `download`.`version_build` , 
                    `download`.`url_1` , `download`.`filename_1` , `package`.`short_name` , `package`.`description` 
                FROM `download` 
                INNER JOIN `package` ON `download`.`package_id` = `package`.`paid` 
                INNER JOIN `company` ON `download`.`company_id` = `company`.`coid`
                WHERE `package_id`=:package_id';
            $select = $this->conn->prepare($sql);
            $select -> bindParam( ':package_id' , $packageid['paid'] , PDO::PARAM_INT);
            $select -> execute();
            $download = $select->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $error)
        {
            throw new Exception('[!] Could not get the download from shortname: ' . $error->getMessage() . PHP_EOL);
        }
        return($download);
    }

    public function GetTaskFromShortName($package, $category, $company)
    {
        try
        {
            $sql = '
                SELECT `taid` FROM `task` 
                WHERE `short_name` LIKE :short_name
                AND `category_id`  = :category_id
                AND `company_id`   = :company_id;';
            $select = $this->conn->prepare($sql);
            $select -> bindParam( ':short_name' , mb_strtolower(trim($package),'UTF-8') , PDO::PARAM_STR , 32 );
            $select -> bindParam( ':category_id' , $category , PDO::PARAM_INT);
            $select -> bindParam( ':company_id'  , $company  , PDO::PARAM_INT);
            $select -> execute();
            $taskid = $select->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $error)
        {
            throw new Exception('[!] Could not get the task id from shortname: ' . $error->getMessage() . PHP_EOL);
        }
        return($taskid);
    }

    public function DeleteTaskFromId($taskid)
    {
        try
        {
            $sql = '
                DELETE FROM `task`
                WHERE `taid` = :taid';
            $delete = $this->conn->prepare($sql);
            $delete -> bindParam( ':taid' , $taskid , PDO::PARAM_INT);
            $delete -> execute();
        }
        catch(PDOException $error)
        {
            throw new Exception('[!] Could not get the task id from shortname: ' . $error->getMessage() . PHP_EOL);
        }
        return($delete);
    }

    public function InsertPackage($record)
    {
        try {
            $sql = '
                INSERT INTO package ( `short_name` , `company_id` , `category_id` , `description` )
                VALUES              ( :short_name  , :company_id  , :category_id  , :description  )';
            $insert = $this->conn->prepare($sql);
            $insert -> bindParam( ':short_name'  , $record['short_name']  , PDO::PARAM_STR , 64);
            $insert -> bindParam( ':company_id'  , $record['company_id']  , PDO::PARAM_INT);
            $insert -> bindParam( ':category_id' , $record['category_id'] , PDO::PARAM_INT);
            $insert -> bindParam( ':description' , $record['description'] , PDO::PARAM_STR , 16777215);
            $insert->execute();


        }
        catch(PDOException $error) {
            throw new Exception('[!] Could not insert package: ' . $error->getMessage() . PHP_EOL);
        }
        return($insert);
    }
    
    public function InsertDownload($record)
    {
        try {
            $sql = '
                INSERT INTO `download` ( `package_id` , `company_id` , `category_id` , `version_major` , `version_minor` , `version_micro` , `version_build` , `url_1` , `filename_1` ) 
                VALUES                 ( :package_id  , :company_id  , :category_id  , :version_major  , :version_minor  , :version_micro  , :version_build  , :url_1  , :filename_1  )';
            $insert = $this->conn->prepare($sql);
            $insert -> bindParam( ':package_id'    , $record['package_id']     , PDO::PARAM_INT);
            $insert -> bindParam( ':company_id'    , $record['company_id']     , PDO::PARAM_INT);
            $insert -> bindParam( ':category_id'   , $record['category_id']    , PDO::PARAM_INT);
            $insert -> bindParam( ':version_major' , $record['version_major']  , PDO::PARAM_INT);
            $insert -> bindParam( ':version_minor' , $record['version_minor']  , PDO::PARAM_INT);
            $insert -> bindParam( ':version_micro' , $record['version_micro']  , PDO::PARAM_INT);
            $insert -> bindParam( ':version_build' , $record['version_build']  , PDO::PARAM_INT);
            $insert -> bindParam( ':url_1'         , $record['url_1']          , PDO::PARAM_STR , 256);
            $insert -> bindParam( ':filename_1'    , $record['filename_1']     , PDO::PARAM_STR , 256);
            $insert -> execute();
                
        }
        catch(PDOException $error)
        {
            throw new Exception('[!] Could not insert download: ' . $error->getMessage() . PHP_EOL);
        }
        return($insert);
    }

    public function InsertPackageRelease($record)
    {
        try {
            $sql = '
                INSERT INTO `package_release` ( `package_id` , `company_id` , `category_id` , `download_id` , `version_major` , `version_minor` , `version_micro` , `version_build` , `version_packing` ) 
                VALUES                        ( :package_id  , :company_id  , :category_id  , :download_id  , :version_major  , :version_minor  , :version_micro  , :version_build  , :version_packing  )';
            $insert = $this->conn->prepare($sql);
            $insert -> bindParam( ':package_id'      , $record['package_id']       , PDO::PARAM_INT);
            $insert -> bindParam( ':company_id'      , $record['company_id']       , PDO::PARAM_INT);
            $insert -> bindParam( ':category_id'     , $record['category_id']      , PDO::PARAM_INT);
            $insert -> bindParam( ':download_id'     , $record['doid']             , PDO::PARAM_INT);
            $insert -> bindParam( ':version_major'   , $record['version_major']    , PDO::PARAM_INT);
            $insert -> bindParam( ':version_minor'   , $record['version_minor']    , PDO::PARAM_INT);
            $insert -> bindParam( ':version_micro'   , $record['version_micro']    , PDO::PARAM_INT);
            $insert -> bindParam( ':version_build'   , $record['version_build']    , PDO::PARAM_INT);
            $insert -> bindParam( ':version_packing' , $record['version_packing']  , PDO::PARAM_INT);
            $insert -> execute();

        }
        catch(PDOException $error) {
            throw new Exception('[!] Could not insert release: ' . $error->getMessage() . PHP_EOL);
        }
        return($insert);
    }
//SELECT doid, MAX(version_major),  MAX(version_minor), MAX(version_micro) FROM  `download` GROUP BY doid
    public function __destruct() {
        $this->conn=null;
    }

}

?>

