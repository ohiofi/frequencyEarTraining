<?php ini_set('display_errors', '1'); ?>
<?php require_once('Connections/ohiofi.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "0,1,2";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "login.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}






/*

----------------------------------------------HERE IS THE SURVEY STUFF----------------------------------------------

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
  $insertSQL = sprintf("INSERT INTO survey (entryNumber, userName, gameNumber, questionNumber, q1, q2, q3, q4, q5) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['entry'], "int"),
                       GetSQLValueString($_SESSION['MM_Username'], "text"),
                       GetSQLValueString($_POST['gamenum'], "int"),
                       GetSQLValueString($_POST['ques'], "int"),
                       GetSQLValueString($_POST['RadioGroup1'], "text"),
                       GetSQLValueString($_POST['RadioGroup2'], "text"),
                       GetSQLValueString($_POST['RadioGroup3'], "text"),
                       GetSQLValueString($_POST['RadioGroup4'], "text"),
                       GetSQLValueString($_POST['RadioGroup5'], "text"));

  mysql_select_db($database_ohiofi, $ohiofi);
  $Result1 = mysql_query($insertSQL, $ohiofi) or die(mysql_error());

  $insertGoTo = "mainmenu.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}*/


$editFormAction = $_SERVER['PHP_SELF'];

if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$colname_rsGame8 = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsGame8 = $_SESSION['MM_Username'];
}
mysql_select_db($database_ohiofi, $ohiofi);
$query_rsGame8=sprintf("SELECT game8responses.entryNumber, game8responses.questionNumber FROM game8responses WHERE userName=%s AND game8responses.tally=1 ORDER BY game8responses.questionNumber DESC",
  GetSQLValueString($colname_rsGame8, "text")); 
$rsGame8 = mysql_query($query_rsGame8, $ohiofi) or die(mysql_error());
$row_rsGame8 = mysql_fetch_assoc($rsGame8);
$totalRows_rsGame8 = mysql_num_rows($rsGame8);




mysql_select_db($database_ohiofi, $ohiofi);
$query_rsScore3=sprintf("SELECT game8responses.entryNumber, game8responses.questionNumber FROM game8responses WHERE userName=%s",
  GetSQLValueString($colname_rsGame8, "text")); 
$rsScore3 = mysql_query($query_rsScore3, $ohiofi) or die(mysql_error());
$row_rsScore3 = mysql_fetch_assoc($rsScore3);
$totalRows_rsScore3 = mysql_num_rows($rsScore3);


/* ------------------------------------- rsGame8_check is the fail safe -----------------------------------------*/

mysql_select_db($database_ohiofi, $ohiofi);
$query_rsGame8_check=sprintf("SELECT game8responses.entryNumber, game8responses.questionNumber FROM game8responses WHERE userName=%s AND game8responses.questionNumber=%s",
  GetSQLValueString($colname_rsGame8, "text"), GetSQLValueString($_POST['questionNumber'], "int")); 
$rsGame8_check = mysql_query($query_rsGame8_check, $ohiofi) or die(mysql_error());
$row_rsGame8_check = mysql_fetch_assoc($rsGame8_check);
$totalRows_rsGame8_check = mysql_num_rows($rsGame8_check);


mysql_select_db($database_ohiofi, $ohiofi);
$query_rsUser=sprintf("SELECT users.userID, users.game8 FROM users WHERE userName=%s ",
  GetSQLValueString($colname_rsGame8, "text")); 
$rsUser = mysql_query($query_rsUser, $ohiofi) or die(mysql_error());
$row_rsUser = mysql_fetch_assoc($rsUser);
$totalRows_rsUser = mysql_num_rows($rsUser);




$maxRows_rsScoreboard = 10;
$pageNum_rsScoreboard = 0;
if (isset($_GET['pageNum_rsScoreboard'])) {
  $pageNum_rsScoreboard = $_GET['pageNum_rsScoreboard'];
}
$startRow_rsScoreboard = $pageNum_rsScoreboard * $maxRows_rsScoreboard;

