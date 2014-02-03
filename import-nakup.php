<?php
//=CELL/(60*60*24)+"1/1/1970"
$i = 0;
$fr = fopen("stahovani.csv","r");
$fw = fopen("nakup.sql","w");
$line = '';

while (!feof ($fr)) {
  $line = trim(fgets($fr, 1024));
  $sl = explode(";", $line);

  $sl[3] = '"' . $sl[3] . '"';

  if($i++ % 20 == 0) {
    fwrite($fw, "INSERT INTO nakup (id, uzivatel_id, skladba_id, datum, cena) VALUES \n");
  }
  $sep = ($i % 20 == 0) ? ";" : ",";

  fwrite($fw, "($sl[0], $sl[1], $sl[2], $sl[3], $sl[4]) $sep \n");

}
fclose ($fr);
fclose ($fw);