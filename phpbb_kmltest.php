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
	$fromzip=$_GET[fromzip];
	$tozip=$_GET[tozip];
	if ($tozip == "" ) {
	                   $tozip = substr ( $fromzip , 5 , 5  );
	                   $fromzip = substr ( $fromzip , 0 , 5 ) ;
					   }
	$query='select lat , lon from zipcodes where zip="' . $fromzip . '"';
	$result=mysql_query($query);
	$fromlat = mysql_result($result,0,"lat");
	$fromlon = mysql_result($result,0,"lon");
	$query='select lat , lon from zipcodes where zip="' . $tozip . '"';
    if ( $dbg == "yes") echo $query;
	$result=mysql_query($query);
	$tolat = mysql_result($result,0,"lat");
	$tolon = mysql_result($result,0,"lon") ;
	$lowlat =  min ( $fromlat - 1 , $tolat - 1 ) ;
	$lowlon =  min ( $fromlon - 1 , $tolon - 1 ) ;
	$hilat =  max ( $fromlat + 1 , $tolat + 1 ) ;
	$hilon =  max ( $fromlon + 1 , $tolon + 1 ) ;

	$mode = $_GET[mode];

$query = 'select '
        . ' phpbb_users.user_id ,'
        . ' phpbb_users.username ,'
        . ' phpbb_profile_fields_data.user_id ,'
        . ' phpbb_profile_fields_data.pf_zip_code ,'
        . ' zipcodes.zip ,'
        . ' zipcodes.lat ,'
        . ' zipcodes.city ,'
        . ' zipcodes.state ,'
        . ' zipcodes.lon'
        . ' from phpbb_users,'
        . ' phpbb_profile_fields_data ,'
        . ' zipcodes'
        . ' where ' 
        . ' phpbb_profile_fields_data.pf_foster_yn = 1 and'
        . ' phpbb_profile_fields_data.user_id = phpbb_users.user_id and '
        . ' zipcodes.zip = phpbb_profile_fields_data.pf_zip_code and '
        . ' zipcodes.lat >= ' . $lowlat . ' and '
        . ' zipcodes.lat <= ' . $hilat . ' and '
        . ' zipcodes.lon >= ' . $lowlon . ' and '
        . ' zipcodes.lon <= ' . $hilon . ' '
        . ' ';
	$result=mysql_query($query);
	header('Content-type: application/vnd.google-earth.kml+xml');
    echo '<?xml version="1.0" encoding="UTF-8"?>
		 <kml xmlns="http://earth.google.com/kml/2.2">
		 <Document>
		 		   <name>Pilots N Paws - Volunteer Locations</name>
				   <description>
				      <![CDATA[Pilots N Paws - Volunteer Locations www.pilotsnpaws.org]]>
				   </description>
         <Style id="style1">
    <IconStyle>
      <Icon>
       <href>http://maps.google.com/mapfiles/ms/micons/blue-dot.png</href> 
      </Icon>
    </IconStyle>
  </Style>';

echo '<Placemark><LineString>
        <extrude>1</extrude>
        <tessellate>1</tessellate>
        <altitudeMode>absolute</altitudeMode>
        <coordinates>';
		echo " $fromlon , $fromlat , 0 ";
		echo '
		';
		echo "$tolon , $tolat , 0 ";
				echo '
		';

		echo '</coordinates>
          </LineString>
		  </Placemark>';
       $num=mysql_numrows($result);
       $i=0;
       while ($i < $num) {
	   $contact_name = mysql_result($result,$i,"phpbb_users.username");  	   
	   $pnp_name = mysql_result($result,$i,"phpbb_users.username");
	   $pnp_id = mysql_result($result,$i,"phpbb_users.user_id");
	   $lat = mysql_result($result,$i,"zipcodes.lat") + rand(-100,100) / 10000 ;
	   $lon = mysql_result($result,$i,"zipcodes.lon")  + rand(-100,100) / 10000 ;
	   $type = "VOLUNTEER";
           $city = mysql_result($result,$i,"zipcodes.city");
           $state = mysql_result($result,$i,"zipcodes.state");

	   $public_comment = $type;

	   echo '<Placemark>';
	
	

	   echo '<name>';
	   echo $pnp_name;
         echo ' (pilot)';
	   echo '</name>';

    echo '<Point>
      <coordinates>';
	  echo $lon ;
	  echo ',' ;
	  echo $lat ;
	  echo ',0.000000</coordinates>
    </Point>
  </Placemark>';
	       $i++;
	       }

$query = 'select '
        . ' phpbb_users.user_id ,'
        . ' phpbb_users.username ,'
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
        . ' airports.apt_id = UCASE(phpbb_profile_fields_data.pf_airport_id) and '
        . ' airports.lat >=' . $lowlat . 'and '
        . ' airports.lat <=' . $hilat . 'and '
        . ' airports.lon >=' . $lowlon . 'and '
        . ' airports.lon <=' . $hilon;
       $result=mysql_query($query);
       $num=mysql_numrows($result);
       mysql_close;
       $i=0;
       while ($i < $num) {
	   $contact_name = mysql_result($result,$i,"phpbb_users.username");  	   
	   $pnp_name = mysql_result($result,$i,"phpbb_users.username");
	   $pnp_id = mysql_result($result,$i,"phpbb_users.user_id");
	   $lat = mysql_result($result,$i,"airports.lat");
	   $lon = mysql_result($result,$i,"airports.lon");
	   $type = "PILOT";
	   $city = mysql_result($result,$i,"airports.city");
	   $state = mysql_result($result,$i,"airports.state");
	   $airport = mysql_result($result,$i,"airports.apt_id");
	   $public_comment = $type;
	   echo '<Placemark>';
	   echo '<name>';
	   echo $pnp_name;
	   echo '</name>';
    echo '<Point>
      <coordinates>';
	  echo $lon ;
	  echo ',' ;
	  echo $lat ;
	  echo ',0.000000</coordinates>
    </Point>
  </Placemark>';
	       $i++;
	       }
		   echo 
		   '</Document></kml>';
       ?>
