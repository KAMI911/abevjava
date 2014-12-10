<?php
 /**
 * File utility class
 *
 * PHP version 5
 *
 * @category Library
 * @package  Fileutility
 * @author   Kálmán „KAMI” Szalai <kami911@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl.html GNU General Public License Version 3
 * @version  3.0.4
 * @link     http://ooo.itc.hu/
 */

class File {

    /**
    * Sanitizes a filename replacing whitespace with dashes
    *
    * @param string $string    - The string to be sanitized
    * @return string        - The sanitized filename
    */
    public static function normal_chars($string) {
        $string = htmlentities($string, ENT_QUOTES, 'UTF-8');
        $string = preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|th|tilde|uml);~i', '$1', $string);
        $string = preg_replace(array('~[^0-9a-z]~i', '~-+~'), '-', $string);
        return trim($string);
    }

    /**
     * Get the directory size
     * @param directory $directory    - Path to directory
     * @return int                    - Size of directory in kilobytes
     */
    public static function getDirectorySize($path) {
        $totalsize = 0;
        if ($handle = opendir ($path)) {
            while (false !== ($file = readdir($handle))) {
                $nextpath = $path . '/' . $file;
                if ($file != '.' && $file != '..' && !is_link ($nextpath)) {
                    if (is_dir ($nextpath)) {
                        $result = File::getDirectorySize($nextpath);
                        $totalsize += $result['size'];
                    }
                    elseif (is_file ($nextpath)) {
                        $totalsize += filesize ($nextpath);
                    }
                }
            }
        }
        closedir ($handle);
        return ceil(((int)$totalsize)/1024);
    }
    /**
    * Calculate the size of directory using UNIX tools
    *
    * @param string $path    - Path to directory
    * @return int            - Size of directory in bytes
    */
    public static function DirectorySizeUnix($path) {
        $io = popen ( '/usr/bin/du -sk ' . $path, 'r' );
        $info = fgets ( $io, 4096);
        $info = explode(" ", $info);
        $size = $info[0];
        pclose ( $io );
        return ((int)$size);
    }

    public static function deaccenter($txt) {
        $replaced = array("á" => "a", "Á" => "A", "é" => "e", "É" => "E", 
                  "í" => "i", "Í" => "I", "ó" => "o", "Ó" => "O",
                  "ö" => "o", "Ö" => "O", "ő" => "o", "Ő" => "O",
                  "ú" => "u", "Ú" => "U", "ü" => "u", "Ü" => "U",
                  "ű" => "u", "Ű" => "U", " " => "-", "." => "-",
                  "_" => "-");
        return(strtr($txt,$replaced));
    }
/**
 * Convert string to filename form
 *
 * @param string $filename The filename what we convert
 * @return string returns converted string
 */

    public static function FilenameNormalizer($filename) {
        return(self::normal_chars(self::deaccenter(trim($filename))));
    }
/**
 * Convert string to short_name form
 *
 * @param string $shortname The short_name what we convert
 * @return string returns converted string
 */

    public static function ShortnameNormalizer($shortname) {
        return(mb_strtolower(self::normal_chars(self::deaccenter(trim($shortname))),'UTF-8'));
    }
