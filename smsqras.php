<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

        	
<?php 
$creden=file("credentials.txt");
$source=file_get_contents('http://orai.kasvyksta.lt/kaunas');

$msg=$_POST["msg"];
$nr=$_POST["nr"];
$debug=$_GET["debug"];
$menulis=file_get_contents("http://day.lt");
$menulis=mb_convert_encoding($menulis,"UTF-8","ISO-8859-13");

$lt = array();
$lt[0] = '/ą/';
$lt[1] = '/ę/';
$lt[2] = '/č/';
$lt[3] = '/ė/';
$lt[4] = '/į/';
$lt[5] = '/š/';
$lt[6] = '/ž/';
$lt[7] = '/ų/';
$lt[8] = '/ū/';
$lt[9] = '/Š/'; 
$lt[10] = '/&minus;/';
$lt[11] = '/\(pilneja\)/';
$lt[12] = '/\(dyla\)/';
$en = array();
$en[0] = 'a';
$en[1] = 'e';
$en[2] = 'c';
$en[3] = 'e';
$en[4] = 'i';
$en[5] = 's';
$en[6] = 'z';
$en[7] = 'u';
$en[8] = 'u';
$en[9] = 's';
$en[10] = '-';
$en[11] = '';
$en[12] = '';
$shvej=array();
$lonvej=array();
$lonvej[0]="Siaures vakaru";
$lonvej[0]="Siaures vakaru";
$lonvej[1]="Siaures vakaru";
$lonvej[2]="Siaures rytu";
$lonvej[3]="Pietryciu";
$lonvej[4]="Pietryciu";
$lonvej[5]="Pietvakariu";
$lonvej[6]="Pietvakariu";
$lonvej[7]="Siaures rytu";
$lonvej[8]="Pietvakariu";
$lonvej[9]="Pietryciu";
$lonvej[10]="Siaures vakaru";
$lonvej[11]="Siaures rytu";
$lonvej[12]="Vakaru";
$lonvej[13]="Rytu";
$lonvej[14]="Siaures";
$lonvej[15]="Pietu";
$shvej[0]="/^WNW$/";
$shvej[1]="/^NNW$/";
$shvej[2]="/^ENE$/";
$shvej[3]="/^ESE$/";
$shvej[4]="/^SSE$/";
$shvej[5]="/^WSW$/";
$shvej[6]="/^SSW$/";
$shvej[7]="/^NNE$/";
$shvej[8]="/^SW$/";
$shvej[9]="/^SE$/";
$shvej[10]="/^NW$/";
$shvej[11]="/^NE$/";
$shvej[12]="/^W$/";
$shvej[13]="/^E$/";
$shvej[14]="/^N$/";
$shvej[15]="/^S$/";

$source=strtolower($source);
$source=preg_replace($lt, $en, $source);
$menulis=strtolower($menulis);
$menulis=preg_replace($lt, $en, $menulis);
$menulis=preg_match("/<p class=\"left\" title=\"menulis\">\n(.*?)<br/m",$menulis,$menulis_m);
$menulis=preg_match("/<a href=.*>(.*)<\/a>/",$menulis_m[1],$menulis_m2);
$menulis=trim($menulis_m2[1]);

//naktis + rytoj diena
preg_match("/<table border=\"0\" id=\"forecast-table\">(.*?)<\/table>/is",$source,$reikalinga_dalis);
//[0][0] - naktis
//[0][2] - diena
preg_match_all("/<img alt=\"(.*)\" title.*\/>/i",$reikalinga_dalis[1],$debesa);
preg_match_all("/>(.*)\&\#8451;<\/div>/i",$reikalinga_dalis[1],$temperatur);
preg_match_all("/<div class=\"winddesc\".*\/css\/kryptis\/forecast-table\/(.*)\.png/i",$reikalinga_dalis[1],$veja);


//siandien diena
preg_match("/<div id=\"forecast\" class=\"left\">(.*)<div class=\"block2-wprec\"/is",$source,$reikalinga_dalis2);
preg_match_all("/<div class=\"block2-temp\">(.*)&#8451;<\/div>/i",$reikalinga_dalis2[1],$temperaturad);
preg_match_all("/src=\"\/css\/kryptis\/block2\/(.*)\.jpg\"/i",$reikalinga_dalis2[1],$vejasd);
preg_match_all("/<div class=\"city kaunas siandien-oras diena\"  style=\"display:none;\">(.*)\"Orai Kaune\"/is",file_get_contents("http://orai.kasvyksta.lt"),$debesaid);
preg_match("/alt=\".*\" title=\"(.*)\">/i",$debesaid[1][0],$debesadienos);


rsort($temperaturad[1]); //didziausias skaicius
array_splice($temperatur[1],4,10); //paliekam tik vienos dienos
sort($temperatur[1]); //maziausias skaicius


$vejd=strtoupper($vejasd[1][1]);
$vejn=strtoupper($veja[1][0]);
$vejd=preg_replace($shvej, $lonvej, $vejd);
$vejn=preg_replace($shvej, $lonvej, $vejn);
$out="Debesuotumas: ".$debesadienos[1]."/".$debesa[1][0]."\nTemperatura: ".$temperaturad[1][0]."/".$temperatur[1][0]."\nVejas: ".$vejd."/".$vejn."\nMenulis: $menulis";


 if($debug=="app1") {
     
	$api_key = trim($creden[0]);
	$registrationIDs = array(trim($creden[1]));
	$url = 'https://android.googleapis.com/gcm/send';
	$fields = array('registration_ids' => $registrationIDs,'data'=> array( "message" => $out, "number" => trim($creden[2])));
	$headers = array('Authorization: key=' . $api_key,'Content-Type: application/json');

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );
	$result = curl_exec($ch);
	curl_close($ch); 
} else {
echo "<b>Debug</b>\n"; 
} 

?>

              <?php echo substr("<p style=\"font-size:15px\">Debesuotumas: ".$debesadienos[1]."/".$debesa[1][0]."<br>Temperatura: ".$temperaturad[1][0]."/".$temperatur[1][0]."<br>Vejas: ".$vejd."/".$vejn."<br>Menulis: $menulis",0,159); ?>
                        
        </div>
                  
            
          
            	
 
      
        Copyright © 2015-2016 gymka

</body>
</html>


