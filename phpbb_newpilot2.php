<?php
function xmlspecialchars($text) {
   return str_replace('-' , ' ' , str_replace('&#039;', '&apos;', htmlspecialchars($text, ENT_QUOTES)));
}



include ( "../forum/config.php");

$username=$dbuser;
$password=$dbpasswd;
$database=$dbname;
$server=$dbhost;

$con = mysql_connect($server,$username,$password)or die( "Unable to connect to database");			@mysql_select_db($database) or die( "Unable to select database");


if(isset($_COOKIE['lastregdate']))
$lastregdate = $_COOKIE['lastregdate'];
else $lastregdate = time() - 3600 * 24 * 30;
$lrd = $lastregdate;
	$mode = $_GET[mode];
	$query="select contact_name , pnp_name , pnp_id , lat , lon ,  public_comment, 
	type , city , state , airport, email, email_alt, cell_num, home_num, other from contacts where lat * lat  > 0" ;

$query = 'select '
        . ' phpbb_users.user_id ,'
        . ' phpbb_users.username ,'
	. ' from_unixtime(phpbb_users.user_regdate) as joined,'
        . ' phpbb_users.user_regdate, '
        . ' phpbb_users.user_email, '
        . ' phpbb_users.user_sig, '
        . ' phpbb_users.user_occ, '
        . ' phpbb_users.user_interests, ' 
        . ' phpbb_profile_fields_data.user_id ,'
        . ' phpbb_profile_fields_data.pf_airport_id ,'
        . ' airports.apt_id ,'
        . ' airports.apt_name ,'
        . ' airports.lat ,'
        . ' airports.city ,'
        . ' airports.state ,'
        . ' airports.lon'
        . ' from phpbb_users,'
        . ' phpbb_profile_fields_data ,'
        . ' airports'
        . ' where ' 
        . ' phpbb_profile_fields_data.pf_pilot_yn = 1 and'
        . ' phpbb_profile_fields_data.user_id = phpbb_users.user_id and '
        . ' phpbb_users.user_regdate > ' . $lrd . ' and '
        . ' airports.apt_id = UCASE(phpbb_profile_fields_data.pf_airport_id) order by user_regdate desc'
        . ' ';
	$result=mysql_query($query);
if ( mysql_numrows($result) > 0 )
setcookie('lastregdate' , mysql_result($result,0,"phpbb_users.user_regdate"));
echo       '<html><body><table border=1><tr><th>UserName</th><th>Joined</th><th>State</th><th>City</th>'
     .'<th>Airport</th><th>Email</th><th>Sig</th><th>Occ</th><th>Interests</th></tr>';

       $num=mysql_numrows($result);
       mysql_close;
       $i=0;
       while ($i < $num) {

echo '<tr><td>' . mysql_result($result,$i,"phpbb_users.username") . '</td>'
	   . '<td>' . mysql_result($result,$i,"joined")  . '</td>'
       . '<td>' . mysql_result($result,$i,"airports.state")  . '</td>'
       . '<td>' . mysql_result($result,$i,"airports.city")  . '</td>'
       . '<td>' . mysql_result($result,$i,"airports.apt_id")  . '</td>'
       . '<td>' . mysql_result($result,$i,"phpbb_users.user_email")  . '</td>'
       . '<td>' . mysql_result($result,$i,"phpbb_users.user_sig")  . ' &nbsp</td>'
       . '<td>' . mysql_result($result,$i,"phpbb_users.user_occ")  . ' &nbsp</td>'
       . '<td>' . mysql_result($result,$i,"phpbb_users.user_interests")  . ' &nbsp</td>'
       . '<td>' . mysql_result($result,$i,"phpbb_users.user_regdate") . ' &nbsp</td>'
       . '</tr>'
 ;
	       $i++;
	       }
		   echo 
		   '</table><br>'. $i . ' Pilots Listed<br></body></html>';
       ?>
