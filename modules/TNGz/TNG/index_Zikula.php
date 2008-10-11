<?php
include("begin.php");
include($cms[tngpath] . "genlib.php");
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "$mylanguage/text.php");
tng_db_connect($database_host,$database_name,$database_username,$database_password) or exit;
include($cms[tngpath] . "checklogin.php");

$flags[noicons] = true;
$flags[noheader] = true;
tng_header( $text[mnuheader], $flags );
?>

<!-- "Home Page" (the text for these messages can be found at near the bottom of text.php -->
<h1><?php echo $text[mnuheader]; ?></h1>

<?php
	if( $currentuser ) {
        $tng_username = ($currentuserdesc != "") ? $currentuserdesc : $currentuser;
		echo "<p><strong>$text[welcome] $tng_username.</strong></p>\n";
	}
?>

<h2><?php echo $text[mnusearchfornames]; ?></h2>
<!-- Do not change the form action or field names! -->
<form action="" method="GET">
<input name="name" type="hidden" value="<?php echo $cms[module]; ?>"/>
<input name="file" type="hidden" value="search"/> 
<table border="0" cellspacing="5" cellpadding="0">
	<tr><td><span class="normal"><?php echo $text[mnulastname]; ?>: </span><br/><input type="text" name="mylastname" size="14"></td></tr>
	<tr><td><span class="normal"><?php echo $text[mnufirstname]; ?>:</span><br/><input type="text" name="myfirstname" size="14"></td></tr>
	<tr><td><input type="hidden" name="mybool" value="AND"><input type="hidden" name="offset" value="0">
            <input type="submit" name="search" value="<?php echo $text[mnusearch]; ?>"></td></tr>
</table>
</form>

<h2><?php echo $text[mnufeatures]; ?></h2>

<ul>
  <li><a href="<?php echo getURL( "searchform"       , 0 )?>"><?php echo $text[mnuadvancedsearch]?></a></li>
  <li><a href="<?php echo getURL( "surnames"         , 0 )?>"><?php echo $text[mnulastnames]    ?></a></li>
  <li><a href="<?php echo getURL( "bookmarks"        , 0 )?>"><?php echo $text[bookmarks]       ?></a></li>
  <li><a href="<?php echo getURL( "browsetrees"      , 0 )?>"><?php echo $text[mnustatistics]   ?></a></li>
  <li><a href="<?php echo getURL( "browsemedia"      , 0 )?>"><?php echo $text[allmedia]        ?></a></li>  
  <li><a href="<?php echo getURL( "browsemedia"      , 1 )."mediatypeID=photos"     ?>"><?php echo $text[mnuphotos]    ?></a></li>
  <li><a href="<?php echo getURL( "browsemedia"      , 1 )."mediatypeID=histories"  ?>"><?php echo $text[mnuhistories] ?></a></li>
  <li><a href="<?php echo getURL( "browsemedia"      , 1 )."mediatypeID=documents"  ?>"><?php echo $text[documents]    ?></a></li>
  <li><a href="<?php echo getURL( "browsemedia"      , 1 )."mediatypeID=videos"     ?>"><?php echo $text[videos]       ?></a></li>
  <li><a href="<?php echo getURL( "browsemedia"      , 1 )."mediatypeID=recordings" ?>"><?php echo $text[recordings]   ?></a></li>
  <li><a href="<?php echo getURL( "browsemedia"      , 1 )."mediatypeID=headstones" ?>"><?php echo $text[headstones]   ?></a></li>
  <li><a href="<?php echo getURL( "browsealbums"     , 0 )?>"><?php echo $text[albums]          ?></a></li>
  <li><a href="<?php echo getURL( "cemeteries"       , 0 )?>"><?php echo $text[mnucemeteries]   ?></a></li>
  <li><a href="<?php echo getURL( "places"           , 0 )?>"><?php echo $text[places]          ?></a></li>
  <li><a href="<?php echo getURL( "browsenotes"      , 0 )?>"><?php echo $text[notes]           ?></a></li>
  <li><a href="<?php echo getURL( "anniversaries"    , 0 )?>"><?php echo $text[anniversaries]   ?></a></li>
  <li><a href="<?php echo getURL( "reports"          , 0 )?>"><?php echo $text[mnureports]      ?></a></li>
  <li><a href="<?php echo getURL( "browsesources"    , 0 )?>"><?php echo $text[mnusources]      ?></a></li>
  <li><a href="<?php echo getURL( "browserepos"      , 0 )?>"><?php echo $text[repositories]    ?></a></li>
  <li><a href="<?php echo getURL( "whatsnew"         , 0 )?>"><?php echo $text[mnuwhatsnew]     ?></a></li>
  <li><a href="<?php echo getURL( "changelanguage"   , 0 )?>"><?php echo $text[mnulanguage]     ?></a></li>
<?php if( $allow_admin ) { ?>
  <li><a href="index.php?module=pnTNG&func=admin"            ><?php echo $text[mnuadmin]        ?></a></li>
  <li><a href="<?php echo getURL( "showlog"          , 0 )?>"><?php echo $text[mnushowlog]      ?></a></li>        
<?php } ?>
  <li><a href="<?php echo getURL( "suggest"          , 0 )?>"><?php echo $text[contactus]      ?></a></li>
</ul>
<br />

<?php
tng_footer( "" );
?>
