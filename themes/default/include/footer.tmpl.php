<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2010                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('TR_INCLUDE_PATH')) { exit; }

global $languageManager, $_my_uri;

if ($this->course_id > 0) { ?>
        <div style="clear:both;text-align:right;" id="gototop">		
          <br />
          <span style="font-size:smaller;padding-right:3px;">
            <a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#content" title="<?php echo _AT('goto_top'); ?> Alt-c" ><?php echo _AT('goto_top'); ?>
              <img src="<?php echo $this->base_path; ?>themes/<?php echo $this->theme; ?>/images/goto_top.gif" alt="<?php echo _AT('goto_top'); ?> Alt-c" border="0"/> 
			</a>
          </span>
        </div>  
<?php 
} // end of goto top
?>
      </div> <!-- end of contentcolumn -->
    </div> <!-- end of contentwrapper -->
	
	<div id="footer">
<?php 

if($languageManager->getNumEnabledLanguages() > 1) {
?>
      <div align="center" id="lang" style="clear: left"><br />
<?php
	if ($languageManager->getNumEnabledLanguages() > 5) {
		echo '        <form method="get" action="'.htmlspecialchars($_my_uri, ENT_QUOTES).'">';
		echo '          <label for="lang" style="display:none;">'._AT('translate_to').' </label>';
		$languageManager->printDropdown($_SESSION['lang'], 'lang', 'lang');
		echo '          <input type="submit" name="submit_language" class="button" value="'._AT('translate').'" />';
		echo '        </form>';
	} else {
		echo '        <small><label for="lang">'._AT('translate_to').' </label></small>';
		$languageManager->printList($_SESSION['lang'], 'lang', 'lang', htmlspecialchars($_my_uri));
	}
?>
        <br/><br/>
      </div>
<?php } // end of displaying language selection ?>

      <div align="center" style="clear:both;margin-left:auto; width:30em;margin-right:auto;">
        <a href="<?php echo TR_BASE_HREF; ?>documentation/web_service_api.php" title="<?php echo _AT("web_service_api"); ?>" target="_new"><?php echo _AT('web_service_api'); ?></a>
          &nbsp;&nbsp;&nbsp;&nbsp;
        <a href="<?php echo TR_BASE_HREF; ?>documentation/oauth_server_api.php" title="<?php echo _AT("oauth_server_api"); ?>" target="_new"><?php echo _AT('oauth_server_api'); ?></a>
        <br /><br />
      </div>
		<br style="clear:both;" />
      <div style="margin-left:auto; margin-right:auto; width:20em;">
        <small><?php if (isset($this->course_copyright)) echo htmlentities_utf8($this->course_copyright, ENT_QUOTES, 'UTF-8').'<br />'; echo _AT("copyright"); ?></small><br />
  <!-- guide -->

    <div>
    <a href="#" onclick="trans.utility.poptastic('<?php echo TR_GUIDES_PATH."index.php?p=home/index.php"; ?>'); return false;" target="_new"><em><?php echo _AT('general_help'); ?></em></a>&nbsp;
  </div>


      </div>
		
    </div>
  </div> <!--  end center-content div -->

  <div class="bottom"></div>
  <!--  bottom for liquid-round theme -->
  </div>
      <div id="logo">
      <a href="http://www.atutor.ca/acontent"><img  src="<?php echo $this->base_path; ?>/images/AC_Logo1_sm.png"  alt="AContent  Logo" style="border:none;" /></a>
    </div>
    <!-- BEAT -->
   
</body>
</html>

<?php
// Timer, calculate how much time to load the page
// starttime is in include/header.inc.php
$mtime = microtime(); 
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0]; 
$endtime = $mtime; 
$totaltime = ($endtime - $starttime); 

if (defined('TR_DEVEL') && TR_DEVEL) 
{
	debug(TABLE_PREFIX, 'TABLE_PREFIX');
	debug(DB_NAME, 'DB_NAME');
	debug($totaltime. ' seconds.', "TIME USED"); 
	debug($_SESSION);
}
// Timer Ends

?>
