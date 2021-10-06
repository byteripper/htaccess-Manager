<?php
/**
 * Program:       htaccess-Manager
 * Version:       1.0
 * Author:        @byte - Dieter
 * Copyright:     GNU GENERAL PUBLIC LICENSE
 * @version 1.0
 * Description:   Create and manage a complete, password protected Area on the Server.
 * 
 * Language settings
 *
 * If you translated those texts into your language or corrected
 * any typos, then send them to me.
 *
 * Thanks!
 *
*/

$fp_htFile    = ""; 
$fp_pwFile    = "";
$filenameHt   = "";
$filenamePw   = "";
$user         = "";
$dir          = "";
$exclude_list = array(".", "..");
    
$version="v1.0";
session_start();

if(isset($_GET['action'])) {      $action =               $_GET["action"];}
if(isset($_GET['user'])) {        $user =                 $_GET["user"];}
if(isset($_GET['pwd'])) {         $pwd =                  $_GET["pwd"];}
if(isset($_GET['AuthName'])) {    $AuthName =             $_GET["AuthName"];}
if(isset($_GET['inhalt'])) {      $inhalt =               $_GET["inhalt"];}
if(isset($_GET['crypt'])) {       $_SESSION['crypt'] =    $_GET["crypt"];}
if(isset($_GET['dir'])) {         $dir =                  $_GET["dir"];}
if(isset($_GET['htlang'])) {      $_SESSION['htlang'] =   $_GET["htlang"];}
if(!isset($_SESSION['htlang'])) { $_SESSION['htlang'] =   'de';}

require('language/'.$_SESSION['htlang'].'.php');

if (isset($dir)) {
  $dir_path = $_SERVER["DOCUMENT_ROOT"]."/".$dir;
}else{
  $dir_path = $_SERVER["DOCUMENT_ROOT"]."/";
  $dir = "";
}

function HTLANG($phrase, $echophrase){
  global $lang;
  if ($echophrase){
    echo $lang[$phrase];
  }else{
    $strlang = $lang[$phrase];
    return $strlang;
  }
}

function confirmdel($text, $user, $dir){
  echo '<div class="opacity opacitycolor"></div><div class="symbol"><font color="#0000FF">&#10067;</font></div><div class="info-msg" align="center"><a href="access.php?action=deluser&amp;user='.$user.'&amp;dir='.$dir.'" class="button" style="position: absolute; top: 110px; left: 150px; ">'.HTLANG('btn_yes', False).'</a>&nbsp;&nbsp;<a href="access.php?dir='.$dir.'" class="button" style="position: absolute; top: 110px; ">'.HTLANG('btn_no', False).'</a><div class="medium"><b>'.$text.'</b></div></div></div>';
}

function confirmreset($text, $dir){
  echo '<div class="opacity opacitycolor"></div><div class="symbol"><font color="#0000FF">&#10067;</font></div><div class="info-msg" align="center"><a href="access.php?action=clear&amp;dir='.$dir.'" class="button" style="position: absolute; top: 110px; left: 150px; ">'.HTLANG('btn_yes', False).'</a>&nbsp;&nbsp;<a href="access.php?dir='.$dir.'" class="button" style="position: absolute; top: 110px; ">'.HTLANG('btn_no', False).'</a><b><div class="medium"><b>'.$text.'</b></div></div>';
}

function error($text, $dir){
  echo '<div class="opacity opacitycolor"></div><div class="symbol"><font color="#FF3300">&#9888;</font></div><div class="info-msg" align="center"><a href="access.php?dir='.$dir.'" class="button" style="position: absolute; top: 110px; left: 200px; ">'.HTLANG('btn_ok', False).'</a><p class="medium"><font color="#FF3300"><b>'.HTLANG('lbl_error', False).'</p><div class="small">'.HTLANG('lbl_error_manager', False).'</b></font></div><p class="small"><b>'.$text.'</b></p></div></div>';
}

function msg($text, $dir) {
  echo '<div class="opacity opacitycolor"></div><div class="symbol"><font color="#0000FF">&#10071;</font></div><div class="info-msg" align="center"><a href="access.php?dir='.$dir.'" class="button" style="position: absolute; top: 110px; left: 200px; ">'.HTLANG('btn_ok', False).'</a><p class="medium"><b>'.$text.'</b></p></div>';
}
    
