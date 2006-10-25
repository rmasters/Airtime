<?php
/**
 * StorageServer configuration file
 *
 * @author $Author$
 * @version  $Revision$
 */


define('LS_VERSION', '1.1');
define('PHP5', version_compare( phpversion(), "5.0.0", ">=" ));

/**
 *  configuration structure:
 *
 *  <dl>
 *   <dt>dsn<dd> datasource setting
 *   <dt>tblNamePrefix <dd>prefix for table names in the database
 *   <dt>authCookieName <dd>secret token cookie name
 *   <dt>AdminsGr <dd>name of admin group
 *   <dt>StationPrefsGr <dd>name of station preferences group
 *   <dt>AllGr <dd>name of 'all users' group
 *   <dt>TrashName <dd>name of trash folder (subfolder of the storageRoot)
 *   <dt>storageDir <dd>main directory for storing binary media files
 *   <dt>bufferDir <dd>directory for temporary files
 *   <dt>transDir <dd>directory for incomplete transferred files
 *   <dt>accessDir <dd>directory for symlinks to accessed files
 *   <dt>isArchive <dd>local/central flag
 *   <dt>validate <dd>enable/disable validator
 *   <dt>useTrash <dd>enable/disable safe delete (move to trash)
 *   <dt>storageUrlPath<dd>path-URL-part of storageServer base dir
 *   <dt>storageXMLRPC<dd>XMLRPC server script address relative to storageUrlPath
 *   <dt>storageUrlHost, storageUrlPort<dd>host and port of storageServer
 *   <dt>archiveUrlPath<dd>path-URL-part of archiveServer base dir
 *   <dt>archiveXMLRPC<dd>XMLRPC server script address relative to archiveUrlPath
 *   <dt>archiveUrlHost, archiveUrlPort<dd>host and port of archiveServer
 *   <dt>archiveAccountLogin, archiveAccountPass <dd>account info
 *           for login to archive
 *   <dt>sysSubjs<dd>system users/groups - cannot be deleted
 *  </dl>
 */

// these are the default values for the config

$config = array(
    /* ================================================== basic configuration */
    'dsn'           => array(
        'username'      => 'test',
        'password'      => 'test',
        'hostspec'      => 'localhost',
        'phptype'       => 'pgsql',
        'database'      => 'Campcaster-test',
    ),
    'tblNamePrefix' => 'ls_',

    /* ================================================ storage configuration */
    'authCookieName'=> 'lssid',
    'AdminsGr'      => 'Admins',
    'StationPrefsGr'=> 'StationPrefs',
    'AllGr'         => 'All',
    'TrashName'     => 'trash_',
    'storageDir'    =>  realpath(dirname(__FILE__).'/../../storageServer/var/stor'),
    'bufferDir'     =>  realpath(dirname(__FILE__).'/../../storageServer/var/stor/buffer'),
    'transDir'      =>  realpath(dirname(__FILE__).'/../../storageServer/var/trans'),
    'accessDir'     =>  realpath(dirname(__FILE__).'/../../storageServer/var/access'),
    'pearPath'      =>  realpath(dirname(__FILE__).'/../../../../usr/lib/pear'),
    'cronDir'       =>  realpath(dirname(__FILE__).'/../../storageServer/var/cron'),
    'isArchive'     =>  FALSE,
    'validate'      =>  TRUE,
    'useTrash'      =>  TRUE,

    /* ==================================================== URL configuration */
    'storageUrlPath'        => '/campcasterStorageServer',
    'storageXMLRPC'         => 'xmlrpc/xrLocStor.php',
    'storageUrlHost'        => 'localhost',
    'storageUrlPort'        => 80,

    /* ================================================ archive configuration */
    'archiveUrlPath'        => '/campcasterArchiveServer',
    'archiveXMLRPC'         => 'xmlrpc/xrArchive.php',
    'archiveUrlHost'        => 'localhost',
    'archiveUrlPort'        => 80,
    'archiveAccountLogin'   => 'root',
    'archiveAccountPass'    => 'q',

    /* ============================================== scheduler configuration */
    'schedulerUrlPath'        => '',
    'schedulerXMLRPC'         => 'RC2',
    'schedulerUrlHost'        => 'localhost',
    'schedulerUrlPort'        => 3344,

    /* ==================================== aplication-specific configuration */
    'objtypes'      => array(
        'RootNode'      => array('Folder'),
        'Storage'       => array('Folder', 'File', 'Replica'),
        'Folder'        => array('Folder', 'File', 'Replica'),
        'File'          => array(),
        'audioclip'     => array(),
        'playlist'      => array(),
        'Replica'       => array(),
    ),
    'allowedActions'=> array(
        'RootNode'      => array('classes', 'subjects'),
        'Folder'        => array('editPrivs', 'write', 'read'),
        'File'          => array('editPrivs', 'write', 'read'),
        'audioclip'     => array('editPrivs', 'write', 'read'),
        'playlist'      => array('editPrivs', 'write', 'read'),
        'Replica'       => array('editPrivs', 'write', 'read'),
        '_class'        => array('editPrivs', 'write', 'read'),
    ),
    'allActions'    =>  array(
        'editPrivs', 'write', 'read', 'classes', 'subjects'
    ),

    /* ============================================== auxiliary configuration */
    'RootNode'      => 'RootNode',
    'tmpRootPass'   => 'q',

    /* =================================================== cron configuration */
#    'cronUserName'      => 'www-data',
    'cronUserName'      => 'apache',
#    'lockfile'          => dirname(__FILE__).'/cron/cron.lock',
    'lockfile'     =>  dirname(__FILE__).'/../../storageServer/var/stor/buffer/cron.lock',
    'cronfile'          => dirname(__FILE__).'/cron/croncall.php',
    'paramdir'          => dirname(__FILE__).'/cron/params',
);
$config['sysSubjs'] = array(
    'root', $config['AdminsGr'], $config['AllGr'], $config['StationPrefsGr']
);
$old_ip = get_include_path();
set_include_path('.'.PATH_SEPARATOR.$config['pearPath'].PATH_SEPARATOR.$old_ip);

// see if a ~/.campcaster/storageServer.conf.php exists, and
// overwrite the settings from there if any

$this_file         = null;
if(isset($_SERVER["SCRIPT_FILENAME"])){
    $this_file         = $_SERVER["SCRIPT_FILENAME"];
}elseif(isset($argv[0])){
    $this_file         = $argv[0];
}
if(!is_null($this_file)){
    $fileowner_id      = fileowner($this_file);
    $fileowner_array   = posix_getpwuid($fileowner_id);
    $fileowner_homedir = $fileowner_array['dir'];
    $fileowner_name    = $fileowner_array['name'];
    $home_conf         = $fileowner_homedir . '/.campcaster/storageServer.conf.php';
    if (file_exists($home_conf)) {
        $default_config = $config;
        $developer_name    = $fileowner_name;
        include $home_conf;
        $user_config = $config;
        $config = $user_config + $default_config;
    }
}

if(!PHP5){
 eval('
    define("FILE_APPEND", TRUE);
    function file_put_contents($f, $s, $ap=FALSE){
        $fp=fopen($f, $ap==FILE_APPEND ? "a" : "w");
        fwrite($fp,$s);
        fclose($fp);
    }
');
}

?>