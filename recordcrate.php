<!DOCTYPE html>
<html lang="en">
  <head>
    <title>TJ</title>
    <meta charset="utf-8"/>
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible"/>
    <meta content="width=device-width,initial-scale=1" name="viewport"/>
    <link href="/thrill_jockey/assets/stylesheets/tj.css" rel="stylesheet" type="text/css"></link>
    <script>
function check_record_crate(){
 var code = document.getElementById("session").value;
 var result;
 var xmlhttp;
 if(window.XMLHttpRequest)
 {
   xmlhttp= new XMLHttpRequest();
 }
 else
 {
   xmlhttp= new ActiveXObject("Microsoft.XMLHTTP");
 }
 xmlhttp.open("GET", "check_record_crate.php?session=" + code, true);
 xmlhttp.onreadystatechange = function()
 {
  if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
  {
    result =xmlhttp.responseText;
    if(isEmpty(result))
    {
     alert("You don't have any albums for download");  
    }
    else
    {
     document.getElementById('albums').innerHTML=result;
    }
  }
 }
xmlhttp.send(); 
}

function download(id,a,remaining,pid,did){
 var artist = document.getElementById(a).value;  
 window.location='http://50.57.169.40/thrill_jockey/orders/'.concat(artist).concat(".zip"); 
 var result;
 var xmlhttp;
 if(window.XMLHttpRequest)
 {
   xmlhttp= new XMLHttpRequest();
 }
 else
 {
   xmlhttp= new ActiveXObject("Microsoft.XMLHTTP");
 }
 xmlhttp.open("GET", "update.php?user=" + id +"&remaining="+ remaining +"&pid=" + pid + "&did="+did , true);
 xmlhttp.onreadystatechange = function()
 {
  if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
  {
    result =xmlhttp.responseText;
    document.getElementById('albums').innerHTML=result;
  }
 }
xmlhttp.send(); 
}

function isEmpty(str){
  return (!str || 0 === str.length);
}
</script>
  </head>
  <body>
    <header>
      <div class="container-header">
        <div class="search">
          <div class="search-inputs">
            <input placeholder="Search..." type="text"></input>
            <a class="btn-close-search" href="#">
              <img src="/thrill_jockey/images/close.png"/>
            </a>
          </div>
        </div>
        <a class="logo" href="http://50.57.169.40:3005/">
          <img src="/thrill_jockey/images/tj-logo.png"/>
        </a>
        <nav class="full nav-full-js">
          <a href="http://50.57.169.40:3005/">store</a>
          <a href="http://50.57.169.40:3005/">artists</a>
          <a href="http://50.57.169.40:3005/">tours</a>
          <a href="http://50.57.169.40:3005/">videos</a>
          <a href="http://50.57.169.40:3005/">upcoming</a>
          <a href="http://50.57.169.40:3005/">news</a>
        </nav>
      </div>
    </header>
    <br><br><br><br>
    <div style="padding-left:25px;">
    <h2> Record crate </h2>
    <p> Enter your session id:</p>
    <?php
      echo "<input type='text'id='session'/>";
      echo "<input type='button' value='Download'onClick='check_record_crate()'/><br><br><br>";
     echo "<a href='http://50.57.169.40:3005/'>Go to Home Page</a><br><br>";
    ?>
    <div id="albums">
    </div>
    <br><br><br><br>
    </div>
    <footer>
      <div class="container-footer">
        <div class="social">
          <p>Follow Thrill Jockey</p>
          <a href="https://soundcloud.com/thrilljockey">
            <img src="/thrill_jockey/images/soundcloud.png"/>
          </a>
          <a href="http://twitter.com/thrilljockey">
            <img src="/thrill_jockey/images/twitterb.png"/>
          </a>
          <a href="http://facebook.com/thrilljockey">
            <img src="/thrill_jockey/images/facebookb.png"/>
          </a>
          <a href="https://plus.google.com/+thrilljockeyrecords">
            <img src="/thrill_jockey/images/googleplus.png"/>
          </a>
          <a href="http://thrilljockey.tumblr.com/">
            <img src="/thrill_jockey/images/tumblr.png"/>
          </a>
          <a href="http://www.vimeo.com/thrilljockey">
            <img src="/thrill_jockey/images/vimeo.png"/>
          </a>
          <a href="http://www.youtube.com/user/thrilljockeyrecords?sub_confirmation=1">
            <img src="/thrill_jockey/images/youtube.png"/>
          </a>
          <a href="http://instagram.com/thrilljockey">
            <img src="/thrill_jockey/images/instagram.png"/>
          </a>
        </div>
        <div class="attribution">
          © 2013 Thrill Jockey Records
        </div>
      </div>
    </footer>
  </body>
</html>
