<?php
function xmlspecialchars($text) {
   return str_replace('-' , ' ' , str_replace('&#039;', '&apos;', htmlspecialchars($text, ENT_QUOTES)));
}



include ( "../../forum/config.php");

$username=$dbuser;
$password=$dbpasswd;
$database=$dbname;
$server=$dbhost;

$con = mysql_connect($server,$username,$password)or die( "Unable to connect to database");			@mysql_select_db($database) or die( "Unable to select database");


	$mode = $_GET[mode];
	$query="select contact_name , pnp_name , pnp_id , lat , lon ,  public_comment, 
	type , city , state , airport, email, email_alt, cell_num, home_num, other from contacts where lat * lat  > 0" ;

$query = 'select '
        . ' phpbb_users.user_id ,'
        . ' phpbb_users.username ,'
        . ' phpbb_users.user_email, '
        . ' phpbb_users.user_sig, '
        . ' phpbb_users.user_occ, '
        . ' phpbb_users.user_interests, '
        . ' from_unixtime(phpbb_users.user_lastvisit) as lastvisit, ' 
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
        . ' airports.apt_id = UCASE(phpbb_profile_fields_data.pf_airport_id) order by airports.state, phpbb_users.user_lastvisit desc'
        . ' ';
	$result=mysql_query($query);

echo       '<html><body><table border=1><tr><th>UserName</th><th>State</th><th>City</th></tr>';

       $num=mysql_numrows($result);
       mysql_close;
       $i=0;
       while ($i < $num) {

echo '<tr><td>' . mysql_result($result,$i,"phpbb_users.username") . '</td>'
       . '<td>' . mysql_result($result,$i,"airports.state")  . '</td>'
	   . '<td>' . mysql_result($result,$i,"airports.city")  . '</td>'
       . '</tr>'
 ;
	       $i++;
	       }
		   echo 
		   '</table></body></html>';
       ?>