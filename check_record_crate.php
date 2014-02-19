<?php
 if(isset($_GET["session"]))
 {
  $conexion = mysqli_connect("localhost","root","mclQghHgRI1Ajk","thrilljockeydb_dev");
  
  if (mysqli_connect_errno())
  {
   echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
  
  $user = $_GET["user_id"];

  $result = mysqli_query($conexion,"SELECT downloads.user_id AS user_id, downloads.remaining AS remaining, downloads.product_id AS product_id, downloads.id AS download_id, spree_variants.id AS variant_id, spree_products.name AS album, spree_product_properties.value AS artist FROM downloads INNER JOIN spree_variants ON downloads.product_id=spree_variants.id INNER JOIN spree_products ON spree_variants.product_id=spree_products.id INNER JOIN spree_product_properties ON spree_products.id=spree_product_properties.product_id INNER JOIN spree_properties ON spree_product_properties.property_id=spree_properties.id WHERE downloads.user_id='$user' AND spree_properties.name="tdb_artist_tb_name" AND downloads.remaining > 0");

  //$result = mysqli_query($conn,"SELECT tdb_download_tb.product_id AS pid, tdb_download_tb.remaining AS remaining, tdb_catalog_tb.title AS album, tdb_catalog_tb.id AS cid, tdb_artist_tb.name AS artist , tdb_catalog_tb.catnum as catnum, tdb_download_tb.id AS did FROM tdb_download_tb, tdb_product_tb, tdb_catalog_tb, tdb_lk_artist_catalog_tb, tdb_artist_tb WHERE tdb_download_tb.customer_id = '$pass' AND tdb_download_tb.remaining > 0 AND tdb_download_tb.product_id = tdb_product_tb.id AND tdb_product_tb.catalog_id = tdb_catalog_tb.id AND tdb_product_tb.catalog_id = tdb_lk_artist_catalog_tb.catalog_id AND tdb_lk_artist_catalog_tb.artist_id = tdb_artist_tb.id");

  $i = 0;
  $crate = array();
  while ($row = mysqli_fetch_array($result) ) {
        $crate[$i] = $row;
        $i++;
  }

  $response="";
  $artist= 0;
  $album= 1;
  
  foreach ($crate as $record) {
    $response =$response."<b>".$record['artist']."</b><br>".$record['album']." Remaining: <b>".$record['remaining']."</b>";
    $response = $response."<input type='text' id=".$artist." value='".$record['artist']."'"." style='display:none;'/><input type='text' id=".$album." value='".$record['album']."'"." style='display:none;'/><input type='button' value='Download' onClick='download(".$user.",".$artist.",".$album.",".$record['variant_id'].",".$record['remaining'].",".$record['product_id'].",".$record['download_id'].")'/><br><br>";
    $artist++;
    $album++;
  }
  
  echo $response;
  mysqli_close($conn); 

 }
?>