function edit_htaccess($inhalt, $dir){
  echo '<div class="opacity opacitycolor"></div><div class="info-edit" align="center"><a href="access.php?dir='.$dir.'" class="button" style="position: absolute; top: 322px; left: 420px;">'.HTLANG('btn_cancel', False).'</a><p class="medium"><font color="#ff0000"><b>'.HTLANG('lbl_htaccess_edit', False).'</font></p><form method=get action=access.php><TEXTAREA NAME="inhalt" cols="60" rows="15" style="resize: none;">'.$inhalt.'</TEXTAREA><input type="hidden" name="action" value="save_htaccess"><input type="hidden" name="dir" value="'.$dir.'"><br><button class="button" style="position: absolute; top: 322px; left: 280px;" type="submit">'.HTLANG('btn_save', False).'</button></form></div>';
}
?> 
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>htaccess-Manager <?php echo $version; ?></title>
<style type="text/css">
.main {
  position:relative;
  color: #fff;
  max-width: 960px;
  margin: 0 auto;
  z-index:1;
}
.symbol {
  background: #F4F4F4;
  position: absolute;
  left: 278px;
  top: 176px;
  height: 20px;
  width: 20px;
  font-size:50px;
  z-index:4;
}
.info-msg {
  border: 1px solid #000000;
  background-color: #F4F4F4;
  position: absolute;
  left: 253px;
  top: 170px;
  height: 140px;
  width: 460px;
  z-index:3;
}
.info-edit {
  border: 1px solid #000000;
  background-color: #F4F4F4;
  position: absolute;
  left: 200px;
  top: 60px;
  height: 350px;
  width: 560px;
  z-index:3;
}

.opacitycolor { background: #668; }

/* partial opacity hack: configuration */
.opacity { 
  opacity: 0.5;       /* modern browser */
  -moz-opacity: 0.5;  /* older Mozilla browser */
  -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)"; /* IE8 in Standard Compliant Mode */
  filter:alpha(opacity=50); /* IE5.x -7.0 */
} 

.opacity { 
  /* partial opacity hack, part II: Don't change the following properties! */
  width: 100%;
  height: 100%;
  position:absolute;
  top:0;
  left:0;
  z-index:2;
} 

/* partial opacity hack, part III: IE6 support */
* html .opacity-wrapper { 
  overflow:hidden;
}
* html .opacity { 
  width: 2000px; 
  height: 2000px;
}

