<?php
/*
-------------------------------------------------------------------------
Name:	dbinfo.php
What:	Execute a query against the ttrss db (tiny tiny rss) to return newswire items 
	; allow query param for searching tickers
Who:	Craig Amos 2006-07-13
Mods:
-------------------------------------------------------------------------
Notes:

See: http://stackoverflow.com/questions/9243383/looping-through-sql-results-in-php-not-getting-entire-array?rq=1

"You need to use the following because if you call mysql_fetch_array outside of the loop, you're only returning an array of all the elements in the first row." - 

while($row = mysql_fetch_array($result))
{
   // This will loop through each row, now use your loop here

}
This doc was useful in foratting table output without using css
http://www.idocs.com/tags/character_famsupp_203.html

 When including this file in other php modules keep in mind the following directives to ensure the file is located. Also ensure that the file has correct permissions and owner (www-data in my instance, same as Apache module). This can be the cause of many 'file not found' issues. As per http://yagudaev.com/posts/resolving-php-relative-path-problem/

 - Use $_SERVER["DOCUMENT_ROOT"] – We can use this variable to make all our includes relative to the server root directory, instead of the current working directory(script’s directory). Then we would use something like this for all our includes:

include($_SERVER["DOCUMENT_ROOT"] . "/dir/script_name.php");

 - Use dirname(__FILE__) – The __FILE__ constant contains the full path and filename of the script that it is used in. The function dirname() removes the file name from the path, giving us the absolute path of the directory the file is in regardless of which script included it. Using this gives us the option of using relative paths just as we would with any other language, like C/C++. We would prefix all our relative path like this:

include(dirname(__FILE__) . "/dir/script_name.php");

-------------------------------------------------------------------------
*/

// Get vars  ; cli supports command line interface
if (PHP_SAPI === 'cli') {
    $arg1 = $argv[1];
 }
else {
    $arg1 = $_GET['name'];
}

// Supports restricted search query based on stock ticker.
// newswire articles carry the ticker at top in format
// <p>Tickers: XCNQ:NUR</br> where XCNQ is exchange and NUR is company code

if (!empty($arg1)) 
 	{
	$arg1 = strtoupper($arg1);
	$ticker = $arg1;
	printf("Restrict resultset to ticker: %s \n",$arg1);
	echo "<br>";
	}

// Debug use only !!
//echo ($_SERVER["DOCUMENT_ROOT"] . "/dbinfo.php");
//echo "<BR>";

// rss_reader has limited permisssions for reading tt-rss articles on DB
$user = "rss_reader";
$pass = "rss_reader";
$db = "ttrss";

//connection to the MySQL database
$mysqli = new mysqli("localhost", $user, $pass, $db) or die ('Unable to connect to database.');

/* check connection */
if ($mysqli->connect_errno) {
    printf("DB connection failed: %s\n", $mysqli->connect_error);
    exit();
}

// Build SQL string including $arg[1] if passed
$sql =	"SELECT left(date_entered,16) as db_date,id,title
	FROM ttrss_entries WHERE link like '%thenewswire%'";

// prototype we are matching against <p>Tickers: XCNQ:NUR</br> 
// XCNQ is eXchange code and NUR is stock code
// i.e. where clause = "content REGEXP 'Tickers:.*(XTSE[.:.]GSI)+'"

if (!empty($ticker))
	{
	// Allow double quotes to be passed and handle ; tokenize and sub
	// i.e. can handle XCNQ:NUR and "XCNQ:NUR" as php arg
	$tokens = explode(":", $ticker);
	$regex  = " and content REGEXP 'Tickers:.*(%1[.:.]%2)+'";
	$regex  = str_replace('%1',$tokens[0],$regex);
	$regex  = str_replace('%2',$tokens[1],$regex);
	$regex  = str_replace('"', '', $regex);
	// Append to SQL str	
	$sql .= $regex;
	}

$sql.= " ORDER BY date_entered DESC";

if ($result = $mysqli->query($sql)) 
{

// Query metadata
$rowcount = mysqli_num_rows($result);
if ($rowcount==0) { die('No rows to process. Exiting.');}
$rowcount = sprintf("Resultset has %d rows.\n",$rowcount);
$QMeta = date("D M d, Y G:i");
$QMeta ="Query executed: ".$QMeta."<br>".$rowcount."<br>";

// Use following for debug purposes.
//$fieldcount = $result->field_count;
//printf("Resultset has %d fields.\n",$fieldcount);
//echo '<pre>' . var_dump($result) . '</pre>';

//Start htmt table output
echo "<div id=nw_tabledata>";
echo "<h1>Newswire articles via thenewswire.ca</h1>";
echo '<div class="QMeta">';
echo "<p>$QMeta</p>";
echo '</div>';

// NB: Using single quotes for ALL html table def strings 
echo '<table allign="left" cellpadding="1" cellspacing="0" class="db-table" "BGCOLOR=NAVY">';

// NB: *MUST* use mysqli (not mysql) fn calls

	// #0acad1 matches color used on website codilight theme
	echo '<tr>';
		echo '<th bgcolor=#0acad1  style="text-align: left;"> <FONT COLOR=White SIZE=4 FACE="Geneva, Arial">Date</FONT></th>';
		echo '<th bgcolor=#0acad1  style="text-align: left;"> <FONT COLOR=White SIZE=4 FACE="Geneva, Arial">Title</FONT></th>';
		echo '<th bgcolor=#0acad1  style="text-align: left;"> <FONT COLOR=White SIZE=4 FACE="Geneva, Arial">ID</FONT></th>';
	echo '</tr>';


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
