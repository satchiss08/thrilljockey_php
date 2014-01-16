<?php
 if(isset($_GET["session"]))
 {
  $conn=mysqli_connect("localhost","root","mclQghHgRI1Ajk","thrilljockey_db");
 if (mysqli_connect_errno())
   {
   echo "Failed to connect to MySQL: " . mysqli_connect_error();
   }
  $pass =$_GET["session"];
  $result = mysqli_query($conn,"SELECT tdb_download_tb.product_id AS pid, tdb_download_tb.remaining AS remaining, tdb_catalog_tb.title AS album, tdb_catalog_tb.id AS cid, tdb_artist_tb.name AS artist , tdb_catalog_tb.catnum as catnum, tdb_download_tb.id AS did FROM tdb_download_tb, tdb_product_tb, tdb_catalog_tb, tdb_lk_artist_catalog_tb, tdb_artist_tb WHERE tdb_download_tb.customer_id = '$pass' AND tdb_download_tb.remaining > 0 AND tdb_download_tb.product_id = tdb_product_tb.id AND tdb_product_tb.catalog_id = tdb_catalog_tb.id AND tdb_product_tb.catalog_id = tdb_lk_artist_catalog_tb.catalog_id AND tdb_lk_artist_catalog_tb.artist_id = tdb_artist_tb.id");
$i = 0;
$crate = array();
while ($row = mysqli_fetch_array($result) ) {
        $crate[$i] = $row;
        $i++;
}

 $response="";
 $a= 0;
 foreach ($crate as $record) {
    $response =$response."<b>".$record['artist']."</b><br>".$record['album']."Remaining: <b>".$record['remaining']."</b>";
    $response = $response."<input type='text' id=".$a." value='".$record['artist']."'"." style='display:none;'/><input type='button' value='Download' onClick='download(".$pass.",".$a.",".$record['remaining'].",".$record['pid'].",".$record['did'].")'/><br><br>";
  $a++;
 }
 echo $response;
  mysqli_close($conn); 

 }
?>
