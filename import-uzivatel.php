<?php
//=CELL/(60*60*24)+"1/1/1970"
$i = 0;
$fr = fopen("uzivatel.csv","r");
$fw = fopen("uzivatel.sql","w");
$line = '';

while (!feof ($fr)) {
  $line = fgets($fr, 1024);
  $sl = explode(";", $line);


  $sl[1] = '"' . $sl[1] . '"';
  $sl[2] = '"' . $sl[2] . '"';
  $sl[5] = '"' . $sl[5] . '"';
  $sl[7] = '"' . $sl[7] . '"';
  $sl[8] = '"' . trim($sl[8]) . '"';



  if($i++ % 20 == 0) {
    //id login heslo email prihlaseni registrace kredit zapomenute
    fwrite($fw, "INSERT INTO uzivatel (id, login, salt, heslo, email, posledni_prihlaseni, datum_registrace, kredit, zapomenute_heslo) VALUES \n");
  }
  $sep = ($i % 20 == 0) ? ";" : ",";

  fwrite($fw, "($sl[0], $sl[1], $sl[2], $sl[3], $sl[4], $sl[5], $sl[6], $sl[7], $sl[8]) $sep \n");

}
fclose ($fr);
fclose ($fw);