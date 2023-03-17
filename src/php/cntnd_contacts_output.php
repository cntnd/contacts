<?php
// cntnd_contacts_output

$cntnd_module = "cntnd_contacts";

// assert framework initialization
defined('CON_FRAMEWORK') || die('Illegal call: Missing framework initialization - request aborted.');

// editmode
$editmode = cRegistry::isBackendEditMode();

// includes
if ($editmode) {
    cInclude('module', 'vendor/autoload.php');
    cInclude('module', 'includes/repository.cdb.php');
    cInclude('module', 'includes/repository.mysql.php');
    cInclude('module', 'includes/source.newsletter.php');
    cInclude('module', 'includes/source.infomail.php');

    cInclude('module', 'includes/script.cntnd_contacts.php');
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
    //csvdb
    $repository = new cDBRepository();
    $headers = [
        'Vorname',
        'Nachname',
        'Strasse',
        'Ort',
        'PLZ',
        'Land',
        'Telefon geschäftlich',
        'Telefon',
        'Mobiltelefon',
        'E-Mail',
        'E-Mail 2',
        'Check:Infomail Spontan',
        'Check:Newsletter',
        'Tag:Familie',
        'Tag:Freunde',
        'Tag:Kollegen',
        'Tag:Nachbarn',
        'Tag:Wanderleiter',
        'Tag:Bergsportunternehmen',
        'Tag:Geschäftskollegen',
        'Tag:Dienstleister ',
        'Tag:linkedin',
        'Tag:Unternehmen',
        'Tag:Organisationen'
    ];
    $contacts = new Contacts\Spreadsheet($repository, $headers);
    // sources
    $newsletter = new ContenidoNewsletter();
    $infomail = new ContenidoInfomail();
    $contacts->add_source($newsletter, $infomail);
    $count_sources = $contacts->count_sources();

    // module
    //echo "<pre>";
    if ($_POST) {
        if (array_key_exists('editor_form_action', $_POST)) {
            // Dashbord & Editor
            if ($_POST['editor_form_action'] == Contacts\Contacts::NEW || $_POST['editor_form_action'] == Contacts\Contacts::UPDATE) {
                $data = new Contacts\Data\Contact($_POST, true);
                if (!empty($_POST['editor_form_source']) && !empty($_POST['editor_form_index'])) {
                    $contacts->upsert_source($data, $_POST['editor_form_source'], $_POST['editor_form_index']);
                } else {
                    $contacts->upsert($data);
                }
            } elseif ($_POST['editor_form_action'] == Contacts\Contacts::DELETE) {
                if (!empty($_POST['editor_form_source']) && !empty($_POST['editor_form_index'])) {
                    $contacts->delete_source($_POST['editor_form_source'], $_POST['editor_form_index']);
                } else if (!empty($_POST['editor_form_delete'])) {
                    $data = json_decode(base64_decode($_POST['editor_form_delete']), true);
                    $contacts->delete($data);
                }
            }
        } elseif (array_key_exists('addresses_form_action', $_POST)) {
            // Addresses
            if ($_POST['addresses_form_action'] == Contacts\Contacts::UPDATE) {
                $records = json_decode(base64_decode($_POST['addresses_form_data']), true);
                $contacts->update($records);
            }
        }
    }
    //echo "</pre>";
    ?>
    <ul class="tabs" id="contacts">
        <li class="tabs__tab  <?= ($count_sources > 0) ? "active" : "" ?>" data-toggle="tabs"
            data-target="contacts__content--dashboard">
            <span class="tabs__tab--link">Dashboard</span>
        </li>
        <li class="tabs__tab  <?= ($count_sources == 0) ? "active" : "" ?>" data-toggle="tabs"
            data-target="contacts__content--contacts">
            <span class="tabs__tab--link">Adressen</span>
        </li>
        <li class="tabs__tab <?= ($contacts->has_history()) ? "" : "disabled" ?>" data-toggle="tabs"
            data-target="contacts__content--history">
            <span class="tabs__tab--link">History</span>
        </li>
    </ul>

    <div class="tabs__content" id="contacts__content">
        <div class="tabs__content--pane fade <?= ($count_sources > 0) ? "active" : "" ?>"
             id="contacts__content--dashboard">
            <h2>Dashboard <span class="header__action new_contact">neuer Eintrag</span></h2>

            <h3>Neue Einträge: <?= $count_sources ?></h3>
            <?php
            $sources = $contacts->load_sources();
            $has_entries = false;
            foreach ($sources as $source => $records) {
                foreach ($records["data"] as $record) {
                    $has_entries = true;
                    if ($record instanceof Contacts\Data\Data) {
                        $uuid = rand();
                        $exist = $contacts->exists($record->email);
                        $merge = $contacts->merge($record, $exist);
                        $base64 = base64_encode(json_encode($merge));
                        ?>
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <strong class="w-25"><?= $record->identifier() ?></strong>
                                <span class="w-25"><?= $record->email ?></span>
                                <span class="w-auto">Quelle <?= $records["name"] ?></span>
                                <span class="w-auto"><?= date('d.m.Y H:i:s', strtotime($record->timestamp())) ?></span>
                                <span class="w-auto">
                                <!-- data -->
                                <input type="hidden" id="contact_<?= $uuid ?>" name="contact_<?= $uuid ?>"
                                       value="<?= $base64 ?>"/>
                                <span class="material-symbols-outlined add_contact"
                                      data-contact="contact_<?= $uuid ?>"
                                      data-action="<?= (count($exist) > 0) ? "update" : "new" ?>"
                                      data-source="<?= $source ?>"
                                      data-index="<?= $record->index() ?>">archive</span>
                                <span class="material-symbols-outlined remove_contact"
                                      data-source="<?= $source ?>"
                                      data-index="<?= $record->index() ?>">delete</span>
                            </span>
                            </div>
                        </div>
                        <?php
                    }
                }
            }

            if (!$has_entries) {
                echo '<div class="cntnd_alert cntnd_alert-primary">Keine neuen Einträge vorhanden.</div>';
            }
            ?>
        </div>

        <div class="tabs__content--pane spreadsheet fade <?= ($count_sources == 0) ? "active" : "" ?>"
             id="contacts__content--contacts">
            <script>
                const data = <?= $contacts->data() ?>;
                const columns = <?= $contacts->columns() ?>;
            </script>
            <div id="spreadsheet"></div>
        </div>

        <div class="tabs__content--pane fade" id="contacts__content--history">
            <h2>History</h2>
            <table class="table">
                <thead>
                <tr>
                    <th>Datei</th>
                    <th>Datum</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $files = $contacts->history();
                foreach ($files as $filename => $file) {
                    if (is_file($file)) {
                        $download_file = str_replace(__DIR__, "", $file);
                        echo "<tr>";
                        echo '<td><a href="' . $download_file . '" target="_blank">' . $filename . '</a></td>';
                        echo '<td>' . date("d.m.Y H:i:s", filectime($file)) . '</td>';
                        echo "</tr>\n";
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php

    $tpl = cSmartyFrontend::getInstance();
    $tpl->display('popup_editor.tpl');
    $tpl->assign('action', 'action');
} else {
    $tpl = cSmartyFrontend::getInstance();
    $tpl->display('public.tpl');
}
?>
