<?php

function download_info($catalogID) {
  global $conn;
  $catalogID = mysqli_real_escape_string($conn, $catalogID);

  $sql = "SELECT tdb_artist_tb.name AS artist, tdb_catalog_tb.id AS cat_id, tdb_catalog_tb.catnum AS catnum, tdb_catalog_tb.year AS year, tdb_catalog_tb.title AS album, tdb_ax_label_tb.name AS label, tdb_catalog_tb.promo_only_booklet AS include_booklet FROM tdb_catalog_tb, tdb_lk_artist_catalog_tb, tdb_artist_tb, tdb_ax_label_tb WHERE tdb_catalog_tb.id = '{$catalogID}' AND tdb_lk_artist_catalog_tb.catalog_id = tdb_catalog_tb.id AND tdb_lk_artist_catalog_tb.main = 1 AND tdb_artist_tb.id = tdb_lk_artist_catalog_tb.artist_id AND	tdb_ax_label_tb.id = tdb_catalog_tb.label_id";
  
  $result = mysqli_query($conn, $sql);

  while ($row = mysqli_fetch_row($result)) {
    $album['artist'] 	= $row['0'];
    $album['title'] 	= $row['4'];
    $album['cid'] 		= $row['1'];
    $album['catnum'] 	= $row['2'];
    $album['year'] 		= $row['3'];
    $album['label'] 	= $row['5'];
    $album['promo_only_booklet'] 	= $row['6'];
  }
  return $album;
}


function pack_for_download($catalogID, $tmpDir, $include_booklet=TRUE)
{
  require_once('/var/www/thrill_jockey/ID3/getid3.php');
  require_once('/var/www/thrill_jockey/ID3/write.php');
  require_once('/var/www/thrill_jockey/ID3/getid3.lib.php');
  
 
  $conn=mysqli_connect("localhost","root","mclQghHgRI1Ajk","thrilljockey_db");
  $catalogID = mysqli_real_escape_string($conn, $catalogID);

  $album = download_info($catalogID);

  $getsongs = "SELECT tdb_song_tb.id, tdb_song_tb.track, tdb_song_tb.name FROM tdb_song_tb WHERE tdb_song_tb.album_id = {$album['cid']} ORDER BY track ASC";
  $result_songs = mysqli_query($conn, $getsongs);

  $i=0;
  $tracks = array();
  
  while ($songs = mysqli_fetch_row($result_songs)) {
    
    $tracks[$i]['sid'] = $songs['0'];
    $tracks[$i]['track'] = $songs['1'];
    $tracks[$i]['title'] = $songs['2'];
    $i++;
  }
  

  if(is_dir($tmpDir)) {
    rmdirr($tmpDir);
  }
  
  mkdir($tmpDir, 0777);  
  $tmpDir2 = $tmpDir."/".$album['catnum'];
  if(is_dir($tmpDir2)) {
    rmdirr($tmpDir2);
  }
  mkdir($tmpDir2, 0770);
  $coverFile = "/var/www/thrill_jockey/Documents/images/albums/thumbs/".$album['cid'].".jpg";
  
  $cF = fopen($coverFile,"rb");
  $APICdata = fread($cF, filesize($coverFile));
  fclose($cF);
  
  if (file_exists("/var/www/thrill_jockey/Documents/images/albums/full/".$album['cid'].".jpg")) {
    $a="/var/www/thrill_jockey/Documents/images/albums/full/".$album['cid'].".jpg";
  } else {
    $a=$coverFile;
  }
  $b=$tmpDir."/".$album['catnum']."/cover.jpg";
  copy ($a, $b);
  
  $booklet_path="/var/www/thrill_jockey/Documents/booklets/albums/".$catalogID.".pdf";
  if ($include_booklet && file_exists($booklet_path) ) { 
   $b=$tmpDir."/".$album['catnum']."/booklet.pdf";
    copy ($booklet_path, $b);
  }

  foreach ($tracks as $track) {
    if ($track['track'] < 10) {$track['track'] = "0" . $track['track']; }
    
    assemble_for_download($album, $track, $APICdata, $tmpDir);
  }
  if(file_exists("/var/www/thrill_jockey/prueba/".$album['artist'].".zip"))
  {
    unlink("/var/www/thrill_jockey/prueba/".$album['artist'].".zip");
  }
  $zip = new ZipArchive();
  $zip_file = "/var/www/thrill_jockey/prueba/".$album['artist'].".zip";
  $dir = opendir("/var/www/thrill_jockey/prueba/".$album['catnum']);
  $file_list = array();
  
  while(false !== ($archive = readdir($dir)))
  {
    array_push($file_list, "/var/www/thrill_jockey/prueba/".$album['catnum']."/".$archive);
  } 
  closedir($dir);

  

  if($zip->open($zip_file, ZIPARCHIVE::CREATE) === true){
    foreach($file_list as $file){
     if($file !== $zip_file){
      $zip->addFile($file,substr($file,strlen($tmpDir)));
    }
  }
  $zip->close();
  }
  if (isset($APICdata)) { unset($APICdata); }
}

