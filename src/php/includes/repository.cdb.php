<?php

use Contacts\Data\Contact;
use Contacts\Data\Data;
use Contacts\Data\Mapping;
use Contacts\Repository\Repository;
use Selective\ArrayReader\ArrayReader;

class cDBRepository implements Repository
{
    private $db;
    const TABLE = "adressdatenbank_test";

    public function __construct()
    {
        $this->db = new \cDb;
    }

    public function contacts(): array
    {
        $sql = "SELECT * FROM :table ORDER BY id";
        $values = array(
            'table' => self::TABLE
        );
        $this->db->query($sql, $values);
        $data = [];
        while ($this->db->next_record()) {
            $data[] = $this->to_data($this->db->toArray());
        }
        return $data;
    }

    public function history(): array
    {
        // TODO: Implement history() method.
        return array();
    }

    public function has_history(): bool
    {
        // TODO: Implement has_history() method.
        return false;
    }

    public function index(): string
    {
        return "email";
    }

    public function upsert(Contact $contact): void
    {
        $exist = $this->exists($contact->email);
        $values = $this->convert($contact);
        if (count($exist) == 0) {
            // insert
            $values['id'] = "NULL";
            $this->db->insert(self::TABLE, $values);
        } else {
            // update
            $this->db->update(self::TABLE, $values, [$this->index() => $contact->email]);
        }
    }

    public function delete($index): void
    {
        $sql = "DELETE FROM :table WHERE " . $this->index() . "=:index ";
        $values = array(
            'table' => self::TABLE,
            'index' => \cSecurity::toString($index)
        );
        $this->db->query($sql, $values);
    }

    public function exists($index): array
    {
        $sql = "SELECT * FROM :table WHERE email=':index' ";
        $values = array(
            'table' => self::TABLE,
            'index' => \cSecurity::toString($index)
        );
        $this->db->query($sql, $values);
        return (array)$this->db->getResultObject();
    }

    public function to_data(array $record): array
    {
        $reader = new ArrayReader($record);

        $data['vorname'] = $reader->findString('vorname', Mapping::$default_string);
        $data['name'] = $reader->findString('name', Mapping::$default_string);
        $data['strasse'] = $reader->findString('strasse', Mapping::$default_string);
        $data['plz'] = $reader->findString('plz', Mapping::$default_string);
        $data['ort'] = $reader->findString('ort', Mapping::$default_string);
        $data['land'] = $reader->findString('land', Mapping::$default_string);
        $data['telefon_geschaeftlich'] = $reader->findString('telefon_geschaeftlich', Mapping::$default_string);
        $data['telefon'] = $reader->findString('telefon', Mapping::$default_string);
        $data['mobile'] = $reader->findString('mobile', Mapping::$default_string);
        $data['email'] = $reader->findString('email', Mapping::$default_string);
        $data['email_2'] = $reader->findString('email_2', Mapping::$default_string);

        $data['infomail_spontan'] = self::string_to_bool($reader->findString('check:infomail_spontan', "0"));
        $data['newsletter'] = self::string_to_bool($reader->findString('check:newsletter', "0"));

        $data['familie'] = self::string_to_bool($reader->findString('tag:familie', "0"));
        $data['freunde'] = self::string_to_bool($reader->findString('tag:freunde', "0"));
        $data['kollegen'] = self::string_to_bool($reader->findString('tag:kollegen', "0"));
        $data['nachbarn'] = self::string_to_bool($reader->findString('tag:nachbarn', "0"));
        $data['wanderleiter'] = self::string_to_bool($reader->findString('tag:wanderleiter', "0"));
        $data['bergsportunternehmen'] = self::string_to_bool($reader->findString('tag:bergsportunternehmen', "0"));
        $data['geschaeftskollegen'] = self::string_to_bool($reader->findString('tag:geschaeftskollegen', "0"));
        $data['dienstleister'] = self::string_to_bool($reader->findString('tag:dienstleister', "0"));
        $data['linkedin'] = self::string_to_bool($reader->findString('tag:linkedin', "0"));
        $data['unternehmen'] = self::string_to_bool($reader->findString('tag:unternehmen', "0"));
        $data['organisationen'] = self::string_to_bool($reader->findString('tag:organisationen', "0"));
        return $data;
    }

    public function convert(Data $record): array
    {
        $data = array();
        $reader = new ArrayReader($record->record());

        $data['vorname'] = $reader->findString('vorname', Mapping::$default_string);
        $data['name'] = $reader->findString('name', Mapping::$default_string);
        $data['strasse'] = $reader->findString('strasse', Mapping::$default_string);
        $data['plz'] = $reader->findString('plz', Mapping::$default_string);
        $data['ort'] = $reader->findString('ort', Mapping::$default_string);
        $data['land'] = $reader->findString('land', Mapping::$default_string);
        $data['telefon'] = $reader->findString('telefon', Mapping::$default_string);
        $data['telefon_geschaeftlich'] = $reader->findString('telefon_geschaeftlich', Mapping::$default_string);
        $data['mobile'] = $reader->findString('mobile', Mapping::$default_string);
        $data['email'] = $reader->findString('email');
        $data['email_2'] = $reader->findString('email_2', Mapping::$default_string);

        $data['check:infomail_spontan'] = Mapping::x_to_bool($reader->findString('infomail_spontan', Mapping::$default_string));
        $data['check:newsletter'] = Mapping::x_to_bool($reader->findString('newsletter', Mapping::$default_string));

        $data['tag:familie'] = Mapping::x_to_bool($reader->findString('familie', Mapping::$default_string));
        $data['tag:freunde'] = Mapping::x_to_bool($reader->findString('freunde', Mapping::$default_string));
        $data['tag:kollegen'] = Mapping::x_to_bool($reader->findString('kollegen', Mapping::$default_string));
        $data['tag:nachbarn'] = Mapping::x_to_bool($reader->findString('nachbarn', Mapping::$default_string));
        $data['tag:wanderleiter'] = Mapping::x_to_bool($reader->findString('wanderleiter', Mapping::$default_string));
        $data['tag:bergsportunternehmen'] = Mapping::x_to_bool($reader->findString('bergsportunternehmen', Mapping::$default_string));
        $data['tag:geschaeftskollegen'] = Mapping::x_to_bool($reader->findString('geschaeftskollegen', Mapping::$default_string));
        $data['tag:dienstleister'] = Mapping::x_to_bool($reader->findString('dienstleister', Mapping::$default_string));
        $data['tag:linkedin'] = Mapping::x_to_bool($reader->findString('linkedin', Mapping::$default_string));
        $data['tag:unternehmen'] = Mapping::x_to_bool($reader->findString('unternehmen', Mapping::$default_string));
        $data['tag:organisationen'] = Mapping::x_to_bool($reader->findString('organisationen', Mapping::$default_string));
        return $data;
    }


    public static function string_to_bool(string $val): bool
    {
        return $val == "1";
    }
}