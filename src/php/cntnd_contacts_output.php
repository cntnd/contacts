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
    cInclude('module', 'includes/repository.csvdb.php');
    cInclude('module', 'includes/source.newsletter.php');
    cInclude('module', 'includes/source.mailchimp.php');

    cInclude('module', 'includes/class.cntnd_contacts_output.php');
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
    $mailchimp_folder = "CMS_VALUE[20]";

    // other vars
    $output = new \Cntnd\Contacts\CntndContactsOutput($idart);
    $mappings = $output->source_mappings();
    $required = $output->required();

    if ($source == "csv") {
        //csvdb
        $config = new CSVDB\Helpers\CSVConfig($index, $encoding, $delimiter, $headers, $cache, $history);
        $repository = new CSVDBRepository($csv, $config);
    } else {
        $repository = new cDBRepository();
    }
    $contacts = new Contacts\Spreadsheet($repository, $required);
    $contacts->update_data_types($output->data_types());

    // sources
    $newsletter = new ContenidoNewsletter();
    $contacts->add_source($newsletter);
    if (!empty($mailchimp_folder)) {
        $cfgClient = \cRegistry::getClientConfig();
        $path = $cfgClient[$client]["upl"]["path"];
        try {
            $mailchimp = new Mailchimp($path . $mailchimp_folder);
            $contacts->add_source($mailchimp);
        } catch (Exception $e) {
            echo '<div class="cntnd_alert cntnd_alert-danger">Es gibt ein Problem mit der Mailchimp Quelle: ' . $e . '</div>';
        }
    }

    // module
    echo "<pre>";
    if ($_POST) {
        if (array_key_exists('editor_form_action', $_POST)) {
            // Dashbord & Editor
            if ($_POST['editor_form_action'] == Contacts\Contacts::NEW || $_POST['editor_form_action'] == Contacts\Contacts::UPDATE) {
                $data = Contacts\Data\Mapping::stripslashes($_POST['data']);
                if (!empty($_POST['editor_form_source']) && !empty($_POST['editor_form_index'])) {
                    $contacts->upsert_source($data, $_POST['editor_form_source'], $_POST['editor_form_index'], $idart);
                } else {
                    $contacts->upsert($data);
                }
            } elseif ($_POST['editor_form_action'] == Contacts\Contacts::DELETE) {
                if (!empty($_POST['editor_form_source']) && !empty($_POST['editor_form_index'])) {
                    $contacts->delete_source($_POST['editor_form_source'], $_POST['editor_form_index'], $idart);
                } else if (!empty($_POST['editor_form_delete'])) {
                    $data = json_decode(base64_decode($_POST['editor_form_delete']), true);
                    $contacts->delete($data);
                }
            }
        } elseif (array_key_exists('addresses_form_action', $_POST)) {
            // Addresses
            if ($_POST['addresses_form_action'] == Contacts\Contacts::DUMP) {
                $records = base64_decode($_POST['addresses_form_data']);
                $contacts->dump($records);
            }
        } elseif (array_key_exists('mapping_form_action', $_POST)) {
            // Mappings
            if ($_POST['mapping_form_action'] == Contacts\Contacts::UPDATE) {
                $output->store($_POST['data']);

                $required = $output->required();
                $mappings = $output->source_mappings();
                $contacts->update_data_types($output->data_types());
            } elseif ($_POST['mapping_form_action'] == Contacts\Contacts::DELETE) {
                $output->delete();
                $required = array();
                $mappings = array();
            }
        }
    }
    echo "</pre>";
    // load sources
    $count_sources = $contacts->count_sources();
    // mappings
    $mapping_count = array_filter($contacts->data_types(), function ($value) {
        return !is_string($value);
    });
    ?>
    <ul class="tabs" id="contacts">
        <li class="tabs__tab  <?= ($count_sources > 0) ? "active" : "" ?>" data-toggle="tabs"
            data-target="contacts__content--dashboard">
            <span class="tabs__tab--link">Dashboard <?= ($count_sources > 0) ? "(" . $count_sources . ")" : "" ?></span>
        </li>
        <li class="tabs__tab  <?= ($count_sources == 0) ? "active" : "" ?>" data-toggle="tabs"
            data-target="contacts__content--contacts">
            <span class="tabs__tab--link">Adressen</span>
        </li>
        <li class="tabs__tab" data-toggle="tabs"
            data-target="contacts__content--mappings">
            <span class="tabs__tab--link">Mappings <?= (count($mapping_count) > 0) ? "(" . count($mapping_count) . ")" : "" ?></span>
        </li>
        <?php if ($contacts->has_history()) { ?>
            <li class="tabs__tab" data-toggle="tabs"
                data-target="contacts__content--history">
                <span class="tabs__tab--link">History</span>
            </li>
        <?php } ?>
    </ul>

    <div class="tabs__content" id="contacts__content">
        <div class="tabs__content--pane fade <?= ($count_sources > 0) ? "active" : "" ?>"
             id="contacts__content--dashboard">
            <h2>Dashboard <span class="header__action new_contact">neuer Eintrag</span></h2>

            <h3>Neue Einträge: <?= $count_sources ?></h3>
            <?php
            $sources = $contacts->load_sources();
            $has_entries = $count_sources > 0;
            if ($has_entries && !empty($mappings)) {
                echo '<div class="card card--list">';
                foreach ($sources as $source => $records) {
                    foreach ($records["data"] as $record) {
                        if ($record instanceof Contacts\Data\Data) {
                            $uuid = rand();
                            $exist = $contacts->exists($record->email);
                            $merge = $contacts->merge($record, $exist, $mappings[$source]);
                            $base64 = base64_encode(json_encode($merge));
                            ?>
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
                            <?php
                        }
                    }
                }
                echo '</div>';
            } else {
                echo '<div class="cntnd_alert cntnd_alert-primary">Keine neuen Einträge vorhanden.</div>';
            }
            ?>
            <h3>Mappings</h3>
            <?php
            if (count($mapping_count) > 0) {
                echo '<div class="cntnd_alert cntnd_alert-danger">Für folgende Felder gibt es offene Mappings:<ul>';
                foreach ($mapping_count as $mapping => $type) {
                    echo '<li>' . $mapping . '</li>';
                }
                echo '</ul></div>';
                // todo sources
            } else {
                echo '<div class="cntnd_alert cntnd_alert-primary">Keine offenen Mappings vorhanden.</div>';
            }
            ?>
        </div>

        <div class="tabs__content--pane fade <?= ($count_sources == 0) ? "active" : "" ?>"
             id="contacts__content--contacts">
            <script>
                const headers = <?= json_encode($contacts->headers()) ?>;
                const columns_handsontable = <?= json_encode($contacts->columns()) ?>;
                const data_handsontable = <?= $contacts->data() ?>;
            </script>
            <div class="spreadsheet__toolbar">
                <button class="material-symbols-outlined new_contact">note_add</button>
                <button class="material-symbols-outlined store_csv">save</button>
                <button class="material-symbols-outlined export_csv">download</button>
                <input id="search_field" type="search" placeholder="Suchen"/>
            </div>
            <div id="exampleParent">
                <div id="example"></div>
            </div>
        </div>

        <div class="tabs__content--pane fade" id="contacts__content--mappings">
            <h2>Mappings</h2>
            <form name="mapping_form" id="mapping_form" method="post">
                <div class="card card--list">
                    <?php
                    $data_types = $contacts->data_types();
                    foreach ($contacts->headers() as $header) {
                        $type = $data_types[$header];
                        ?>
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <strong class="w-25"><?= $header ?></strong>
                            <span class="w-auto">
                            <?= $contacts->make_select_assoc(
                                ["string" => "Text", "integer" => "Zahl", "date" => "Datum", "boolean" => "Checkbox", "float" => "Gleitkommazahl"],
                                "data[data_type][$header]",
                                "Typ",
                                $type) ?>
                            <?= $contacts->make_checkbox("data[required][$header]", "Muss-Feld", false, in_array($header, $required)) ?>
                        </span>
                            <?php
                            foreach ($contacts->sources_headers() as $key => $sources) {
                                $value = "";
                                if (array_key_exists($header, $mappings[$key])) {
                                    $value = $mappings[$key][$header];
                                }
                                echo '<span class="w-auto">' . $contacts->make_select($sources, "data[mappings][$key][$header]", $key, $value) . '</span>';
                            }
                            ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="action">
                    <input type="hidden" name="mapping_form_action" value="update"/>
                    <button class="btn btn-primary" type="submit">Speichern</button>
                    <button class="btn btn-light" type="reset">Zurücksetzen</button>
                    <button class="btn btn-dark right mapping_form_remove" type="button">Löschen</button>
                </div>
            </form>
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

    <div id="editor" class="overlay">
        <div class="popup">
            <h2>Editor</h2>
            <span class="close">&times;</span>
            <form name="editor_form" id="editor_form" method="post">
                <div class="content">
                    <div class="d-flex">
                        <?php
                        foreach ($contacts->data_types() as $header => $type) {
                            if ($type === "boolean") {
                                echo $contacts->make_checkbox("data[" . $header . "]", $header, in_array($header, $required));
                            } else {
                                $input_type = ($type === "integer" || $type === "float") ? "number" : "text";
                                echo $contacts->make_input("data[" . $header . "]", $header, $input_type, in_array($header, $required));
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="action">
                    <input type="hidden" name="editor_form_action" value="new"/>
                    <input type="hidden" name="editor_form_source"/>
                    <input type="hidden" name="editor_form_index"/>
                    <button class="btn btn-primary" type="submit">Speichern</button>
                    <button class="btn btn-light" type="reset">Zurücksetzen</button>
                    <button class="btn btn-dark right editor_form_remove" type="button">Löschen</button>
                </div>
            </form>
        </div>
    </div>
    <div id="delete" style="visibility: hidden;">
        <form name="delete_form" id="delete_form" method="post">
            <input type="hidden" name="editor_form_action" value="delete"/>
            <input type="hidden" name="editor_form_source"/>
            <input type="hidden" name="editor_form_index"/>
            <input type="hidden" name="editor_form_delete"/>
        </form>
    </div>
    <div id="update" style="visibility: hidden;">
        <form name="addresses_form" id="addresses_form" method="post">
            <input type="hidden" name="addresses_form_action" value="dump"/>
            <input type="hidden" name="addresses_form_data"/>
        </form>
    </div>
    <?php
} else {
    $tpl = cSmartyFrontend::getInstance();
    $tpl->display('public.tpl');
}
?>