mysql_select_db($database_ohiofi, $ohiofi);
$query_rsScoreboard = "SELECT userName, game8 FROM users ORDER BY game8 DESC";
$query_limit_rsScoreboard = sprintf("%s LIMIT %d, %d", $query_rsScoreboard, $startRow_rsScoreboard, $maxRows_rsScoreboard);
$rsScoreboard = mysql_query($query_limit_rsScoreboard, $ohiofi) or die(mysql_error());
$row_rsScoreboard = mysql_fetch_assoc($rsScoreboard);

if (isset($_GET['totalRows_rsScoreboard'])) {
  $totalRows_rsScoreboard = $_GET['totalRows_rsScoreboard'];
} else {
  $all_rsScoreboard = mysql_query($query_rsScoreboard);
  $totalRows_rsScoreboard = mysql_num_rows($all_rsScoreboard);
}
$totalPages_rsScoreboard = ceil($totalRows_rsScoreboard/$maxRows_rsScoreboard)-1;$maxRows_rsScoreboard = 10;
$pageNum_rsScoreboard = 0;
if (isset($_GET['pageNum_rsScoreboard'])) {
  $pageNum_rsScoreboard = $_GET['pageNum_rsScoreboard'];
}
$startRow_rsScoreboard = $pageNum_rsScoreboard * $maxRows_rsScoreboard;

mysql_select_db($database_ohiofi, $ohiofi);
$query_rsScoreboard = "SELECT userName, game8 FROM users WHERE game8 > 0 ORDER BY game8 DESC";
$query_limit_rsScoreboard = sprintf("%s LIMIT %d, %d", $query_rsScoreboard, $startRow_rsScoreboard, $maxRows_rsScoreboard);
$rsScoreboard = mysql_query($query_limit_rsScoreboard, $ohiofi) or die(mysql_error());
$row_rsScoreboard = mysql_fetch_assoc($rsScoreboard);

if (isset($_GET['totalRows_rsScoreboard'])) {
  $totalRows_rsScoreboard = $_GET['totalRows_rsScoreboard'];
} else {
  $all_rsScoreboard = mysql_query($query_rsScoreboard);
  $totalRows_rsScoreboard = mysql_num_rows($all_rsScoreboard);
}
$totalPages_rsScoreboard = ceil($totalRows_rsScoreboard/$maxRows_rsScoreboard)-1;

$queryString_rsScoreboard = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsScoreboard") == false && 
        stristr($param, "totalRows_rsScoreboard") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsScoreboard = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsScoreboard = sprintf("&totalRows_rsScoreboard=%d%s", $totalRows_rsScoreboard, $queryString_rsScoreboard);





