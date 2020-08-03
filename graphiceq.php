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

$colname_rsGame3 = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsGame3 = $_SESSION['MM_Username'];
}
mysql_select_db($database_ohiofi, $ohiofi);
$query_rsGame3=sprintf("SELECT game1responses.entryNumber, game1responses.questionNumber FROM game1responses WHERE userName=%s AND game1responses.tally=1 ORDER BY game1responses.questionNumber DESC",
  GetSQLValueString($colname_rsGame3, "text")); 
$rsGame3 = mysql_query($query_rsGame3, $ohiofi) or die(mysql_error());
$row_rsGame3 = mysql_fetch_assoc($rsGame3);
$totalRows_rsGame3 = mysql_num_rows($rsGame3);




mysql_select_db($database_ohiofi, $ohiofi);
$query_rsScore3=sprintf("SELECT game1responses.entryNumber, game1responses.questionNumber FROM game1responses WHERE userName=%s",
  GetSQLValueString($colname_rsGame3, "text")); 
$rsScore3 = mysql_query($query_rsScore3, $ohiofi) or die(mysql_error());
$row_rsScore3 = mysql_fetch_assoc($rsScore3);
$totalRows_rsScore3 = mysql_num_rows($rsScore3);


/* ------------------------------------- rsGame3_check is the fail safe -----------------------------------------*/

mysql_select_db($database_ohiofi, $ohiofi);
$query_rsGame3_check=sprintf("SELECT game1responses.entryNumber, game1responses.questionNumber FROM game1responses WHERE userName=%s AND game1responses.questionNumber=%s",
  GetSQLValueString($colname_rsGame3, "text"), GetSQLValueString($_POST['questionNumber'], "int")); 
$rsGame3_check = mysql_query($query_rsGame3_check, $ohiofi) or die(mysql_error());
$row_rsGame3_check = mysql_fetch_assoc($rsGame3_check);
$totalRows_rsGame3_check = mysql_num_rows($rsGame3_check);


mysql_select_db($database_ohiofi, $ohiofi);
$query_rsUser=sprintf("SELECT users.userID, users.game1 FROM users WHERE userName=%s ",
  GetSQLValueString($colname_rsGame3, "text")); 
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
$query_rsScoreboard = "SELECT userName, game1 FROM users ORDER BY game1 DESC";
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
$query_rsScoreboard = "SELECT userName, game1 FROM users WHERE game1 > 0 ORDER BY game1 DESC";
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
  GetSQLValueString($colname_rsGame3, "text")); 
$rsSurvey = mysql_query($query_rsSurvey, $ohiofi) or die(mysql_error());
$row_rsSurvey = mysql_fetch_assoc($rsSurvey);
$totalRows_rsSurvey = mysql_num_rows($rsSurvey);


