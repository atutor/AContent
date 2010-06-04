<?php 
if (!defined('TR_INCLUDE_PATH')) { exit; } 
global $_base_path;
//debug($this->default_status);
$compact_title = str_replace(' ', '', $this->title);
?>

<br />
<script language="javascript" type="text/javascript">
  trans.utility.printSubmenuHeader("<?php echo $this->title; ?>", "<?php echo $_base_path; ?>", "<?php echo _AT('show'); ?>", "<?php echo _AT('hide'); ?>", "<?php echo $this->default_status; ?>");
</script>

<div class="box" id="menu_<?php echo $compact_title ?>">
	<?php echo $this->dropdown_contents; ?>
</div>

<script language="javascript" type="text/javascript">
cookie_value = trans.utility.getcookie("m_<?php echo $this->title; ?>");
if (cookie_value == "0" || cookie_value == "" && "<?php echo $this->default_status; ?>" == "hide")
{
	jQuery("#menu_<?php echo $compact_title; ?>").hide();
}
else if (cookie_value == "1")
{
	jQuery("#menu_<?php echo $compact_title; ?>").show();
}
</script>