/*
mysql_select_db($database_ohiofi, $ohiofi);
$query_rsSurvey=sprintf("SELECT survey.questionNumber FROM survey WHERE survey.userName=%s AND survey.gameNumber=3 ORDER BY survey.questionNumber DESC",
  GetSQLValueString($colname_rsGame8, "text")); 
$rsSurvey = mysql_query($query_rsSurvey, $ohiofi) or die(mysql_error());
$row_rsSurvey = mysql_fetch_assoc($rsSurvey);
$totalRows_rsSurvey = mysql_num_rows($rsSurvey);


*/


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	if ($totalRows_rsGame8_check == 0) {
		  $updateSQL = sprintf("UPDATE users SET game8=%s, game8total=%s WHERE userID=%s",
							   GetSQLValueString($_POST['points'], "int"),
							   GetSQLValueString($_POST['game8total'], "int"),
							   GetSQLValueString($_POST['userNumber'], "int"));
		
		  mysql_select_db($database_ohiofi, $ohiofi);
		  $Result1 = mysql_query($updateSQL, $ohiofi) or die(mysql_error());
	}
}
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
	if ($totalRows_rsGame8_check == 0) {
		  $updateSQL = sprintf("UPDATE users SET game8total=%s WHERE userID=%s",
							   GetSQLValueString($_POST['game8total'], "int"),
							   GetSQLValueString($_POST['userNumber'], "int"));
		
		  mysql_select_db($database_ohiofi, $ohiofi);
		  $Result1 = mysql_query($updateSQL, $ohiofi) or die(mysql_error());
	}
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	if ($totalRows_rsGame8_check == 0) {
		///if ($row_rsScore3['questionNumber'] == $totalRows_rsScore3){
			
					   
	  		$insertSQL = sprintf("INSERT INTO game8responses (entryNumber, userName, questionNumber, question, answer, tally) VALUES (%s, %s, %s, %s, %s, %s)",
						   GetSQLValueString($_POST['entryNumber'], "int"),
						   GetSQLValueString($_SESSION['MM_Username'], "text"),
						   GetSQLValueString($_POST['questionNumber'], "int"),
						   GetSQLValueString($_POST['question1'], "text"),
						   GetSQLValueString($_POST['response1'], "text"),
						   GetSQLValueString($_POST['tally'], "int"));
					   
	  		mysql_select_db($database_ohiofi, $ohiofi);
	 		$Result1 = mysql_query($insertSQL, $ohiofi) or die(mysql_error());
		///}
	}
	header('Location: ' . $_SERVER['PHP_SELF']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
	if ($totalRows_rsGame8_check == 0) {
		///if ($row_rsScore3['questionNumber'] == $totalRows_rsScore3){
	  		$insertSQL = sprintf("INSERT INTO game8responses (entryNumber, userName, questionNumber, question, answer, tally) VALUES (%s, %s, %s, %s, %s, %s)",
						   GetSQLValueString($_POST['entryNumber'], "int"),
						   GetSQLValueString($_SESSION['MM_Username'], "text"),
						   GetSQLValueString($_POST['questionNumber'], "int"),
						   GetSQLValueString($_POST['question2'], "text"),
						   GetSQLValueString($_POST['response2'], "text"),
						   GetSQLValueString($_POST['tally'], "int"));
	
	  		mysql_select_db($database_ohiofi, $ohiofi);
	  		$Result1 = mysql_query($insertSQL, $ohiofi) or die(mysql_error());
		///}
	}
	header('Location: ' . $_SERVER['PHP_SELF']);
}



$points = $totalRows_rsGame8 + 1;
$currentQuestion = $totalRows_rsScore3 + 1;
//print_r($currentQuestion . "vs" . $rsSurvey['questionNumber'] );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Ear Training 2 web app</title>
<link rel="stylesheet" type="text/css" href="musictechwebapps.css" />
<style>

#head1{
	display:block;
	float:left;
	width:100%;
	height:60px;
	margin:10px 0 10px 0;
}
.divider{
	display:block;
	float:left;
	width:100%;
	height:10px;
	margin:10px 0 10px 0;
}
.topPlayButton{
	display:block;
	margin:0 auto;
	padding:0 3px 0 3px;
	position:relative;
	height:30px;
	line-height:30px;
	width:150px;
	border:2px solid black;
	text-align:center;
	background:rgba(255,255,255,1);
	-webkit-border-radius:6px;
	-moz-border-radius:6px;
	-webkit-box-shadow: 2px 2px 6px rgba(0,0,0,0.6);
}
.playButton{
	display:block;
	margin:2px;
	padding:0 3px 0 3px;
	position:relative;
	height:30px;
	line-height:30px;
	width:315px;
	border:2px solid black;
	text-align:center;
	background:rgba(0,0,0,0);
	-webkit-border-radius:6px;
	-moz-border-radius:6px;
	-webkit-box-shadow: 2px 2px 6px rgba(0,0,0,0.6);
}

.floatLeft{
	float:left;
}
#piano{
	display:block;
	margin:0 auto 0 auto;
	position:relative;
	height:180px;
	width:754px;
	border:2px solid black;
	background-image:url(../img/brushedMetal.jpg);
	-webkit-border-radius:6px;
	-moz-border-radius:6px;
	-webkit-box-shadow: 2px 2px 6px rgba(0,0,0,0.6);
}
.margin{
	display:block;
	position:relative;
	float:left;
	height:180px;
	width:26px;
	margin:0;
	text-align:left;
	font-size:10px;
}
.key{
	display:block;
	position:relative;
	float:left;
	background-image:url(img/fader.png);
	height:180px;
	width:26px;
	margin:10px 0 0 0;
	text-align:left;
	font:Arial, Helvetica, sans-serif;
	font-size:10px;
}
.overlay{
	display:block;
	position:relative;
	float:left;
	height:30px;
	/*width:26px;*/
	margin:0;
	-webkit-border-radius:6px;
	-moz-border-radius:6px;
}
.topPlayButton:hover{
	background:rgba(0,0,255,0.3);
}
.topPlayButton:active{
	background:rgba(72,61,139,0.7);
}
.playButton:hover{
	background:rgba(0,0,255,0.3);
}
.playButton:active{
	background:rgba(72,61,139,0.7);
}
.playOverlay{
	display:block;
	position:relative;
	float:left;
	height:30px;
	/*width:100px;*/
	margin:0;
	-webkit-border-radius:6px;
	-moz-border-radius:6px;
}
.playOverlay:hover{
	background:rgba(0,0,255,0.3);
}
.playOverlay:active{
	background:rgba(72,61,139,0.7);
}


