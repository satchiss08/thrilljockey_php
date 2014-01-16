<?php
 //require 'thrilltest2.php';
 require_once '/var/www/thrill_jockey/download.inc';
 if(isset($_GET["pass"]))
 {
  $conn=mysqli_connect("localhost","root","mclQghHgRI1Ajk","thrilljockey_db");
 // Check connection
 if (mysqli_connect_errno())
   {
   echo "Failed to connect to MySQL: " . mysqli_connect_error();
   }
  $pass =$_GET["pass"];
  $result = mysqli_query($conn, "SELECT catalog_id FROM tdb_album_promo_pass_tb WHERE password ='$pass'AND used = 0");
  $contador = 0;
  $res;
  while($row=mysqli_fetch_array($result))
  {
   $contador = $contador + 1;
   $res = $row[0];   
  }
  if($contador == 1)
  {
   pack_for_download($res,'/var/www/thrill_jockey/prueba/',true);
   $catnum = "SELECT tdb_artist_tb.name AS artist, tdb_catalog_tb.id AS cat_id, tdb_catalog_tb.catnum AS catnum, tdb_catalog_tb.year AS year, tdb_catalog_tb.title AS album, tdb_ax_label_tb.name AS label, tdb_catalog_tb.promo_only_booklet AS include_booklet FROM tdb_catalog_tb, tdb_lk_artist_catalog_tb, tdb_artist_tb, tdb_ax_label_tb WHERE tdb_catalog_tb.id = '{$res}' AND tdb_lk_artist_catalog_tb.catalog_id = tdb_catalog_tb.id AND tdb_lk_artist_catalog_tb.main = 1 AND tdb_artist_tb.id = tdb_lk_artist_catalog_tb.artist_id AND	tdb_ax_label_tb.id = tdb_catalog_tb.label_id";
  
  $result = mysqli_query($conn, $catnum);
  $artist = "";
  while ($row = mysqli_fetch_row($result)) {
    $artist = $row['0'];
   }
   $result = mysqli_query($conn, "UPDATE tdb_album_promo_pass_tb SET used = 1 WHERE password ='$pass'");
   echo $artist; 
  }
  else
  {
   echo '';
  }
  
  mysqli_close($conn);  

 }
?>
