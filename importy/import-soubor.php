<?php
$fw = fopen("soubor.sql","w");
$i = 0;
if ($handle = opendir('midi/data/old')) {
  while (false !== ($entry = readdir($handle))) {
    if ($entry == "." || $entry == "..") continue;
    
    $pok = explode("_", $entry);
    $id = $pok[0];

    if(strpos($entry, 'skladba')) $format = '4';
    elseif(strpos($entry, 'demo')) $format = '11';
    elseif(strpos($entry, 'text')) $format = '1';

    $pok = explode(".", $entry);
    $ext = '"' . trim($pok[1]) . '"';

    echo "skladba-$id-$format.$ext\n";
    

    if($i++ % 20 == 0) {
      fwrite($fw, "INSERT INTO soubor (id, skladba_id, format_id, nazev) VALUES \n");
    }
    $sep = ($i % 20 == 0) ? ";" : ",";

    fwrite($fw, "(0, $id, $format, $ext) $sep \n");
    copy("midi/data/old/$entry", "midi/data/skladba-$id-$format");
  }
  closedir($handle);
}
fclose ($fw);