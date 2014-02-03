<?php
//SET foreign_key_checks = 0;
//=CELL/(60*60*24)+"1/1/1970"
$i = 0;
$fr = fopen("uzivatele.csv","r");
$fw = fopen("uzivatel.sql","w");
$line = '';

while (!feof ($fr)) {
  $line = trim(fgets($fr, 1024));

  $sl = explode(";", $line);

  $sl[1] = '"' . $sl[1] . '"';
  $sl[2] = '"' . $sl[2] . '"';
  $sl[3] = '"' . $sl[3] . '"';
  $sl[4] = '"' . $sl[4] . '"';
  if($sl[5] == '') $sl[5] = "NULL";
  else $sl[5] = '"' . $sl[5] . '"';
  if($sl[6] == '') $sl[6] = "NULL";
  else $sl[6] = '"' . $sl[6] . '"';
  if($sl[8] == '') $sl[8] = "NULL";
  else $sl[8] = '"' . $sl[8] . '"';

  if($i++ % 20 == 0) {
     fwrite($fw, "INSERT INTO uzivatel (id, login, salt, heslo, email, posledni_prihlaseni, datum_registrace, kredit, zapomenute_heslo) VALUES \n");
//     echo "INSERT INTO uzivatel (id, login, salt, heslo, email, posledni_prihlaseni, datum_registrace, kredit, zapomenute_heslo) VALUES \n";
  }
  $sep = ($i % 20 == 0) ? ";" : ",";

   fwrite($fw, "($sl[0], $sl[1], $sl[2], $sl[3], $sl[4], $sl[5], $sl[6], $sl[7], $sl[8]) $sep \n");
}
fclose ($fr);
fclose ($fw);