<?php
//=CELL/(60*60*24)+"1/1/1970"
$i = 0;
$fr = fopen("dobijeni.csv","r");
$fw = fopen("dobijeni.sql","w");
$line = '';

while (!feof ($fr)) {
  $line = trim(fgets($fr, 1024));
  $sl = explode(";", $line);


  $sl[2] = '"' . $sl[2] . '"';
  $sl[3] = '"' . $sl[3] . '"';
  $sl[4] = '"' . $sl[4] . '"';

  if($i++ % 20 == 0) {
    fwrite($fw, "INSERT INTO dobijeni (uzivatel_id, castka, vs, datum, vyrizeno) VALUES \n");
  }
  $sep = ($i % 20 == 0) ? ";" : ",";

  fwrite($fw, "($sl[0], $sl[1], $sl[2], $sl[3], $sl[4]) $sep \n");

}
fclose ($fr);
fclose ($fw);