function rmdirr ($dir) {

	if ($objs = glob($dir."/*")) {

		foreach($objs as $obj) {
			is_dir($obj)? rmdirr($obj) : unlink($obj);
			}
	}
	rmdir($dir);

}

function readfile_download($filename,$retbytes=true) {
  $chunksize = 1*(8*1024); // how many bytes per chunk
  $buffer = '';
  $cnt =0;
  
  $handle = fopen($filename, 'rb');
  if ($handle === false) {
    return false;
  }
  $i = 0;
  while (!feof($handle)) {
    $i++;
    //flush();
    //ob_flush();
    $buffer = fread($handle, $chunksize);
    if (strlen($buffer) == 0) { break;}
    print $buffer;
    set_time_limit(0);
    if ($retbytes) {
      $cnt += strlen($buffer);
    }
  }
  $status = fclose($handle);
  if ($retbytes && $status) {
    return $cnt; // return num. bytes delivered like readfile() does.
  } else {
    return $status;
  }
}


function assemble_for_download($album, $track, $APICDATA, $tmpDir) {

	$masterTrack = "/var/www/thrill_jockey/Documents/audio/albums/".$album['cid']."/".$track['sid'].".mp3";
	$newTrack = $tmpDir."/".$album['catnum']."/".$track['track']." - ".addslashes(clean_download_filename($track['title'])).".mp3";
	copy($masterTrack, $newTrack);
#print "copy $masterTrack $newTrack\n";
	$tagwriter = new getid3_writetags;
	$tagwriter->filename       = $newTrack;
	$tagwriter->tagformats     = array('id3v1', 'id3v2.3');
	
	// set various options (optional)
	$tagwriter->overwrite_tags = true;
	$tagwriter->tag_encoding   ="ISO-8859-1";
	$tagwriter->remove_other_tags = true;
	

	$cr = "(P) ".$album['year']. " Thrill Jockey Records\n(C) ".$album['year']. " ".rtrim($album['artist']);	
	$ident = md5($_SESSION['user']['uid'] . 'rocking without DRM');
	$TagData['title'][]   = $track['title'];
	$TagData['artist'][]  = rtrim($album['artist']);
	$TagData['album'][]   = $album['title'];
	$TagData['year'][]    = $album['year'];
	$TagData['genre'][]   = "Rock";
	$TagData['comment'][] = $cr;
	$TagData['track'][]   = $track['track'];
	$TagData['frodus'][] = $ident;
	global $coverFile;
        
    if ($coverFile)
    {
	    list($APIC_width, $APIC_height, $APIC_imageTypeID) = GetImageSize($coverFile);
    
    } else {
    } 
	
	$TagData['attached_picture'][0]['data']          = $APICDATA;
	$TagData['attached_picture'][0]['picturetypeid'] = "0x03";
	$TagData['attached_picture'][0]['description']   = $album['catnum'];
	$TagData['attached_picture'][0]['mime']          = 'image/jpeg';
	
	
	$tagwriter->tag_data = $TagData;

	$tagwriter->WriteTags();
        
} // assembler

function clean_download_filename($inp) {

        $goodchars="abcdefghijklmnopqrstuvwxyz1234567890-";
        $out = "";

        $i = 0;
        while ($i < strlen($inp)) {

                if (stristr($goodchars, $inp{$i})) { $out .= $inp{$i}; } else { $out .= " "; }


        $i++;
        }

		return $out;
} // filecleaner



 function in_user_crate($userid, $product_id)
   {
     global $conn;

     $sql = "SELECT tdb_catalog_tb.id as cid, tdb_download_tb.product_id AS pid, tdb_download_tb.remaining AS remaining FROM tdb_catalog_tb, tdb_download_tb, tdb_product_tb  WHERE tdb_download_tb.customer_id = '{$userid}' AND tdb_download_tb.product_id = '{$product_id}' AND tdb_download_tb.remaining > 0 AND tdb_catalog_tb.id = tdb_product_tb.catalog_id AND tdb_product_tb.id = tdb_download_tb.product_id";

     $result = mysqli_query($conn, $sql);
     if ($result and mysqli_num_rows($result) > 0) {
       $row = mysqli_fetch_array($result);
       return $row[0];
     }
     return FALSE;
   }