#greatJob{
	text-align:center;
	margin:0 auto 0 auto;
}


#copyright{
	position:relative;
	float:left;
	margin:10px 0 0 0;
	width:100%;
	z-index:100;
	text-decoration:none;
}
#resistorsmain {
	position:relative;
	width:1000px;
	margin:30px auto 30px auto;
	padding:0 20px 20px 20px;
	background:#FFF;
	/*border:3px solid black;*/
	-webkit-border-radius:10px;
	-moz-border-radius:10px;
	/*-webkit-box-shadow:0 0 8px rgba(67,71,60,0.9);
	-moz-box-shadow:0 0 8px rgba(67,71,60,0.9);*/
	height:530px;
}
</style>
</head>

<body onload="preloader(7);" />
	<?php include("_includes/header.php"); ?>
	<div id="resistorsmain">  
	    <div id="stage">
    		<div id="head1">
            Level 2, Question <?=$currentQuestion?>
            <br />
    			<div class="topPlayButton" onclick="play();">PLAY</div>
    		</div>
            <br />
    		<div class="divider">Tones</div>
    		<div class="playButton floatLeft" onclick="gimme('200hz');" onmouseover="preloader(0);">200 Hz</div>
    		<div class="playButton floatLeft" onclick="gimme('250hz');" onmouseover="preloader(1);">250 Hz</div>
    		<div class="playButton floatLeft" onclick="gimme('300hz');" onmouseover="preloader(2);">300 Hz</div>
    		
    		<div class="playButton floatLeft" onclick="gimme('500hz');" onmouseover="preloader(3);">500 Hz</div>
    		<div class="playButton floatLeft" onclick="gimme('600hz');" onmouseover="preloader(4);">600 Hz</div>
    		<div class="playButton floatLeft" onclick="gimme('800hz');" onmouseover="preloader(5);">800 Hz</div>
    		
    		<div class="playButton floatLeft" onclick="gimme('1200hz');" onmouseover="preloader(6);">1200 Hz</div>
            <div class="playButton floatLeft" onclick="gimme('1600hz');" onmouseover="preloader(7);">1600 Hz</div>
    		<div class="playButton floatLeft" onclick="gimme('2000hz');" onmouseover="preloader(8);">2000 Hz</div>
    		
    		<div class="playButton floatLeft" onclick="gimme('3200hz');" onmouseover="preloader(9);">3200 Hz</div>
    		<div class="playButton floatLeft" onclick="gimme('4000hz');" onmouseover="preloader(10);">4000 Hz</div>
    		<div class="playButton floatLeft" onclick="gimme('4800hz');" onmouseover="preloader(11);">4800 Hz</div>
    		
    		<div class="playButton floatLeft" onclick="gimme('8000hz');" onmouseover="preloader(12);">8000 Hz</div>
    		<div class="playButton floatLeft" onclick="gimme('9600hz');" onmouseover="preloader(13);">9600 Hz</div>
            <div class="playButton floatLeft" onclick="gimme('12800hz');" onmouseover="preloader(14);">12800 Hz</div>
            <br />
    		<div class="divider">White Noise</div>
    		<div class="playButton floatLeft" onclick="gimme('whitenoise20hzto20000hz');" onmouseover="preloader(15);">20 Hz - 20000 Hz</div>
    		<div class="playButton floatLeft" onclick="gimme('whitenoise150hzto300hz');" onmouseover="preloader(16);">150 Hz - 300 Hz</div>
            <div class="playButton floatLeft" onclick="gimme('whitenoise300hzto600hz');" onmouseover="preloader(17);">300 Hz - 600 Hz</div>
    		<div class="playButton floatLeft" onclick="gimme('whitenoise600hzto1200hz');" onmouseover="preloader(18);">600 Hz - 1200 Hz</div>
            <div class="playButton floatLeft" onclick="gimme('whitenoise1200hzto2400hz');" onmouseover="preloader(19);">1200 Hz - 2400 Hz</div>
            <div class="playButton floatLeft" onclick="gimme('whitenoise2400hzto4800hz');" onmouseover="preloader(20);">2400 Hz - 4800 Hz</div>
            <div class="playButton floatLeft" onclick="gimme('whitenoise4800hzto9600hz');" onmouseover="preloader(21);">4800 Hz - 9600 Hz</div>
    		<div class="playButton floatLeft" onclick="gimme('whitenoise9600hzto19200hz');" onmouseover="preloader(22);">9600 Hz - 19200 Hz</div>
    		<div class="divider"></div>
    		<br />
    		&nbsp;
    		<br />
    		&nbsp;
		</div>
   	</div>
	
	<div id="greatJob" class="popup">
            	<h2>
                	<?php $my_array = array(0 => "Great Job!", 1 => "Nicely Done!", 2 => "That's Right!");
					shuffle($my_array);
					echo($my_array[0]);
					?>                           
                </h2>
              <form id="form1" name="form1" action="<?php echo $editFormAction; ?>" method="POST"><input name="entryNumber" type="hidden" value="" /><input name="userName" type="hidden" value="" /><input type="hidden" name="question1" id="question1" value=""><input type="hidden" name="response1" id="response1" value=""><input name="tally" type="hidden" value="1" /><input name="questionNumber" type="hidden" value="<?php echo $currentQuestion; ?>" /><input name="points" type="hidden" value="<?=$points ?>" /><input name="userNumber" type="hidden" value="<?=$row_rsUser["userID"] ?>" /><input name="game8total" type="hidden" value="<?php echo $totalRows_rsScore3; ?>" /><input class="submit button popupbutton" type="submit" name="Submit" id="Submit" value="Continue">
                  <input type="hidden" name="MM_insert" value="form1" />
                  <input type="hidden" name="MM_update" value="form1" />
              </form>
	</div>
	<div id="tryAgain" class="popup">
            	<h2>Incorrect</h2>
       		  <form id="form2" name="form2" action="<?php echo $editFormAction; ?>" method="POST"><input name="entryNumber" type="hidden" value="" /><input name="userName" type="hidden" value="" /><input type="hidden" name="question2" id="question2" value=""><input type="hidden" name="response2" id="response2" value=""><input name="tally" type="hidden" value="0" /><input name="questionNumber" type="hidden" value="<?php echo $currentQuestion; ?>" /><input name="userNumber" type="hidden" value="<?=$row_rsUser["userID"] ?>" /><input name="game8total" type="hidden" value="<?php echo $totalRows_rsScore3; ?>" /><input class="submit button popupbutton" type="submit" name="Submit" id="Submit" value="Continue">
           		  <input type="hidden" name="MM_insert" value="form2" />
                  <input type="hidden" name="MM_update" value="form2" />
                </form>
                
                
                
                <? if ($currentQuestion % 1 == 0) { ?>
                
                	<h2>Your current score is<br /><? echo $row_rsUser["game8"] ?> <? if ($row_rsUser["game8"] != 1)
                                echo " pts";
                            else
                                echo " pt";
                            ?></h2>
                    
                      
                      <hr />
                      
                      <p>High Scores</p>
                      <p>
                      <table border="0" STYLE="margin:15px;">
                        <?php
                        do { ?>
                          <tr>
                            <td><?php echo $row_rsScoreboard['userName']," "; ?></td>
                            <td><?php echo " "; ?></td>
                            <td><?php echo " ",$row_rsScoreboard['game8'];
                            if ($row_rsScoreboard['game8'] != 1)
                                echo " pts";
                            else
                                echo " pt";
                            ?>
                            
                            
                            </td>
                          </tr>
                          <?php 
                          } while ($row_rsScoreboard = mysql_fetch_assoc($rsScoreboard)); ?>
                          <tr>
                            <td><?php if ($pageNum_rsScoreboard > 0) { // Show if not first page ?><a href="<?php printf("%s?pageNum_rsScoreboard=%d%s", $currentPage, max(0, $pageNum_rsScoreboard - 1), $queryString_rsScoreboard); ?>"><font size="1">Previous</font></a><?php } // Show if not first page ?></td>
                            <td></td>
                            <td><?php if ($pageNum_rsScoreboard < $totalPages_rsScoreboard) { // Show if not last page ?>
                      <a href="<?php printf("%s?pageNum_rsScoreboard=%d%s", $currentPage, min($totalPages_rsScoreboard, $pageNum_rsScoreboard + 1), $queryString_rsScoreboard); ?>"><font size="1">Next</font></a>
                      <?php } // Show if not last page ?></td>
                          </tr>
                      </table>
                       
                      </p>
                      
                      <? } ?>
                
                
                
	</div>
	<?php include("_includes/footer.php"); ?>
    <span id="blank"></span>
    <span id="blank2"></span>
</body>
<script>
var oneChance = 0;
var youReady = 0;
var randomMP3=new Array("200hz","250hz","300hz","500hz","600hz","800hz","1200hz","1600hz","2000hz","3200hz","4000hz","4800hz","8000hz","9600hz","12800hz","whitenoise20hzto20000hz","whitenoise150hzto300hz","whitenoise300hzto600hz","whitenoise600hzto1200hz","whitenoise1200hzto2400hz","whitenoise2400hzto4800hz","whitenoise4800hzto9600hz","whitenoise9600hzto19200hz");
var question=randomMP3[7];

function newQuestion() {
	var oldQuestion=question;
	question=randomMP3[(Math.floor ( ( Math.random ( ) * 23) ))];
	while (oldQuestion==question) {
		question=randomMP3[(Math.floor ( ( Math.random ( ) * 23) ))];
	}
	document.form1.question1.value = question;
	document.form2.question2.value = question;
}

function play() {
	playSound(question);
	youReady++;
}
function gimme(pressed) {
	playSound(pressed);	
	if (youReady>0) {
		if (oneChance == 0) {
			document.form1.response1.value = pressed;
			document.form2.response2.value = pressed;
			if (question == pressed) {
				setTimeout('document.getElementById("blank").innerHTML="<embed src=\"<?php $my_array = array(0 => 'win1', 1 => 'win2', 2 => 'win3', 3 => 'win4', 4 => 'win5', 5 => 'win6', 6 => 'win7', 7 => 'win8');
						shuffle($my_array);
						echo($my_array[0]);
						?>.mp3\" hidden=\"true\" autostart=\"true\" loop=\"false\" />"',300);
				setTimeout('document.getElementById("greatJob").className = "popup popupActive"',300);
			}
			else {
				setTimeout('document.getElementById("blank").innerHTML="<embed src=\"<?php $my_array = array(0 => 'fail1', 1 => 'fail2', 2 => 'fail3', 3 => 'fail4', 4 => 'fail5', 5 => 'fail6', 6 => 'fail7', 7 => 'fail8');
						shuffle($my_array);
						echo($my_array[0]);
						?>.mp3\" hidden=\"true\" autostart=\"true\" loop=\"false\" />"',300);
				setTimeout('document.getElementById("tryAgain").className = "popup popupActive"',300);
			}
		oneChance++;
		}
	}
};

function playSound(soundfile) {
 	document.getElementById("blank").innerHTML="<embed src=\"../eartraining1/"+soundfile+".mp3\" hidden=\"true\" autostart=\"true\" loop=\"false\" />";
}

function preloader(i) {
	document.getElementById("blank2").innerHTML="<embed src=\"../eartraining1/"+randomMP3[i]+".mp3\" hidden=\"true\" autostart=\"true\" loop=\"false\" volume=\"0\" />";
}

newQuestion();

</script>
</html>