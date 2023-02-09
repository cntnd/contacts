?><?php
// cntnd_SKELETON_input
$cntnd_module = "cntnd_SKELETON";

// includes
cInclude('module', 'includes/class.cntnd_SKELETON.php');
cInclude('module', 'includes/script.cntnd_SKELETON.php');
cInclude('module', 'includes/style.cntnd_SKELETON.php');

// input/vars
$truncate = (bool) "CMS_VALUE[1]";
$lines = (int) "CMS_VALUE[2]";
$own_js = (bool) "CMS_VALUE[3]";
$selectedDir = "CMS_VALUE[4]";

// other vars
$uuid = rand();
$SKELETON = new Cntnd\Skeleton\CntndSkeleton($lang, $client);

?>
<div class="form-vertical">

    <div class="form-check form-check-inline">
        <input id="truncate_<?= $uuid ?>" class="form-check-input" type="checkbox" name="CMS_VAR[1]" value="true" <?php if($truncate){ echo 'checked'; } ?> />
        <label for="truncate_<?= $uuid ?>"><?= mi18n("TRUNCATE") ?></label>
    </div>

    <div class="form-group">
        <label for="lines_<?= $uuid ?>"><?= mi18n("TRUNCATE_LINES") ?></label>
        <input id="lines_<?= $uuid ?>" name="CMS_VAR[2]" type="number" value="<?= $lines ?>" />
    </div>

    <div class="form-check form-check-inline">
        <input id="ownjs_<?= $uuid ?>" class="form-check-input" type="checkbox" name="CMS_VAR[3]" value="true" <?php if($own_js){ echo 'checked'; } ?> />
        <label for="ownjs_<?= $uuid ?>"><?= mi18n("TRUNCATE_OWNJS") ?></label>
    </div>

    <div class="form-group">
        <label for="gallery_<?= $uuid ?>"><?= mi18n("GALLERY") ?></label>
        <select name="CMS_VAR[4]" id="gallery_<?= $uuid ?>" size="1">
            <option value="false"><?= mi18n("SELECT_CHOOSE") ?></option>
            <?php
            foreach ($SKELETON->folders() as $folder) {
                $selected = "";
                if ($selectedDir == $folder) {
                    $selected = 'selected="selected"';
                }
                echo '<option value="' . $folder . '" ' . $selected . '>' . $folder . '</option>';
            }
            ?>
        </select>
    </div>
</div>
<?php
