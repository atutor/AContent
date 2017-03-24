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
            <a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#contenttop" title="<?php echo _AT('goto_top'); ?> Alt-c" ><?php echo _AT('goto_top'); ?>
              <img src="<?php echo $this->base_path; ?>themes/<?php echo $this->theme; ?>/images/goto_top.png" alt="<?php echo _AT('goto_top'); ?> Alt-c" class="goto"/> 
			</a>
          </span>
        </div>  
<?php 
} // end of goto top
?>
      </div> <!-- end of contentcolumn -->
    </div> <!-- end of contentwrapper -->
	
	<div id="footer" role="contentinfo">
	    <div id="logo">
      <a href="http://www.atutor.ca/acontent"><img  src="<?php echo $this->base_path; ?>images/AC_Logo1_sm.png"  alt="AContent  Logo" style="border:none;" /></a>
    </div>

      <div class="foot_text">
        <a href="<?php echo TR_BASE_HREF; ?>documentation/web_service_api.php" title="<?php echo _AT("web_service_api"); ?>" target="atutor"><?php echo _AT('web_service_api'); ?></a>
          &nbsp;&nbsp;&nbsp;&nbsp;
        <a href="<?php echo TR_BASE_HREF; ?>documentation/oauth_server_api.php" title="<?php echo _AT("oauth_server_api"); ?>" target="atutor"><?php echo _AT('oauth_server_api'); ?></a>
      </div>

<?php require(TR_INCLUDE_PATH.'html/languages.inc.php'); ?>

      <div class="foot_text">
  <!-- guide -->

            <div>
    <a href="#" onclick="trans.utility.poptastic('<?php echo TR_GUIDES_PATH."index.php?p=home/index.php"; ?>'); return false;" target="atutor"><em><?php echo _AT('general_help'); ?></em></a>&nbsp;
        </div>


      </div>
    </div>
  </div> <!--  end center-content div -->

  <!-- <div class="bottom"></div> -->
  <!--  bottom for liquid-round theme -->
  
    <div style="width:100%; ">
  <?php
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
  </div>
  </div>

</body>
</html>