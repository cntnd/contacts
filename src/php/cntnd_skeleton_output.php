<?php
// cntnd_SKELETON_output
$cntnd_module = "cntnd_SKELETON";

// assert framework initialization
defined('CON_FRAMEWORK') || die('Illegal call: Missing framework initialization - request aborted.');

// editmode
$editmode = cRegistry::isBackendEditMode();

// includes
cInclude('module', 'includes/class.cntnd_SKELETON.php');
if ($editmode) {
    cInclude('module', 'includes/script.cntnd_SKELETON.php');
    cInclude('module', 'includes/style.cntnd_SKELETON.php');
}

// input/vars
$truncate = (bool) "CMS_VALUE[1]";
$lines = (int) "CMS_VALUE[2]";
if (empty($lines)){
  $lines = 5;
}
$own_js = (bool) "CMS_VALUE[3]";
$selectedDir = "CMS_VALUE[4]";

// other vars
$uuid = rand();
$text = "CMS_HTML[1]";
$SKELETON = new Cntnd\Skeleton\CntndSkeleton($lang, $client);

// module
if ($editmode){
    echo '<span class="module_box"><label class="module_label">'.mi18n("MODULE").'</label></span>';
}

$tpl = cSmartyFrontend::getInstance();
$tpl->assign('truncate', $truncate);
$tpl->assign('uuid', 'idart'.$idart);
$tpl->assign('text', $text);
$tpl->display('default.html');
?>
