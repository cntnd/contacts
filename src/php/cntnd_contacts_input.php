?><?php
// cntnd_contacts_input
$cntnd_module = "cntnd_contacts";

// includes
cInclude('module', 'includes/class.cntnd_contacts_input.php');
cInclude('module', 'includes/style.cntnd_contacts.php');

// input/vars
$source = "CMS_VALUE[1]";
$csv = "CMS_VALUE[10]";
$encoding = "CMS_VALUE[11]";
$delimiter = "CMS_VALUE[12]";
$headers = (bool)"CMS_VALUE[13]";
$cache = (bool)"CMS_VALUE[14]";
$history = (bool)"CMS_VALUE[15]";
$index = (int)"CMS_VALUE[16]";

// other vars
$contacts = new Cntnd\Contacts\CntndContactsInput($client);

?>
<div class="form-vertical">

    <div class="form-group w-50">
        <label for="source"><?= mi18n("SOURCE") ?></label>
        <select id="source" name="CMS_VAR[1]" size="1">
            <option><?= mi18n("CHOOSE") ?></option>
            <option value="csv" <?= ($source == "csv") ? "selected" : "" ?>><?= mi18n("CSV") ?></option>
            <option value="db" <?= ($source == "db") ? "selected" : "" ?>><?= mi18n("DB") ?></option>
        </select>
    </div>

    <fieldset <?= ($source == "csv") ? "" : "disabled" ?>>
        <legend><?= mi18n("CSV") ?></legend>
        <div class="form-group w-50">
            <label for="csv"><?= mi18n("CSV_FILE") ?></label>
            <select id="csv" name="CMS_VAR[10]" size="1">
                <option><?= mi18n("CHOOSE") ?></option>
                <?php
                $files = $contacts->files();
                foreach ($files as $file) {
                    $selected = ($csv == $file['file']) ? "selected" : "";
                    echo "<option value=\"" . $file['file'] . "\" $selected>" . $file['filename'] . "</option>";
                }
                ?>
            </select>
        </div>

        <fieldset <?= (empty($csv)) ? "disabled" : "" ?>>
            <legend><?= mi18n("CSV_CONFIG") ?></legend>

            <div class="d-flex justify-content-between">
                <div class="form-group w-32">
                    <label for="encoding"><?= mi18n("ENCODING") ?></label>
                    <select id="encoding" name="CMS_VAR[11]" size="1">
                        <option value="UTF-8" <?= ($encoding == "UTF-8") ? "selected" : "" ?>>
                            UTF-8
                        </option>
                        <option value="ISO-8859-1" <?= ($encoding == "ISO-8859-1") ? "selected" : "" ?>>
                            ISO-8859-1
                        </option>
                        <option value="Windows-1252" <?= ($encoding == "Windows-1252") ? "selected" : "" ?>>
                            Windows-1252
                        </option>
                    </select>
                </div>

                <div class="form-group w-32">
                    <label for="delimiter"><?= mi18n("DELIMITER") ?></label>
                    <input id="delimiter" name="CMS_VAR[12]" type="text" maxlength="1" value="<?= $delimiter ?>"/>
                </div>

                <div class="form-group w-32 align-self-flex-end" style="margin: 10px 0;">
                    <div class="form-check form-check-inline">
                        <input id="headers" class="form-check-input" name="CMS_VAR[13]"
                               type="checkbox" <?= ($headers) ? "checked" : "" ?>/>
                        <label for="headers" class="form-check-label"
                               title="<?= mi18n("HEADERS") ?>"><?= mi18n("HEADERS") ?></label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input id="cache" class="form-check-input" name="CMS_VAR[14]"
                               type="checkbox" <?= ($cache) ? "checked" : "" ?> />
                        <label for="cache" class="form-check-label"
                               title="<?= mi18n("CACHE_INFO") ?>"><?= mi18n("CACHE") ?></label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input id="history" class="form-check-input" name="CMS_VAR[15]"
                               type="checkbox" <?= ($history) ? "checked" : "" ?>/>
                        <label for="history" class="form-check-label"
                               title="<?= mi18n("HISTORY_INFO") ?>"><?= mi18n("HISTORY") ?></label>
                    </div>
                </div>
            </div>

            <div class="d-flex">
                <div class="form-group w-32">
                    <label for="index"><?= mi18n("INDEX") ?></label>
                    <select id="index" name="CMS_VAR[16]" size="1" <?= (!$headers) ? "disabled" : "" ?>>
                        <option><?= mi18n("CHOOSE") ?></option>
                        <?php
                        $headers = $contacts->headers($csv, $delimiter);
                        $i = 0;
                        foreach ($headers as $header) {
                            $selected = ($index == $i) ? "selected" : "";
                            echo "<option value=\"$i\" $selected>$header</option>";
                            $i++;
                        }
                        ?>
                    </select>
                </div>
            </div>

        </fieldset>
    </fieldset>
</div>
<?php
