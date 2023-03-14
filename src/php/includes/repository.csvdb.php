<?php


use Contacts\Data\Contact;
use Contacts\Data\Data;
use Contacts\Data\Mapping;
use Contacts\Repository\Repository;
use CSVDB\CSVDB;
use CSVDB\Helpers\CSVConfig;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use Selective\ArrayReader\ArrayReader;

cInclude('module', 'includes/TagConverter.php');

class CSVDBRepository implements Repository
{
    private CSVDB $csvdb;

    public function __construct(string $csv, CSVConfig $config)
    {
        $this->csvdb = new CSVDB($csv, $config);
    }

    public function contacts(): array
    {
        return $this->csvdb->select()->get(new TagConverter());
    }

    public function history(): array
    {
        $dir = $this->csvdb->history_dir();
        $files = scandir($dir, SCANDIR_SORT_DESCENDING);
        $result = array();
        foreach ($files as $file) {
            $result[$file] = $dir . $file;
        }
        return $result;
    }

    public function has_history(): bool
    {
        return $this->csvdb->config->history;
    }

    public function index(): string
    {
        return $this->csvdb->index;
    }

    // CRUD

    /**
     * @throws InvalidArgument
     * @throws CannotInsertRecord
     * @throws Exception
     */
    public function upsert(Contact $contact): void
    {
        $this->csvdb->upsert($this->convert($contact));
    }

    /**
     * @throws InvalidArgument
     * @throws Exception
     */
    public function delete($index): void
    {
        $this->csvdb->delete([$this->index() => $index]);
    }

    public function exists($index): array
    {
        $exist = $this->csvdb->select()->where([$this->index() => $index])->get();
        return $exist[0];
    }

    public function to_data(array $record): array
    {
        $reader = new ArrayReader($record);

        $data['vorname'] = $reader->findString('Vorname', Mapping::$default_string);
        $data['name'] = $reader->findString('Nachname', Mapping::$default_string);
        $data['strasse'] = $reader->findString('Strasse', Mapping::$default_string);
        $data['plz'] = $reader->findString('PLZ', Mapping::$default_string);
        $data['ort'] = $reader->findString('Ort', Mapping::$default_string);
        $data['land'] = $reader->findString('Land', Mapping::$default_string);
        $data['telefon_geschaeftlich'] = $reader->findString('Telefon geschäftlich', Mapping::$default_string);
        $data['telefon'] = $reader->findString('Telefon', Mapping::$default_string);
        $data['mobile'] = $reader->findString('Mobiltelefon', Mapping::$default_string);
        $data['email'] = $reader->findString('E-Mail');
        $data['email_2'] = $reader->findString('E-Mail 2', Mapping::$default_string);

        $data['infomail_spontan'] = Mapping::x_to_bool($reader->findString('Check:Infomail Spontan', Mapping::$default_string));
        $data['newsletter'] = Mapping::x_to_bool($reader->findString('Check:Newsletter', Mapping::$default_string));

        $data['familie'] = Mapping::x_to_bool($reader->findString('Tag:Familie', Mapping::$default_string));
        $data['freunde'] = Mapping::x_to_bool($reader->findString('Tag:Freunde', Mapping::$default_string));
        $data['kollegen'] = Mapping::x_to_bool($reader->findString('Tag:Kollegen', Mapping::$default_string));
        $data['nachbarn'] = Mapping::x_to_bool($reader->findString('Tag:Nachbarn', Mapping::$default_string));
        $data['wanderleiter'] = Mapping::x_to_bool($reader->findString('Tag:Wanderleiter', Mapping::$default_string));
        $data['bergsportunternehmen'] = Mapping::x_to_bool($reader->findString('Tag:Bergsportunternehmen', Mapping::$default_string));
        $data['geschaeftskollegen'] = Mapping::x_to_bool($reader->findString('Tag:Geschäftskollegen', Mapping::$default_string));
        $data['dienstleister'] = Mapping::x_to_bool($reader->findString('Tag:Dienstleister', Mapping::$default_string));
        $data['linkedin'] = Mapping::x_to_bool($reader->findString('Tag:linkedin', Mapping::$default_string));
        $data['unternehmen'] = Mapping::x_to_bool($reader->findString('Tag:Unternehmen', Mapping::$default_string));
        $data['organisationen'] = Mapping::x_to_bool($reader->findString('Tag:Organisationen', Mapping::$default_string));
        return $data;
    }

    public function convert(Data $record): array
    {
        $data = array();
        $reader = new ArrayReader($record->record());

        $data['Vorname'] = $reader->findString('vorname', Mapping::$default_string);
        $data['Nachname'] = $reader->findString('name', Mapping::$default_string);
        $data['Strasse'] = $reader->findString('strasse', Mapping::$default_string);
        $data['PLZ'] = $reader->findString('plz', Mapping::$default_string);
        $data['Ort'] = $reader->findString('ort', Mapping::$default_string);
        $data['Land'] = $reader->findString('land', Mapping::$default_string);
        $data['Telefon geschäftlich'] = $reader->findString('telefon_geschaeftlich', Mapping::$default_string);
        $data['Telefon'] = $reader->findString('telefon', Mapping::$default_string);
        $data['Mobiltelefon'] = $reader->findString('mobile', Mapping::$default_string);
        $data['E-Mail'] = $reader->findString('email');
        $data['E-Mail 2'] = $reader->findString('email_2', Mapping::$default_string);

        $data['Check:Infomail Spontan'] = Mapping::bool_to_x($reader->findBool('infomail_spontan', Mapping::$default_bool));
        $data['Check:Newsletter'] = Mapping::bool_to_x($reader->findBool('newsletter', Mapping::$default_bool));

        $data['Tag:Familie'] = Mapping::bool_to_x($reader->findBool('familie', Mapping::$default_bool));
        $data['Tag:Freunde'] = Mapping::bool_to_x($reader->findBool('freunde', Mapping::$default_bool));
        $data['Tag:Kollegen'] = Mapping::bool_to_x($reader->findBool('kollegen', Mapping::$default_bool));
        $data['Tag:Nachbarn'] = Mapping::bool_to_x($reader->findBool('nachbarn', Mapping::$default_bool));
        $data['Tag:Wanderleiter'] = Mapping::bool_to_x($reader->findBool('wanderleiter', Mapping::$default_bool));
        $data['Tag:Bergsportunternehmen'] = Mapping::bool_to_x($reader->findBool('bergsportunternehmen', Mapping::$default_bool));
        $data['Tag:Geschäftskollegen'] = Mapping::bool_to_x($reader->findBool('geschaeftskollegen', Mapping::$default_bool));
        $data['Tag:Dienstleister'] = Mapping::bool_to_x($reader->findBool('dienstleister', Mapping::$default_bool));
        $data['Tag:linkedin'] = Mapping::bool_to_x($reader->findBool('linkedin', Mapping::$default_bool));
        $data['Tag:Unternehmen'] = Mapping::bool_to_x($reader->findBool('unternehmen', Mapping::$default_bool));
        $data['Tag:Organisationen'] = Mapping::bool_to_x($reader->findBool('organisationen', Mapping::$default_bool));
        return $data;
    }
}