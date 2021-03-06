<?php
/*
-------------------------------------------------------------------------
Name:	dbinfo.php
What:	Execute a query against the ttrss db (tiny tiny rss) to return newswire items 
	; allow query param for searching tickers

Who:	Craig Amos 2006-07-13

Mods:	2016-07-14 Refactor & clean
	2016-07-21 Get script working in production (Sometimes SO saves your ass! See (4) below)

Notes:	Place file in www 'root'
	NB: ** MySQL hostname is username.ipagemysql.com NOT what is listed on MyPhpAdmin page thru hosting app.

References:

1.  http://stackoverflow.com/questions/9243383/looping-through-sql-results-in-php-not-getting-entire-array?rq=1
2.  http://www.idocs.com/tags/character_famsupp_203.html
3.  http://yagudaev.com/posts/resolving-php-relative-path-problem/
4.  http://stackoverflow.com/questions/31381552/cant-connect-using-mysql-connect-to-database-in-ipage-com-hosting
-------------------------------------------------------------------------
*/

// Get vars  ; cli supports command line interface
if (PHP_SAPI === 'cli') {
    $arg1 = $argv[1];
 }
else {
    $arg1 = $_GET['name'];
}

// Support a restricted search query based on stock ticker.
// newswire articles carry the ticker at top in format
// <p>Tickers: XCNQ:NUR</br> where XCNQ is exchange and NUR is company code

if (!empty($arg1)) 
 	{
	$arg1 = strtoupper($arg1);
	printf("Restrict resultset to ticker: %s \n",$arg1);
	}

// rss_reader has limited permisssions for reading tt-rss articles on DB
$user = "rss_reader";
$pass = "rss_reader";
$db = "ttrss";

//connection to the MySQL database
$mysqli = new mysqli("equitydigitalcom.ipagemysql.com", $user, $pass, $db); 
if (!$mysqli)
{
  die('DB Connect Error: '. mysqli_connect_errno());
}
 

// Build SQL string including $arg[1] if passed
$sql =	"SELECT left(date_entered,16) as db_date,id,title
	FROM ttrss_entries WHERE link like '%thenewswire%'";

// prototype we are matching against <p>Tickers: XCNQ:NUR</br> 
// i.e. SQL WHERE clause = "content REGEXP 'Tickers:.*(XCNQ[.:.]NUR)+'"

if (!empty($arg1))
	{
	$tokens = explode(":", $arg1);
	$regex  = " and content REGEXP 'Tickers:.*(%1[.:.]%2)+'";
	$regex  = str_replace('%1',$tokens[0],$regex);
	$regex  = str_replace('%2',$tokens[1],$regex);
	$regex  = str_replace('"', '', $regex);		// allow double quotes to be passed
	$sql .= $regex;					// append to SQL str	
	
	}

$sql.= " ORDER BY date_entered DESC";

if ($result = $mysqli->query($sql)) 
{

// Query metadata
$rowcount = mysqli_num_rows($result);
if ($rowcount==0) { die('No rows to process. Exiting.');}
$rowcount = sprintf("Resultset has %d rows.\n",$rowcount);
$qMeta = date("D M d, Y G:i");
$qMeta ="Query executed: ".$qMeta."<br>".$rowcount."<br>";

//Start htmt table output
echo "<div id=nw_tabledata>";
echo "<h1>Newswire articles via thenewswire.ca</h1>";
echo '<div class="qMeta">';
echo "<p>$qMeta</p>";
echo '</div>';

// NB: Using single quotes for ALL html table def strings 
echo '<table class="db-table" allign="left" cellpadding="1" cellspacing="0">';

	// #0acad1 matches color used on website codilight theme (light blue)
	echo '<tr>';
		$th='<th bgcolor=#0acad1  style="text-align: left;"> <FONT COLOR=White SIZE=4 FACE="Geneva, Arial">%1</FONT></th>';
		echo str_replace('%1','Date',$th);
		echo str_replace('%1','Title',$th);
		echo str_replace('%1','ID',$th);
	echo '</tr>';

	// NB: *MUST* use mysqli (not mysql) fn calls
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){	
	
	  echo '<tr>';
	
			// prototype  echo "<td>$cell</td>";
			print '<td style="width: 11%;">'. $row['db_date'] .'</td>';
  			print '<td style="width: 87%;">'. $row['title'] .'</td>';			
			print '<td style="width: 20%;">'. $row ['id'] .'</td>';

	 echo '</tr>';
		
	}
   
echo '</table>';
echo '</div>';
}
else {throw new Exception(mysqli_error($mysqli)."[ $sql]");}

?>

