<?php

use Contacts\Data\Mapping;
use Contacts\Repository\Repository;

class cDBRepository extends Repository
{
    private $db;
    const TABLE = "adressdatenbank_test";

    public function __construct()
    {
        $this->db = new \cDb;
    }

    /*
     * Reihenfolge der Felder muss mit der Reihenfolge des Headers übereinstimmen! Evtl. noch Mapping einführen!
     */
    public function contacts(): array
    {
        $sql = "SELECT * FROM :table ORDER BY id";
        $values = array(
            'table' => self::TABLE
        );
        $this->db->query($sql, $values);
        $data = [];
        while ($this->db->next_record()) {
            $data[] = $this->to_record($this->db->toArray(), $this->mapping_columns());
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

    public function upsert(array $contact): void
    {
        $exist = $this->exists($contact[$this->index()]);
        $values = $this->to_record($contact);
        if (count($exist) == 0) {
            // insert
            $values['id'] = "NULL";
            $this->db->insert(self::TABLE, $values);
        } else {
            // update
            $this->db->update(self::TABLE, $values, [$this->index() => $contact[$this->index()]]);
        }
    }

    public function update(array $contact, array $where): void
    {
        $this->db->update(self::TABLE, $this->to_record($contact), $this->update_where_stmt($where));
    }

    private function update_where_stmt(array $where): array
    {
        $mapped = Mapping::where_stmt($where, $this->mapping_columns());
        $where_mapped = array();
        foreach ($mapped as $clause) {
            $key = key($clause);
            $where_mapped[$key] = $clause[$key];
        }
        return $where_mapped;
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

    public function delete_where(array $where): void
    {
        $where_mapped = Mapping::where_stmt($where, $this->mapping_columns());
        $sql = "DELETE FROM :table WHERE ";
        $values['table'] = self::TABLE;
        foreach ($where_mapped as $clause) {
            $key = key($clause);
            $sql .= $key . "=':" . $key . "' AND ";
            $values[$key] = $clause[$key];
        }
        $sql = substr($sql, 0, -4);

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

    public function mapping_columns(): array
    {
        return [
            'vorname' => 'vorname',
            'name' => 'name',
            'strasse' => 'strasse',
            'ort' => 'ort',
            'plz' => 'plz',
            'land' => 'land',
            'telefon_geschaeftlich' => 'telefon_geschaeftlich',
            'telefon' => 'telefon',
            'mobile' => 'mobile',
            'email' => 'email',
            'email_2' => 'email_2',
            'infomail_spontan' => 'check:infomail_spontan',
            'newsletter' => 'check:newsletter',
            'familie' => 'tag:familie',
            'freunde' => 'tag:freunde',
            'kollegen' => 'tag:kollegen',
            'nachbarn' => 'tag:nachbarn',
            'wanderleiter' => 'tag:wanderleiter',
            'bergsportunternehmen' => 'tag:bergsportunternehmen',
            'geschaeftskollegen' => 'tag:geschaeftskollegen',
            'dienstleister' => 'tag:dienstleister',
            'linkedin' => 'tag:linkedin',
            'unternehmen' => 'tag:unternehmen',
            'organisationen' => 'tag:organisationen'
        ];
    }

    /*
    public function to_data(array $record): array
    {
        $reader = new ArrayReader($record);

        $data['vorname'] = $reader->findString('vorname', Mapping::$default_string);
        $data['name'] = $reader->findString('name', Mapping::$default_string);
        $data['strasse'] = $reader->findString('strasse', Mapping::$default_string);
        $data['ort'] = $reader->findString('ort', Mapping::$default_string);
        $data['plz'] = $reader->findString('plz', Mapping::$default_string);
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
        $data['ort'] = $reader->findString('ort', Mapping::$default_string);
        $data['plz'] = $reader->findString('plz', Mapping::$default_string);
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
    */

    public static function string_to_bool(string $val): bool
    {
        return $val == "1";
    }

    public function headers(): array
    {
        // TODO: Implement headers() method.
        return array();
    }

    public function data_types(): array
    {
        // TODO: Implement data_types() method.
        return array();
    }

    public function dump(string $records): void
    {
        // TODO: Implement dump() method.
    }
}