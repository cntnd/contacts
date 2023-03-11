<?php
// cntnd_contacts_output
use Contacts\Spreadsheet;

$cntnd_module = "cntnd_contacts";

// assert framework initialization
defined('CON_FRAMEWORK') || die('Illegal call: Missing framework initialization - request aborted.');

// editmode
$editmode = cRegistry::isBackendEditMode();

// includes
if ($editmode) {
    cInclude('module', 'vendor/autoload.php');
    cInclude('module', 'includes/repository.cdb.php');

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
    $contacts = new Spreadsheet($repository, $headers);

    $contacts->data();
    // module
    ?>
    <ul class="tabs" id="contacts">
        <li class="tabs__tab active" data-toggle="tabs" data-target="contacts__content--dashboard">
            <span class="tabs__tab--link">Dashboard</span>
        </li>
        <li class="tabs__tab" data-toggle="tabs" data-target="contacts__content--contacts">
            <span class="tabs__tab--link">Adressen</span>
        </li>
        <li class="tabs__tab <?= ($contacts->has_history()) ? "" : "disabled" ?>" data-toggle="tabs" data-target="contacts__content--history">
            <span class="tabs__tab--link">History</span>
        </li>
    </ul>

    <div class="tabs__content" id="contacts__content">
        <div class="tabs__content--pane fade active" id="contacts__content--dashboard">
            <h2>Dashboard <a href="#editor" class="header__action">neuer Eintrag</a></h2>
        </div>

        <div class="tabs__content--pane spreadsheet fade" id="contacts__content--contacts">
            <script>
                const data = <?= $contacts->data() ?>;
                const columns = <?= $contacts->columns() ?>;
            </script>
            <div id="spreadsheet"></div>
        </div>

        <div class="tabs__content--pane fade" id="contacts__content--history">
            <h2>History</h2>
        </div>
    </div>
    <?php

    $tpl = cSmartyFrontend::getInstance();
    $tpl->display('popup_editor.tpl');
} else {
    $tpl = cSmartyFrontend::getInstance();
    $tpl->display('public.tpl');
}
?>