/**
 * Makes directory, returns TRUE if exists or made
 *
 * @param string $pathname The directory path.
 * @return boolean returns TRUE if exists or made or FALSE on failure.
 */

    public static function mkdir_recursive($pathname, $mode) {
        is_dir(dirname($pathname)) || mkdir_recursive(dirname($pathname), $mode);
        return is_dir($pathname) || @mkdir($pathname, $mode);
    }    

    public static function DownloadFile ($URL, $FileWithPath) {
        File::mkdir_recursive(trim(dirname($FileWithPath)), 770);
        $FilePointer = fopen($FileWithPath, 'w');
        $parts = parse_url($URL);
        $url = $parts['scheme'] . '://' . $parts['host'] . str_replace("//", "/", $parts['path']);
        $CURLHandler = curl_init($url);
        curl_setopt($CURLHandler, CURLOPT_FILE, $FilePointer);
        $error = curl_exec($CURLHandler);
        curl_close($CURLHandler);
        fclose($FilePointer);
        return ($error);
    }

    public static function UnZip ($ZipFile, $DestinationDir) {
        $error = 255; // Unknown error
        $DestinationDir = $DestinationDir . '/';
        $zip = new ZipArchive;
        if ( $zip -> open($ZipFile) === TRUE ) {
            echo '[.] Start ZIP file extraction: ' . $ZipFile . PHP_EOL;
            if ( $zip -> extractTo($DestinationDir) !== TRUE) {
                echo '[!] Extraction ' . $ZipFile . 'ZIP file failed: ' . $zip -> getStatusString() . PHP_EOL;
                $error = 2;
            }
            else {
                echo '[+] Extraction ZIP file OK.' . PHP_EOL;
                $error = 0;
            }        
            $zip -> close();
        }
        else {
            echo '[!] Open ZIP file failed.' . PHP_EOL;
            $error = 1;
        }
        return ($error);
    }
    
    public static function FileNameWithVersion ( $FullPathFilename, $Major, $Minor, $Micro, $Build, $Packing = 0) {
        $path_parts = pathinfo( $FullPathFilename );
        return ( $path_parts['filename'] . '-' . $Major . '.' . $Minor . '.' . $Micro . '.' . $Build . '.' . $path_parts['extension'] );
    }

    public static function FileNameWithVersionAndPacking ( $FullPathFilename, $Major, $Minor, $Micro, $Build, $Packing = 0 ) {
        $path_parts = pathinfo( $FullPathFilename );
        return ( $path_parts['filename'] . '-' . $Major . '.' . $Minor . '.' . $Micro . '.' . $Build . '-' . $Packing . '.' . $path_parts['extension']);
    }

    public static function PackageName ( $FullPathFilename, $PackageType ) {
        $path_parts = pathinfo( $FullPathFilename );
        switch ( $PackageType ) {
            case "1": return( $path_parts['filename'] ); break;
            case "2": return( 'abev-form-' . $path_parts['filename'] ); break;
            case "3": return( 'abev-form-' . $path_parts['filename'] . '-doc' ); break;
            case "4": return( $path_parts['filename'] ); break;
            case "2147483647": return( $path_parts['filename'] ); break;
            default:  return( $path_parts['filename'] );
        }
    }

    public static function PackageNameWithExtension ( $FullPathFilename, $PackageType ) {
        $path_parts = pathinfo( $FullPathFilename );
        switch ( $PackageType ) {
            case "1": return( $path_parts['filename'] . '.' . $path_parts['extension'] ); break;
            case "2": return( 'abev-form-' . $path_parts['filename'] . '.' . $path_parts['extension'] ); break;
            case "3": return( 'abev-form-' . $path_parts['filename'] . '-doc' . '.' . $path_parts['extension'] ); break;
            case "4": return( $path_parts['filename'] . '.' . $path_parts['extension'] ); break;
            case "2147483647": return( $path_parts['filename'] . '.' . $path_parts['extension'] ); break;
            default : return( $path_parts['filename'] . '.' . $path_parts['extension'] );
        }
    }

    public static function PackageNameWithVersionPlatform ( $FullPathFilename, $Major, $Minor, $Micro, $Build, $Packing = 0, $PackageType ) {
        $path_parts = pathinfo( $FullPathFilename );
        switch ( $PackageType ) {
            case "1": return( $path_parts['filename']                . '-' . $Major . '.' . $Minor . '.' . $Micro . '.' . $Build . '-' . $Packing . '_all.'     . $path_parts['extension'] ); break;
            case "2": return( 'abev-form-' . $path_parts['filename'] . '-' . $Major . '.' . $Minor . '.' . $Micro . '.' . $Build . '-' . $Packing . '_all.'     . $path_parts['extension'] ); break;
            case "3": return( 'abev-form-' . $path_parts['filename'] . '-' . $Major . '.' . $Minor . '.' . $Micro . '.' . $Build . '-' . $Packing . '-doc_all.' . $path_parts['extension'] ); break;
            case "4": return( $path_parts['filename']                . '-' . $Major . '.' . $Minor . '.' . $Micro . '.' . $Build . '-' . $Packing . '_all.'     . $path_parts['extension'] ); break;
            case "2147483647": return( $path_parts['filename']       . '-' . $Major . '.' . $Minor . '.' . $Micro . '.' . $Build . '-' . $Packing . '_all.'     . $path_parts['extension'] ); break;
            default:  return( $path_parts['filename']                . '-' . $Major . '.' . $Minor . '.' . $Micro . '.' . $Build . '-' . $Packing . '_all.'     . $path_parts['extension'] );
        }
    }
    public static function CorrectDescription ( $Description ) {
        $replace = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", ".\n", $Description);

        $findlist = array("\n", "\r\n" );
        $replacelist = array("\n ", "\r\n ");
        $Description = str_replace( $findlist, $replacelist, $replace );
        return ( $Description );
    }

    public static function SetRights ( $Direcroty ) {
        $err = 0;
        $err = $err + system('find ' . $Direcroty . '/  -type d -exec chmod 755 {} \;', $output);
        $err = $err + system('find ' . $Direcroty . '/  -type f -exec chmod 644 {} \;', $output);
        return ( $err );
    }

/**
 * Calculate SHA1 sum of a directory's files' SHA1 sum
 *
 * @param string $Pattern    - The directory path with filename mask.
 * @return string            - SHA1 sum of the matching filename masks' SHA1 sum.
 */
    public static function SHA1sumDirectory( $Pattern ) {
        $collector="";
        foreach ( glob( $Pattern ) as $ent ) {
            if ( is_dir( $ent ) ) {
                continue;
            }
            $collector = $collector . sha1_file( $ent );
        }
            return ( sha1($collector) );    
    }

}

?>