*/


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	if ($totalRows_rsGame3_check == 0) {
		  $updateSQL = sprintf("UPDATE users SET game1=%s, game1total=%s WHERE userID=%s",
							   GetSQLValueString($_POST['points'], "int"),
							   GetSQLValueString($_POST['game1total'], "int"),
							   GetSQLValueString($_POST['userNumber'], "int"));
		
		  mysql_select_db($database_ohiofi, $ohiofi);
		  $Result1 = mysql_query($updateSQL, $ohiofi) or die(mysql_error());
	}
}
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
	if ($totalRows_rsGame3_check == 0) {
		  $updateSQL = sprintf("UPDATE users SET game1total=%s WHERE userID=%s",
							   GetSQLValueString($_POST['game1total'], "int"),
							   GetSQLValueString($_POST['userNumber'], "int"));
		
		  mysql_select_db($database_ohiofi, $ohiofi);
		  $Result1 = mysql_query($updateSQL, $ohiofi) or die(mysql_error());
	}
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	if ($totalRows_rsGame3_check == 0) {
		///if ($row_rsScore3['questionNumber'] == $totalRows_rsScore3){
			
					   
	  		$insertSQL = sprintf("INSERT INTO game1responses (entryNumber, userName, questionNumber, question, answer, tally) VALUES (%s, %s, %s, %s, %s, %s)",
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
	if ($totalRows_rsGame3_check == 0) {
		///if ($row_rsScore3['questionNumber'] == $totalRows_rsScore3){
	  		$insertSQL = sprintf("INSERT INTO game1responses (entryNumber, userName, questionNumber, question, answer, tally) VALUES (%s, %s, %s, %s, %s, %s)",
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

$points = $totalRows_rsGame3 + 1;
$currentQuestion = $totalRows_rsScore3 + 1;
//print_r($currentQuestion . "vs" . $rsSurvey['questionNumber'] );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Graphic EQ web app</title>

<link rel="stylesheet" type="text/css" href="musictechwebapps.css" />

</head>

<body onload="preloader('01')" />
	<?php include("_includes/header.php"); ?>
	<div id="graphiceqmain">  
        
        <div id="header">
        <h2>Question <?=$currentQuestion?></h2>
            <div id="playButton">
                <div class="playOverlay" onclick="play();"><font size=4>Play</font></div>
            </div>
        <br />
        </div>
        <div id="stage">
            <div id="piano">
                <div class="pianomargin">
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(01);">&nbsp;40</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(02);">&nbsp;50</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(03);">&nbsp;63</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(04);">&nbsp;80</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(05);">100</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(06);">125</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(07);">160</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(08);">200</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(09);">250</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(10);">315</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(11);">400</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(12);">500</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(13);">630</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(14);">800</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(15);">&nbsp;1k</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(16);">1.25</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(17);">1.6</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(18);">&nbsp;2k</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(19);">2.5</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(20);">3.15</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(21);">&nbsp;4k</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(22);">&nbsp;5k</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(23);">6.3</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(24);">&nbsp;8k</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(25);">10k</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(26);">12.5</div>
                </div>
                <div class="pianokey">
                    <div class="pianooverlay" onclick="gimme(27);">16k</div>
                </div>
                <div class="pianomargin">
                </div>
            </div>
        </div>
        <div id="footer">
        	
            
        </div>
        <span id="blank"></span>
        
        <br />
    </div>
    <?php include("_includes/footer.php"); ?>
    <div id="greatJob" class="popup">
            	<h2>
                	<?php $my_array = array(0 => "Great Job!", 1 => "Nicely Done!", 2 => "That's Right!");
					shuffle($my_array);
					echo($my_array[0]);
					?>                           
                </h2>
              <form id="form1" name="form1" action="<?php echo $editFormAction; ?>" method="POST"><input name="entryNumber" type="hidden" value="" /><input name="userName" type="hidden" value="" /><input type="hidden" name="question1" id="question1" value=""><input type="hidden" name="response1" id="response1" value=""><input name="tally" type="hidden" value="1" /><input name="questionNumber" type="hidden" value="<?php echo $currentQuestion; ?>" /><input name="points" type="hidden" value="<?=$points ?>" /><input name="userNumber" type="hidden" value="<?=$row_rsUser["userID"] ?>" /><input name="game1total" type="hidden" value="<?php echo $totalRows_rsScore3; ?>" /><input class="submit button centered" type="submit" name="Submit" id="Submit" value="Continue">
                  <input type="hidden" name="MM_insert" value="form1" />
                  <input type="hidden" name="MM_update" value="form1" />
              </form>
            </div>
            <div id="tryAgain" class="popup">
            	<h2>Incorrect</h2>
       		  <form id="form2" name="form2" action="<?php echo $editFormAction; ?>" method="POST"><input name="entryNumber" type="hidden" value="" /><input name="userName" type="hidden" value="" /><input type="hidden" name="question2" id="question2" value=""><input type="hidden" name="response2" id="response2" value=""><input name="tally" type="hidden" value="0" /><input name="questionNumber" type="hidden" value="<?php echo $currentQuestion; ?>" /><input name="userNumber" type="hidden" value="<?=$row_rsUser["userID"] ?>" /><input name="game1total" type="hidden" value="<?php echo $totalRows_rsScore3; ?>" /><input class="submit button centered" type="submit" name="Submit" id="Submit" value="Continue">
           		  <input type="hidden" name="MM_insert" value="form2" />
                  <input type="hidden" name="MM_update" value="form2" />
                </form>
                
                
                
                <? if ($currentQuestion % 1 == 0) { ?>
                
                	<h2>Your current score is<br /><? echo $row_rsUser["game1"] ?> <? if ($row_rsUser["game1"] != 1)
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
                            <td><?php echo " ",$row_rsScoreboard['game1'];
                            if ($row_rsScoreboard['game1'] != 1)
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
</body>
<script>
var question=0;
var oneChance=0;

function newQuestion() {
	var randomNumber=Math.floor(Math.random()*27+1);
	if (randomNumber>27) {
		randomNumber=1;
	}
	if (randomNumber<1){
		randomNumber=1;
	}
	question=randomNumber;
	document.form1.question1.value = randomNumber;
	document.form2.question2.value = randomNumber;
}

function play() {
	playSound(question);
}

function gimme(pressed) {
	playSound(pressed);	
	if (oneChance == 0) {
		document.form1.response1.value = pressed;
		document.form2.response2.value = pressed;
	}
	oneChance++;
	if (question==pressed) {
			///wait 1000 milliseconds and then popup "Great Job!"
  			setTimeout('document.getElementById("greatJob").className = "popup popupActive"',1000);
  		}
		else {
			///wait 1000 milliseconds and then popup "Try Again."
  			setTimeout('document.getElementById("tryAgain").className = "popup popupActive"',1000);
  		}
}

function playSound(soundfile) {
 	document.getElementById("blank").innerHTML="<embed src=\""+soundfile+".mp3\" hidden=\"true\" autostart=\"true\" loop=\"false\" />";
}


function preloader(soundfile) {
 	document.getElementById("blank").innerHTML= "<embed src=\""+soundfile+".mp3\" hidden=\"true\" autostart=\"true\" loop=\"false\" volume=\"0\" />";
}

newQuestion();

</script>
</html>
<?php


mysql_free_result($rsGame3);

mysql_free_result($rsGame3_check);

mysql_free_result($rsScore3);

mysql_free_result($rsUser);

?>