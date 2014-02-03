<?php
//=CELL/(60*60*24)+"1/1/1970"
$i = 0;
$fr = fopen("skladby.csv","r");
$fw = fopen("skladby.sql","w");
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
    fwrite($fw, "INSERT INTO skladba (id, nazev, autor, zanr_id, cena, datum_pridani, pocet_stazeni, verze, poznamka) VALUES \n");
  }
  $sep = ($i % 20 == 0) ? ";" : ",";

  fwrite($fw, "($sl[0], $sl[1], $sl[2], $sl[3], $sl[4], $sl[5], $sl[6], $sl[7], $sl[8]) $sep \n");

}
fclose ($fr);
fclose ($fw);