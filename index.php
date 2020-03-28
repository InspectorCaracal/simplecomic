<html>
<head>

  <!-- Change the text inside the title tags to your comic title! -->
  <title>A Simple Comic Site</title>

</head>

<!--
  The style below applies to the comic image and all navigation images.
  You can change it or move it to your own stylesheet.
-->
<style>
  .comic_img { border:0px; }
</style>

<body>
<?php
// Change these to suit your site!
$homepage  = "http://example.com"; // The home page of your comic
$comic_dir = "comics";             // The folder where comic image files are kept
$news_dir  = "news";               // The folder where post/news HTML files are kept
$digits    = 3;                    // The number of digits in each comic ID. e.g. 3 for 001

$firstlink = "first.gif";  // first page button
$prevlink  = "back.gif";   // previous page button
$homelink  = "home.gif";   // Home button
$nextlink  = "next.gif";   // next page button
$newlink   = "newest.gif"; // newest page button

// !! DON'T change any PHP below this line, unless you know what you're doing
// Skip to the end if you need to add styling or move page elements.

// These two functions print out all the comic image IDs as a dropdown menu
function add_option($item, $key, $target_dir) {
  if (substr(mime_content_type("$target_dir/$item"),0,5) == 'image') {
    $item_id = explode(".", $item)[0];
    if ($item_id == $_GET['id']) echo "<option selected>$item_id</option>\n";
    else echo "<option>$item_id</option>\n";
  }
}
function comics_dropdown($target_dir) {
  echo '<form name="comiclist">';
  echo '<select name="id" id="id">';
  if (filetype($target_dir) == 'dir') {
    $file_list = scandir($target_dir,1);
    array_walk($file_list, 'add_option', $target_dir);
  }
  echo '</select><input type="submit" value="Go" method="get"></form>';

}

// Checks whether a page exists for a given ID in the directory
// If it does, returns the file name
function page_exists($idnum, $target_dir) {
  global $digits;
  if (strlen($idnum) < $digits) $idnum = str_pad($idnum, $digits, '0', STR_PAD_LEFT);
  if (filetype($target_dir) == 'dir') {
    $flist = glob("$target_dir/$idnum.*");
    if (count($flist) > 0) return $flist[0];
    else return false;
  }
  else return false;
}

// Retrieves the newest comic or alphabetically "largest" file
function get_newest($target_dir, $comic) {
  // scandir in reverse order, grab the top one for a while loop until it's an image
  if (filetype($target_dir) == 'dir') {
    $templist = scandir($target_dir,1);
    $i=0;
    if ($comic) {
      foreach($templist as $item) {
        if (substr(mime_content_type("$target_dir/$item"),0,5) == 'image') break;
        else $i++;
      }
    }
    $newest = explode(".", $templist[$i])[0];
    return $newest;
  }
  else return false;
}

// Displays the navigation buttons
function display_nav($target_dir) {
  global $firstlink, $prevlink, $homelink, $nextlink, $newlink, $homepage;
  $php_file = $_SERVER['PHP_SELF'];
  if ($newest = get_newest($target_dir, true)) {
    if (empty($_GET['id'])) {
      $prev = $newest-1;
      $next = $newest;
      $page_id = $newest;
    }
    else {
      $prev = $_GET['id']-1;
      $next = $_GET['id']+1;
      $page_id = $_GET['id'];
      if ($next > $newest) $next = $newest;
    }
    if ($prev > 1) echo "<a href=\"$php_file?id=1\"><img src=\"$firstlink\" alt=\"First\" class=\"comic_img\" /></a>";
    if ($prev > 0) echo "<a href=\"$php_file?id=$prev\"><img src=\"$prevlink\" alt=\"Previous\" class=\"comic_img\" /></a>";
    echo "<a href=\"$homepage\"><img src=\"$homelink\" alt=\"Home\" class=\"comic_img\" /></a>";
    if ($page_id < $newest) echo "<a href=\"$php_file?id=$next\"><img src=\"$nextlink\" alt=\"Next\" class=\"comic_img\" /></a>";
    if ($next < $newest) echo "<a href=\"$php_file?id=$newest\"><img src=\"$newlink\" alt=\"Newest\" class=\"comic_img\" /></a>\n";
  }
}

// Displays the comic image for the current page
function display_comic($target_dir) {
  if (empty($_GET['id'])) $page_id = get_newest($target_dir, true);
  else $page_id = $_GET['id'];
  if ($comic_img = page_exists($page_id, $target_dir)) {
    echo "<img src=\"$comic_img\" class=\"comic_img\" />";
  }
}

// Displays the news file for the current page.
function display_news($target_dir) {
  if (empty($_GET['id'])) $page_id = get_newest($target_dir, false);
  else $page_id = $_GET['id'];
  if ($news_file = page_exists($page_id, $target_dir)) include($news_file);
}
?>

<!-- This is the part that actually displays the page.
You can add to it as needed for extra menus, to insert style classes, etc. -->

<div align="center">
<?php display_comic($comic_dir); /*displays the comic image*/ ?>
<br/><br/>
<?php display_nav($comic_dir); /*displays the comic navigation buttons*/ ?>
<?php comics_dropdown($comic_dir); /*displays the archive dropdown*/ ?>
</div>

<div>
<?php display_news($news_dir); /*inserts the news post html*/ ?>
</div>
  
</body>
</html>