.button {
  -moz-box-shadow: 3px 4px 0px 0px #899599;
  -webkit-box-shadow: 3px 4px 0px 0px #899599;
  box-shadow: 3px 4px 0px 0px #899599;
  background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #ededed), color-stop(1, #bab1ba));
  background:-moz-linear-gradient(top, #ededed 5%, #bab1ba 100%);
  background:-webkit-linear-gradient(top, #ededed 5%, #bab1ba 100%);
  background:-o-linear-gradient(top, #ededed 5%, #bab1ba 100%);
  background:-ms-linear-gradient(top, #ededed 5%, #bab1ba 100%);
  background:linear-gradient(to bottom, #ededed 5%, #bab1ba 100%);
  filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ededed', endColorstr='#bab1ba',GradientType=0);
  background-color:#ededed;
  -moz-border-radius:10px;
  -webkit-border-radius:10px;
  border-radius:10px;
  border:1px solid #d6bcd6;
  display:inline-block;
  cursor:pointer;
  color:#000000;
  font-family:Arial;
  font-size:12px;
  font-weight:bold;
  padding:1px 25px;
  text-decoration:none;
  text-shadow:0px 1px 0px #e1e2ed;
}
.button:hover {
  background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #bab1ba), color-stop(1, #ededed));
  background:-moz-linear-gradient(top, #bab1ba 5%, #ededed 100%);
  background:-webkit-linear-gradient(top, #bab1ba 5%, #ededed 100%);
  background:-o-linear-gradient(top, #bab1ba 5%, #ededed 100%);
  background:-ms-linear-gradient(top, #bab1ba 5%, #ededed 100%);
  background:linear-gradient(to bottom, #bab1ba 5%, #ededed 100%);
  filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#bab1ba', endColorstr='#ededed',GradientType=0);
  background-color:#bab1ba;
  color:#0000FF;
}
.button:active {
  position:relative;
  top:1px;
}
input[type='text'] { 
  font-size: 11px;
}
select { 
  font-size: 11px;
}
.small {
  font-family:Geneva, Arial, Helvetica, san-serif;
  font-size: 11px;
  color: #666666;
}
.medium {
  font-family:Geneva, Arial, Helvetica, san-serif;
  font-size: 12px;
  color: #666666;
}
.big {
  font-family:Geneva, Arial, Helvetica, san-serif;
  font-size: 18px;
  color: #666666;
}
</style>
</head>
<body bgcolor="#666666" text="#333333" link="#0033FF" vlink="#0033CC" alink="#FF3300" topmargin="0"><br>
<div class="main">
  <table width="960" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
    <tr>
      <td width="23%">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr bgcolor="#CCCCCC">
            <td width="6%">&nbsp;</td>
            <td width="94%" bgcolor="#CCCCCC">
              <div class="big" align="center">&nbsp;</div>
              <div class="small" align="left">&nbsp;</div>
            </td>
          </tr>
          <tr>
            <td width="6%" bgcolor="#CCCCCC">&nbsp;</td>
            <td width="94%">
              <div class="medium" style="overflow:scroll; overflow-x:hidden; height:382px;">
                <?php
                $directories = array_diff(scandir($dir_path), $exclude_list);
                $dir_path = str_replace("//", "/", $dir_path);
                
                if ($dir_path == $_SERVER["DOCUMENT_ROOT"]."/"){
                  echo '<b>Browse:</b><br><br>'.HTLANG('lbl_root', False).'<br>';
                  echo '<ul style="list-style:none;padding:0">';
                  //Kein Root oder up anzeigen
                }else{
                  echo HTLANG('lbl_browse', False);
                  echo '<ul style="list-style:none;padding:0">';
                  echo '<li style="margin-left:1em;">&#8624; <a href="access.php?dir=">'.HTLANG('lbl_root', False).'</a></li>';
                  //echo '<li style="margin-left:1em;">&#11014; <a href="access.php?dir='.$dir.'../">up</a></li>';
                }
                foreach($directories as $entry) {
                  if(is_dir($dir_path.$entry)) {
                    $filepathHt = $dir_path.$entry . '/.htaccess';
                    $filepathPw = $dir_path.$entry . '/.htpasswd';
                    if ( file_exists( $filepathHt ) && file_exists( $filepathPw )) {
                      echo "<li style='margin-left:1em;'>&#128193; <a href='access.php?dir=".$dir.$entry."/"."'>".$entry."</a><font color=\"#B0B000\">&nbsp;&#128274;</font></li>";
                    }else{
                      echo "<li style='margin-left:1em;'>&#128193; <a href='access.php?dir=".$dir.$entry."/"."'>".$entry."</a></li>";
                    }
                  }
                }
                echo "</ul>";
                ?>
              </div>
            </td>
          </tr>
          <tr>
            <td width="6%" height="35" bgcolor="#CCCCCC">&nbsp;</td>
            <td width="94%" height="35" bgcolor="#CCCCCC">
              <div class="big" align="center"><b>&nbsp;</b></div>
            </td>
          </tr>
        </table>
      </td>
      <td width="77%">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr bgcolor="#CCCCCC">
                  <td width="63%" bgcolor="#CCCCCC">
                    <div align="center">
                      <div class="big"><b>htaccess - Manager <?php echo $version; ?></b></div>
                      <div class="small">&#169;copyright 2016, @byte - Dieter</div>
                    </div>
                  </td>
                  <td width="35%" bgcolor="#CCCCCC">
                    <div align="right">
                      <div class="big">
                        <div class="small">
                          <form method="get" action="./access.php" name="send">
                          <?php HTLANG('lbl_lang', True);?>
                            <select name="htlang" onchange='this.form.submit();'>
                              <option value="de" <?php if($_SESSION['htlang']=="de") echo 'selected="selected"'; ?>>Deutsch</option>
                              <option value="en" <?php if($_SESSION['htlang']=="en") echo 'selected="selected"'; ?>>English</option>
                              <option value="cs" <?php if($_SESSION['htlang']=="cs") echo 'selected="selected"'; ?>>Czech</option>
                            </select>
                            <input type="hidden" name="dir" value="<?php echo $dir ?>">
                          </form>
                        </div>
                      </div>
                    </div>
                    <div class="small">&nbsp;</div>
                  </td>
                  <td width="2%">&nbsp;</td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="98%" height="70px">
                    <div class="small" align="center">
                      <?php HTLANG('lbl_intro1', True);?><br>
                      <font color="#FF3300">
                        <?php HTLANG('lbl_intro2', True);?>
                      </font>
                      <br>
                    </div>
                  </td>
                  <td width="2%" bgcolor="#CCCCCC">&nbsp;</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">         
                <tr>
                  <td width="98%">
                    <div align="center">
                      <a href="./access.php?action=msgboxclear&amp;dir=<?php echo $dir ?>" class="button"><?php HTLANG('btn_clear', True);?></a>&nbsp;&nbsp;
                      <a href="./access.php?action=edit_htaccess&amp;dir=<?php echo $dir ?>" class="button"><?php HTLANG('btn_edit', True);?></a>
                      <br><br>
                    </div>
                  </td>
                  <td width="2%" bgcolor="#CCCCCC">&nbsp;</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">         
                <tr>
                  <td width="98%">
                    <div class="small" align="center"><font color="#0000FF">
                      <?php
                      echo HTLANG('lbl_server_crypt', False);
                      if (CRYPT_STD_DES == 1) {echo 'Standard DES, ';}
                      if (CRYPT_EXT_DES == 1) {echo 'Extended DES, ';}
                      if (CRYPT_MD5 == 1) {echo 'MD5, ';}
                      if (CRYPT_BLOWFISH == 1) {echo 'Blowfish, ';}
                      if (CRYPT_SHA256 == 1) {echo 'SHA-256, ';}
                      if (CRYPT_SHA512 == 1) {echo 'SHA-512';}
                      ?>
                      <br><font color="#FF8040"><b>
                      <?php
                      echo "PHP-Version: " . phpversion();
                      if (version_compare(phpversion(), "5.3.2", "<")) {echo '<font color="#FF3300">'.HTLANG('lbl_attention_php', False).'</font>';}
                      ?>
                      <br></b></font></font>
                    </div>
                  </td>
                  <td width="2%" bgcolor="#CCCCCC">&nbsp;</td>
                </tr>
              </table>
            </td>
          </tr>
<?php
/**
* Returns size of htaccess File
* Liefert die Größe der htaccess Datei
* 
* @return    mixed    Die Groesse der Datei oder im Fehlerfall false
*/
function getHtFileSize($dir){
    $filenameHt = str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"]."/".$dir . '/.htaccess');
        
    if ( $filenameHt != NULL ) {
      if ( file_exists( $filenameHt ) ) {
        return ( filesize( $filenameHt ) );
      }
    }
        
    return false;
}
    
/**
* Returns size of htpasswd File
* Liefert die Größe der htpasswd Datei
* 
* @return    mixed    Die Groesse der Datei oder im Fehlerfall false
*/
function getPwFileSize($dir){
  $filenamePw = str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"]."/".$dir . '/.htpasswd');
        
  if ( $filenamePw != NULL ) {
    if ( file_exists( $filenamePw ) ) {
      return ( filesize( $filenamePw ) );
    }
  }
        
  return false;
}
    
/**
* Checks if User exists in htpasswd File
* Prüft, ob User bereits in der htpasswd-Datei vorhanden ist
* 
* @return    boolean                True im Erfolgsfall, false im Fehlerfall
*/
function user_exists($user, $dir){
  $filenamePw = str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"]."/".$dir . '/.htpasswd');
        
  if ( $filenamePw != NULL )
    $fp_pwFile = fopen ( $filenamePw, "r+" );
        
  rewind($fp_pwFile);
            
  if ( getPwFileSize( $dir ) > 0 ) {
    while ( ($data = fgetcsv ( $fp_pwFile, 500, ":" ) ) !== FALSE ) {
      if ( $data[0] == $user){
        return true;
      }
    }
  }

  return false;
}
    
/**
* Adds new User to htpasswd File
* Fügt einen neuen User der htpasswd-Datei hinzu
* 
* @return    boolean                True im Erfolgsfall, false im Fehlerfall
*/
function addNewUser ( $user, $passwd, $crypt, $dir ){
  $filenamePw = str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"]."/".$dir . '/.htpasswd');
        
  if ( $filenamePw != NULL )
    $fp_pwFile = fopen ( $filenamePw, "r+" );
        
    $userLoad = "";
        
  if ( !$fp_pwFile ) 
    return false;
        
  rewind( $fp_pwFile );

  if ( getPwFileSize( $dir ) > 0 )
    $userLoad = fread( $fp_pwFile, getPwFileSize( $dir ) );
        
  switch($crypt){
    case "crypt_apache_DES":
      $salt = substr(hash('sha512', $passwd), rand(1, 100), 2);
      $userLoad .= $user . ':' . crypt($passwd, $salt) . "\n";
      break;
    case "crypt_apache_EXT_DES":
      $salt = '_' . substr(hash('sha512', $passwd), rand(1, 100), 8);
      $userLoad .= $user . ':' . crypt($passwd, $salt) . "\n";
      break;
    case "crypt_apache_MD5":
      $salt = '$1$' . substr(hash('sha512', $passwd), rand(1, 100), 9);
      $userLoad .= $user . ':' . crypt($passwd, $salt) . "\n";
    case "crypt_apache_BLOWFISH":
      //$options = ['cost' => 10,];
      if (version_compare(phpversion(), "5.5.0", ">=")) { 
        $salt = password_hash($passwd, PASSWORD_DEFAULT);
      }else{
        $salt = '$2a$10$' . substr(hash('sha512', $passwd), rand(1, 100), 22);
      }
      $userLoad .= $user . ':' . crypt($passwd, $salt) . "\n";
      break;
    case "crypt_apache_SHA256":
      $salt = '$5$rounds=5000$' . substr(hash('sha512', $passwd), rand(1, 100), 16) . '$';
      $userLoad .= $user . ':' . crypt($passwd, $salt) . "\n";
      break;
    case "crypt_apache_SHA512":
      $salt = '$6$rounds=5000$' . substr(hash('sha512', $passwd), rand(1, 100), 16) . '$';
      $userLoad .= $user . ':' . crypt($passwd, $salt) . "\n";
      break;
      break;
    case "crypt_iispassword":
      $salt = substr(md5("htaccManager".microtime()), 0, 9);
      $userLoad .= $user . ':' . crypt($passwd, $salt) . "\n";
      break;
    case "default":
      $userLoad .= $user . ':' . $passwd . "\n";
      break;
  }

    $fp_pwFile = fopen ( $filenamePw, "w+" );
        
    if ( !fwrite( $fp_pwFile, $userLoad ) ) 
      return false;
        
    if ( $fp_pwFile != FALSE )
      fclose($fp_pwFile);
        
    return true;
}
    
/**
* Delete User from htpasswd File
* Löscht einen bestehenden User aus der htpasswd-Datei
* 
* @return    boolean                True im Erfolgsfall, false im Fehlerfall
*/
function deleteUser ( $user, $dir ){
  $filenamePw = str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"]."/".$dir . '/.htpasswd');
        
  if ( $filenamePw != NULL )
    $fp_pwFile = fopen ( $filenamePw, "r+" );
            
  $userLoad = "";
      
  if ( !$fp_pwFile ) 
    return false;
        
  rewind( $fp_pwFile );
        
  if ( getPwFileSize( $dir ) > 0 ) {
    while ( ($data = fgetcsv ( $fp_pwFile, 500, ":" ) ) !== FALSE ) {
      if ( $data[0] != $user){
        $userLoad .= $data[0] . ':' . $data[1] . "\n";
      }
    }
  }
                     
  $fp_pwFile = fopen ( $filenamePw, "w+" );
        
  if ( !fwrite( $fp_pwFile, $userLoad ) ) 
    return false;
        
  if ( $fp_pwFile != FALSE )
    fclose($fp_pwFile);
        
   return true;
}
    
/**
* Create new htaccess File
* Neue htacess Datei erstellen
* 
* @return    boolean                True im Erfolgsfall, false im Fehlerfall
*/
function createNewHtaccess ( $AuthName , $htFile = NULL, $pwFile = NULL, $dir ) {
  $filenameHt = str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"]."/".$dir . '/.htaccess');
  $filenamePw = str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"]."/".$dir . '/.htpasswd');
        
  if ( $htFile == NULL )
    $htFile = $filenameHt;
            
  if ( $pwFile == NULL )
    $pwFile = $filenamePw;
                    
  $insideFile = 'AuthType Basic' . "\n";
  $insideFile .= 'AuthName "' . $AuthName . '"' . "\n";
  $insideFile .= 'AuthUserFile ' . $pwFile . "\n";
  $insideFile .= 'require valid-user';
       
  $fp_htFile = fopen ( $htFile, "w+" );
      
  if ( !fwrite( $fp_htFile, $insideFile ) ) 
    return false;
        
  if ( $fp_htFile != FALSE )
    fclose($fp_htFile);
            
  return true;
}
    
/**
* Create new htpasswd File
* Neue htpasswd Datei erstellen
* 
* @return    boolean                True im Erfolgsfall, false im Fehlerfall
*/
function createNewHtpasswd ( $pwFile = NULL, $dir ) {
  $filenamePw = str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"]."/".$dir . '/.htpasswd');
        
  if ( $pwFile == NULL )
    $pwFile = $filenamePw;
            
  $fp_pwFile = fopen ( $pwFile, "w+" );
        
  if ( !$fp_pwFile ) 
    return false;
        
  if ( $fp_pwFile != FALSE )
    fclose($fp_pwFile);
            
  return true;        
}

if (isset($action)) {
  switch($action){
    case "neu":
      if ( file_exists( str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"]."/".$dir . '/.htaccess') ) ) {
        error(HTLANG('err_htaccess_exists', False), $dir);
      }elseif (!isset($_SESSION['crypt'])) {
        error(HTLANG('err_crypt', False), $dir);
      }elseif ($AuthName == "" || $AuthName == " "){
        error(HTLANG('err_section', False), $dir);
      }elseif ($user == "" || $user == " "){
        error(HTLANG('err_username', False), $dir);
      }elseif ($pwd == "" || $pwd == " "){
        error(HTLANG('err_password', False), $dir);
      }else{
        $err = "";
        $msg = "";
        if (createNewHtaccess( $AuthName, NULL, NULL, $dir ) != FALSE){
          $msg = $msg. HTLANG('msg_htaccess_created', False);
        }else{
          $err = $err. HTLANG('err_htaccess_create', False);
        }
        if (createNewHtpasswd( NULL, $dir ) != FALSE){
          $msg = $msg. HTLANG('msg_htpasswd_created', False);
        }else{
          $err = $err. HTLANG('err_htpasswd_create', False);
        }
        if (addNewUser( $user, $pwd, $_SESSION['crypt'], $dir) != FALSE){
          $msg = $msg. HTLANG('msg_user_created', False);
        }else{
          $err = $err. HTLANG('err_user_create', False);
        }
        if ($err != ""){error($err. HTLANG('err_folder_cmod', False), $dir);}
        if ($msg != ""){msg($msg, $dir);}
      }
      break;
    case "adduser":
      if ( !file_exists( str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"]."/".$dir . '/.htpasswd') ) ) {
        error(HTLANG('err_htpasswd_not_exists', False), $dir);
      }elseif (!isset($_SESSION['crypt'])) {
        error(HTLANG('err_crypt', False), $dir);
      }elseif ($user == "" || $user == " "){
        error(HTLANG('err_username', False), $dir);
      }elseif ($pwd == "" || $pwd == " "){
        error(HTLANG('err_password', False), $dir);
      }elseif (user_exists($user, $dir)){
        error(HTLANG('err_user_exists', False), $dir);
      }else{
        $err = "";
        $msg = "";
        if (addNewUser( $user, $pwd, $_SESSION['crypt'], $dir) != FALSE){
          $msg = $msg. HTLANG('msg_user_created', False);
        }else{
          $err = $err. HTLANG('err_user_create', False);
        }
        if ($err != ""){error($err. HTLANG('err_folder_cmod', False), $dir);}
        if ($msg != ""){msg($msg, $dir);}
      }
      break;
    case "deluser":
      if ($user == "" || $user == " "){
        error(HTLANG('err_username', False), $dir);
      }else{
        $err = "";
        $msg = "";
        if (deleteUser( $user, $dir) != FALSE){
          $msg = $msg. HTLANG('msg_user_deleted', False);
        }else{
          $err = $err. HTLANG('err_user_deleted', False);
        }
        if ($err != ""){error($err. HTLANG('err_folder_cmod', False), $dir);}
        if ($msg != ""){msg($msg, $dir);}
      }
      break;
    case "edit_htaccess":
      $inhalt = "";
      $filenameHt = str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"]."/".$dir . '/.htaccess');
            
      if ( file_exists( $filenameHt ) ) {
        $fp_htFile = fopen ( $filenameHt, "r+" );
        $inhalt = fread ($fp_htFile, getHtFileSize($dir));
                
        if ( $fp_htFile != FALSE )
          fclose($fp_htFile);
                
        edit_htaccess($inhalt, $dir);
      }else{
        error(HTLANG('err_htaccess_not_exists', False), $dir);
      }
      break;
    case "save_htaccess":
      $err = "";
      $msg = "";
      $filenameHt = str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"]."/".$dir . '/.htaccess');
      if ( file_exists( $filenameHt ) ) {
        $fp_htFile = fopen ( $filenameHt, "w+" );
        if ( !fwrite( $fp_htFile, $inhalt ) ){
          $err = $err. HTLANG('err_htaccess_save', False);
        }else{
          $msg = $msg. HTLANG('msg_htaccess_save', False);
        }
      }else{
          $err = $err. HTLANG('err_htaccess_not_exists', False);
      }
        
      if ( $fp_htFile != FALSE )
        fclose($fp_htFile);
            
      if ($err != ""){error($err, $dir);}
      if ($msg != ""){msg($msg, $dir);}
      break;
    case "clear":
      $err = "";
      $msg = "";
      $filenameHt = str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"]."/".$dir . '/.htaccess');
      $filenamePw = str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"]."/".$dir . '/.htpasswd');
            
      if(file_exists($filenameHt)) {
        if (!unlink($filenameHt)) {
          $err = $err. HTLANG('err_htaccess_delete_manual', False);
        }else{
          $msg = $msg. HTLANG('msg_htaccess_deleted', False);
        }
      }else{
          $err = $err. HTLANG('err_no_htaccess_delete', False);
      }
      if(file_exists($filenamePw)) {
        if (!unlink($filenamePw)) {
          $err = $err. HTLANG('err_htpasswd_delete_manual', False);
        }else{
          $msg = $msg. HTLANG('msg_htpasswd_deleted', False);
        }
      }else{
          $err = $err. HTLANG('err_no_htpasswd_delete', False);
      }
      if ($err != ""){error($err, $dir);}
      if ($msg != ""){msg($msg, $dir);}
      break;
    case "msgboxclear":
      confirmreset(HTLANG('msg_confirm_clear', False), $dir);
      break;
    case "msgboxdel":
      confirmdel(HTLANG('msg_confirm_user_delete', False), $user, $dir);
      break;
  }
}
?>
          <tr>  
            <td>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>            
                  <td width="0,5%">&nbsp;</td>
                  <td width="32%" align="center" bgcolor="#000000"><div class="small"><font color="#FFFFFF"><b><?php HTLANG('lbl_protected_section', True);?></b></font></div></td>
                  <td width="0,5%">&nbsp;</td>
                  <td width="32%" align="center" bgcolor="#000000"><div class="small"><font color="#FFFFFF"><b><?php HTLANG('lbl_user_add', True);?></b></font></div></td>
                  <td width="0,5%">&nbsp;</td>
                  <td width="32%" align="center" bgcolor="#000000"><div class="small"><font color="#FFFFFF"><b><?php HTLANG('lbl_user_delete', True);?></b></font></div></td>
                  <td width="0,51%">&nbsp;</td>
                  <td width="2%" bgcolor="#CCCCCC">&nbsp;</td>
                </tr>
                <tr>            
                  <td width="0,5%">&nbsp;</td>
                  <td width="32%">
                    <form method="get" action="./access.php" name="send">
                      <div class="small">
                        <b><?php HTLANG('lbl_option_crypt', True);?></b><br>
                          <select name="crypt">
                            <option value="crypt_apache_DES" <?php if(isset($_SESSION['crypt']) AND $_SESSION['crypt']=="crypt_apache_DES") echo 'selected="selected"'; ?>>(Apache-Linux) DES</option>
                            <option value="crypt_apache_EXT_DES" <?php if(isset($_SESSION['crypt']) AND $_SESSION['crypt']=="crypt_apache_EXT_DES") echo 'selected="selected"'; ?>>(Apache-Linux) EXT_DES</option>
                            <option value="crypt_apache_MD5" <?php if(isset($_SESSION['crypt']) AND $_SESSION['crypt']=="crypt_apache_MD5") echo 'selected="selected"'; ?>>(Apache-Linux) MD5</option>
                            <option value="crypt_apache_BLOWFISH" <?php if(isset($_SESSION['crypt']) AND $_SESSION['crypt']=="crypt_apache_BLOWFISH") echo 'selected="selected"'; ?>>(Apache-Linux) Blowfish</option>
                            <option value="crypt_apache_SHA256" <?php if(isset($_SESSION['crypt']) AND $_SESSION['crypt']=="crypt_apache_SHA256") echo 'selected="selected"'; ?>>(Apache-Linux) SHA-256</option>
                            <option value="crypt_apache_SHA512" <?php if(isset($_SESSION['crypt']) AND $_SESSION['crypt']=="crypt_apache_SHA512") echo 'selected="selected"'; ?>>(Apache-Linux) SHA-512</option>
                            <option value="default" <?php if(isset($_SESSION['crypt']) AND $_SESSION['crypt']=="default") echo 'selected="selected"'; ?>>(Apache-Windows) <?php HTLANG('lbl_no_crypt', True);?></option>
                            <option value="crypt_iispassword" <?php if(isset($_SESSION['crypt']) AND $_SESSION['crypt']=="crypt_iispassword") echo 'selected="selected"'; ?>>(IIS-Windows, IISPassword)</option>
                          </select>
                          <br>
                          <br>
                          <div class="small" align="left">
                            <font color="#FF8040"></b><?php HTLANG('lbl_no_space', True);?><b></font>
                          </div>
                          <b><?php HTLANG('lbl_max_section', True);?></b><br>
                          <input type="text" name="AuthName" size="39" maxsize="30" value="<?php if(isset($AuthName)) echo $AuthName ?>">
                          <br>
                          <br>
                          <b><?php HTLANG('lbl_usernamen', True);?>&nbsp;&nbsp;&nbsp;</b>
                          <input type="text" name="user" size="25">
                          <br>
                          <br>
                          <b><?php HTLANG('lbl_password', True);?></b>&nbsp;&nbsp;&nbsp;&nbsp;
                          <input type="password" name="pwd">
                          <input type="hidden" name="action" value="neu">
                          <input type="hidden" name="dir" value="<?php echo $dir ?>">
                      </div>
                         <br>
                         <div align="center">
                         <button class="button" type="submit"><?php HTLANG('btn_save', True);?></button>
                         <br>
                         <br>
                         </div>
                         </form>
                  </td>
                  <td width="0,5%">&nbsp;</td>
                  <td valign="top" width="32%">
                    <form method="get" action="./access.php" name="send">
                      <div class="small">
                        <b><?php HTLANG('lbl_option_crypt', True);?></b><br>
                          <select name="crypt"">
                            <option value="crypt_apache_DES" <?php if(isset($_SESSION['crypt']) AND $_SESSION['crypt']=="crypt_apache_DES") echo 'selected="selected"'; ?>>(Apache-Linux) DES</option>
                            <option value="crypt_apache_EXT_DES" <?php if(isset($_SESSION['crypt']) AND $_SESSION['crypt']=="crypt_apache_EXT_DES") echo 'selected="selected"'; ?>>(Apache-Linux) EXT_DES</option>
                            <option value="crypt_apache_MD5" <?php if(isset($_SESSION['crypt']) AND $_SESSION['crypt']=="crypt_apache_MD5") echo 'selected="selected"'; ?>>(Apache-Linux) MD5</option>
                            <option value="crypt_apache_BLOWFISH" <?php if(isset($_SESSION['crypt']) AND $_SESSION['crypt']=="crypt_apache_BLOWFISH") echo 'selected="selected"'; ?>>(Apache-Linux) Blowfish</option>
                            <option value="crypt_apache_SHA256" <?php if(isset($_SESSION['crypt']) AND $_SESSION['crypt']=="crypt_apache_SHA256") echo 'selected="selected"'; ?>>(Apache-Linux) SHA-256</option>
                            <option value="crypt_apache_SHA512" <?php if(isset($_SESSION['crypt']) AND $_SESSION['crypt']=="crypt_apache_SHA512") echo 'selected="selected"'; ?>>(Apache-Linux) SHA-512</option>
                            <option value="default" <?php if(isset($_SESSION['crypt']) AND $_SESSION['crypt']=="default") echo 'selected="selected"'; ?>>(Apache-Windows) <?php HTLANG('lbl_no_crypt', True);?></option>
                            <option value="crypt_iispassword" <?php if(isset($_SESSION['crypt']) AND $_SESSION['crypt']=="crypt_iispassword") echo 'selected="selected"'; ?>>(IIS-Windows, IISPassword)</option>
                          </select>
                          <br>
                          <br>
                          <div class="small" align="left">
                            <font color="#FF8040"></b><?php HTLANG('lbl_no_space', True);?><b></font>
                          </div>
                          <p style="line-height:12px;">&nbsp;</p>
                          <br>
                          <b><?php HTLANG('lbl_usernamen', True);?>&nbsp;&nbsp;&nbsp;</b>
                          <input type="text" name="user" size="25">
                          <br>
                          <br>
                          <b><?php HTLANG('lbl_password', True);?></b>&nbsp;&nbsp;&nbsp;&nbsp;
                          <input type="password" name="pwd">
                          <input type="hidden" name="action" value="adduser">
                          <input type="hidden" name="dir" value="<?php echo $dir ?>">
                          </div>
                          <br>
                          <div align="center">
                            <button class="button" type="submit"><?php HTLANG('btn_save', True);?></button>
                          <br>
                          <br>
                          </div>
                          </form>
                  </td>
                  <td width="0,5%">&nbsp;</td>
                  <td valign="top" width="32%">
                    <form method="get" action="./access.php" name="send">
                      <div class="small" align="center">
                        <b><?php HTLANG('lbl_select_user', True);?></b><br>
                        <select name="user">
                        <?php
                        $filenamePw = str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"]."/".$dir . '/.htpasswd');
                        if ( file_exists( $filenamePw ) ) {
                          if ( $filenamePw != NULL )
                            $fp_pwFile = fopen ( $filenamePw, "r" );

                          while ( ($data = fgetcsv ( $fp_pwFile, 500, ":" ) ) !== FALSE ) {
                            $user = $data[0];
                              echo "<option value=$user>$user</option>";
                          }
                          if ( $fp_pwFile != FALSE )
                            fclose($fp_pwFile);
                        }
                        ?>
                        </select>
                        <input type="hidden" name="action" value="msgboxdel">
                        <input type="hidden" name="dir" value="<?php echo $dir ?>">
                      </div>
                      <div align="center">
                        <button class="button" type="submit" style="position: relative; top: 152px; "><?php HTLANG('btn_delete', True);?></button>
                        <br>
                      </div>
                    </form>
                  </td>
                  <td width="0,5%">&nbsp;</td>
                  <td width="2%" bgcolor="#CCCCCC">&nbsp;</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr bgcolor="#CCCCCC">
                  <td width="98%" height="35" bgcolor="#CCCCCC">
                    <div class="big" align="center">
                      <font color="#0080FF"><b>
                      <?php
                      $auth = "";
                      $filenameHt = str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"]."/".$dir . '/.htaccess');
                            
                      if ( file_exists( $filenameHt ) ) {
                        if ( $filenameHt != NULL )
                          $array = file($filenameHt);
                                
                          foreach($array as $zeile) {
                            if (substr($zeile, 0, 8) == "AuthName"){
                              $auth = substr($zeile, 9, 45);
                            }
                          }
                          echo '<font color="#B0B000">&#128274;</font> '.$auth.'';
                      }else{
                        echo HTLANG('not_protected_section', False);
                      }
                      ?>
                      </font></b>
                    </div>
                  </td>
                  <td width="2%" height="35">&nbsp;</td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</div>
</body>
</html>
