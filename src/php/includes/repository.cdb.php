<?php

use Contacts\Data\Contact;
use Contacts\Data\Data;
use Contacts\Data\Mapping;
use Contacts\Repository\Repository;
use Selective\ArrayReader\ArrayReader;

//cInclude('module', 'vendor/autoload.php');

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
        $sql = "SELECT * FROM :table ";
        $values = array(
            'table' => self::TABLE
        );
        $this->db->query($sql, $values);
        $data = [];
        while ($this->db->next_record()) {
            $data[] = $this->to_data((array) $this->db->getResultObject());
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
        $values = $contact->record();
        $values['table'] = self::TABLE;
        if (!$exist[0]) {
            // insert
            $values['id'] = "";
            $sql = "INSERT INTO :table (" . $this->columns() . ") VALUES (" . $this->values() . ")";
        } else {
            // update
            $sql = "UPDATE :table SET " . $this->update_columns() . " WHERE " . $this->index() . "=:index ";
        }
        $this->db->query($sql, $values);
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
        $sql = "SELECT * FROM :table WHERE " . $this->index() . "=:index ";
        $values = array(
            'table' => self::TABLE,
            'index' => \cSecurity::toString($index)
        );
        $this->db->query($sql, $values);
        return $this->db->getResultObject();
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
        $data['email'] = $reader->findString('email');
        $data['email_2'] = $reader->findString('email_2', Mapping::$default_string);

        $data['infomail_spontan'] = $reader->findBool('check:infomail_spontan', Mapping::$default_bool);
        $data['newsletter'] = $reader->findBool('check:newsletter', Mapping::$default_bool);

        $data['familie'] = $reader->findBool('tag:familie', Mapping::$default_bool);
        $data['freunde'] = $reader->findBool('tag:freunde', Mapping::$default_bool);
        $data['kollegen'] = $reader->findBool('tag:kollegen', Mapping::$default_bool);
        $data['nachbarn'] = $reader->findBool('tag:nachbarn', Mapping::$default_bool);
        $data['wanderleiter'] = $reader->findBool('tag:wanderleiter', Mapping::$default_bool);
        $data['bergsportunternehmen'] = $reader->findBool('tag:bergsportunternehmen', Mapping::$default_bool);
        $data['geschaeftskollegen'] = $reader->findBool('tag:geschaeftskollegen', Mapping::$default_bool);
        $data['dienstleister'] = $reader->findBool('tag:dienstleister', Mapping::$default_bool);
        $data['linkedin'] = $reader->findBool('tag:linkedin', Mapping::$default_bool);
        $data['unternehmen'] = $reader->findBool('tag:unternehmen', Mapping::$default_bool);
        $data['organisationen'] = $reader->findBool('tag:organisationen', Mapping::$default_bool);
        return $data;
    }

    public function convert(Data $record): array
    {
        $data = array();
        $reader = new ArrayReader($record->record());

        $data['id'] = $reader->findInt('id');
        $data['vorname'] = $reader->findString('vorname', Mapping::$default_string);
        $data['name'] = $reader->findString('name', Mapping::$default_string);
        $data['strasse'] = $reader->findString('strasse', Mapping::$default_string);
        $data['plz'] = $reader->findString('plz', Mapping::$default_string);
        $data['ort'] = $reader->findString('ort', Mapping::$default_string);
        $data['land'] = $reader->findString('land', Mapping::$default_string);
        $data['telefon_geschaeftlich'] = $reader->findString('telefon_geschaeftlich', Mapping::$default_string);
        $data['telefon'] = $reader->findString('telefon', Mapping::$default_string);
        $data['mobile'] = $reader->findString('mobile', Mapping::$default_string);
        $data['email'] = $reader->findString('email');
        $data['email_2'] = $reader->findString('email_2', Mapping::$default_string);

        $data['check:infomail_spontan'] = $reader->findBool('infomail_spontan', Mapping::$default_bool);
        $data['check:newsletter'] = $reader->findBool('newsletter', Mapping::$default_bool);

        $data['tag:familie'] = $reader->findBool('familie', Mapping::$default_bool);
        $data['tag:freunde'] = $reader->findBool('freunde', Mapping::$default_bool);
        $data['tag:kollegen'] = $reader->findBool('kollegen', Mapping::$default_bool);
        $data['tag:nachbarn'] = $reader->findBool('nachbarn', Mapping::$default_bool);
        $data['tag:wanderleiter'] = $reader->findBool('wanderleiter', Mapping::$default_bool);
        $data['tag:bergsportunternehmen'] = $reader->findBool('bergsportunternehmen', Mapping::$default_bool);
        $data['tag:geschaeftskollegen'] = $reader->findBool('geschaeftskollegen', Mapping::$default_bool);
        $data['tag:dienstleister'] = $reader->findBool('dienstleister', Mapping::$default_bool);
        $data['tag:linkedin'] = $reader->findBool('linkedin', Mapping::$default_bool);
        $data['tag:unternehmen'] = $reader->findBool('unternehmen', Mapping::$default_bool);
        $data['tag:organisationen'] = $reader->findBool('organisationen', Mapping::$default_bool);
        return $data;
    }

    private function columns(): string
    {
        return "'id',
            'vorname',
            'name',
            'strasse',
            'plz',
            'ort',
            'land',
            'telefon_geschaeftlich',
            'telefon',
            'mobile',
            'email',
            'email_2',
            'check:infomail_spontan',
            'check:newsletter',
            'tag:familie',
            'tag:freunde',
            'tag:kollegen',
            'tag:nachbarn',
            'tag:wanderleiter',
            'tag:bergsportunternehmen',
            'tag:geschaeftskollegen',
            'tag:dienstleister',
            'tag:linkedin',
            'tag:unternehmen',
            'tag:organisationen'";
    }

    private function select_columns(): string
    {
        return "'vorname',
            'name',
            'strasse',
            'plz',
            'ort',
            'land',
            'telefon_geschaeftlich',
            'telefon',
            'mobile',
            'email',
            'email_2',
            'check:infomail_spontan',
            'check:newsletter',
            'tag:familie',
            'tag:freunde',
            'tag:kollegen',
            'tag:nachbarn',
            'tag:wanderleiter',
            'tag:bergsportunternehmen',
            'tag:geschaeftskollegen',
            'tag:dienstleister',
            'tag:linkedin',
            'tag:unternehmen',
            'tag:organisationen'";
    }

    private function values(): string
    {
        return ":id,
            :vorname,
            :name,
            :strasse,
            :plz,
            :ort,
            :land,
            :telefon_geschaeftlich,
            :telefon,
            :mobile,
            :email,
            :email_2,
            :infomail_spontan,
            :newsletter,
            :familie,
            :freunde,
            :kollegen,
            :nachbarn,
            :wanderleiter,
            :bergsportunternehmen,
            :geschaeftskollegen,
            :dienstleister,
            :linkedin,
            :unternehmen,
            :organisationen";
    }

    private function update_columns(): string
    {
        return "'vorname'=:vorname,
            'name'=:name,
            'strasse'=:strasse,
            'plz'=:plz,
            'ort'=:ort,
            'land'=:land,
            'telefon_geschaeftlich'=:telefon_geschaeftlich,
            'telefon'=:telefon,
            'mobile'=:mobile,
            'email'=:email,
            'email_2'=:email_2,
            'check:infomail_spontan'=:infomail_spontan,
            'check:newsletter'=:newsletter,
            'tag:familie'=:familie,
            'tag:freunde'=:freunde,
            'tag:kollegen'=:kollegen,
            'tag:nachbarn'=:nachbarn,
            'tag:wanderleiter'=:wanderleiter,
            'tag:bergsportunternehmen'=:bergsportunternehmen,
            'tag:geschaeftskollegen'=:geschaeftskollegen,
            'tag:dienstleister'=:dienstleister,
            'tag:linkedin'=:linkedin,
            'tag:unternehmen'=:unternehmen,
            'tag:organisationen'=:organisationen";
    }
}