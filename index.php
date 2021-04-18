<?php
set_time_limit(0);

function create_possible_arrays($string) {
    $letters = str_split($string);

    $combos = array_unique($letters);
    $lastres = $letters;
    for ($i = 1; $i < count($letters); $i++) {
        $newres = array();
        foreach ($lastres as $r) {
            foreach ($letters as $let) {
                $new = $r . $let;
                if (!in_array($new, $newres)) {
                    $newres[] = $new;

                    // Action
                    $combos[] = $new;
                }

            }
        }

        $lastres = $newres;

    }

    return $combos;
}

function whois_query($domain)
{
 
 // fix the domain name:
 $domain = strtolower(trim($domain));
 $domain = preg_replace('/^http:\/\//i', '', $domain);
 $domain = preg_replace('/^www\./i', '', $domain);
 $domain = explode('/', $domain);
 $domain = trim($domain[0]);
 
 // split the TLD from domain name
 $_domain = explode('.', $domain);
 $lst = count($_domain)-1;
 $ext = $_domain[$lst];
 
 // the list of whois servers
 // most taken from www.iana.org/domains/root/db/
 $servers = array(
  "com" => "whois.internic.net"
 );
 
 if (!isset($servers[$ext])) {
  die('Error: No matching whois server found!');
 }
 
 $nic_server = $servers[$ext];
 
 $output = '';
 
 // connect to whois server:
 if ($conn = fsockopen($nic_server, 43)) {
  fwrite($conn, $domain."\r\n");
  while (!feof($conn)) {
   $output .= fgets($conn, 128);
  }
  fclose($conn);
 } else {
  die('Error: Could not connect to ' . $nic_server . '!');
 }
 return $output;
}

function checkDomainTaken($domain){
    $result = strtok(whois_query($domain), "\n");

    if(substr( $result, 0, 13 ) === "No match for "){
        return true;
    }
    return false;
}

$listeDomaine = array();
$listeDomaine = create_possible_arrays('abc');

echo "Nombre de domaine testé : " . count($listeDomaine);
echo "<br>Domaines disponible : <br>";

for ($i = 0; $i < count($listeDomaine); $i++){

    if (checkDomainTaken($listeDomaine[$i].'.com')){        
        echo "<br>Obtenir : <a href=https://domains.google.com/registrar/search?searchTerm=".$listeDomaine[$i].".com>".$listeDomaine[$i].'.com</a>';
    }
}
