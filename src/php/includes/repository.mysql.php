<?php


use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Cake\Database\Query;
use Contacts\Data\Contact;
use Contacts\Data\Data;
use Contacts\Data\Mapping;
use Contacts\Repository\Repository;
use Selective\ArrayReader\ArrayReader;

class MySQLRepository implements Repository
{
    private Connection $connection;
    const TABLE = "adressdatenbank_test";

    public function __construct($connection)
    {
        // Database settings
        $settings['db'] = [
            'driver' => Mysql::class,
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'quoteIdentifiers' => true,
            'timezone' => null,
            'cacheMetadata' => false,
            'log' => false,
            // PDO options
            'flags' => [
                PDO::ATTR_PERSISTENT => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => true,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_STRINGIFY_FETCHES => true
            ],
        ];

        $settings['db']['host'] = $connection['host'];
        $settings['db']['database'] = $connection['database'];
        $settings['db']['username'] = $connection['user'];
        $settings['db']['password'] = $connection['password'];
        $this->connection = new Connection($settings['db']);
    }

    /**
     * Create a new query.
     *
     * @return Query The query
     */
    private function newQuery(): Query
    {
        return $this->connection->newQuery();
    }

    /**
     * Create a new select query.
     *
     * @return Query The query
     */
    private function newSelect(): Query
    {
        return $this->connection->newQuery()->from(self::TABLE)->select($this->select_columns());
    }

    private function columns(): array
    {
        return [
            'id',
            'vorname',
            'name',
            'strasse',
            'ort',
            'plz',
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
            'tag:organisationen'
        ];
    }

    private function select_columns(): array
    {
        return array_values($this->mapping_columns());
    }

    private function mapping_columns(): array
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

    public function contacts(): array
    {
        $query = $this->newSelect();
        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function history(): array
    {
        return array();
    }

    public function has_history(): bool
    {
        return false;
    }

    public function index(): string
    {
        return "email";
    }

    public function upsert(Contact $contact): void
    {
        $data = $this->convert($contact);
        $exist = $this->exists($contact->email);
        if (!$exist[0]) {
            // insert
            $query = $this->newQuery()->insert($this->columns());
            $query->into(self::TABLE)
                ->values($data)
                ->execute();
        } else {
            // update
            $query = $this->newQuery()->update(self::TABLE);
            $query->set($data)
                ->andWhere([$this->index() => $contact->email])
                ->execute();
        }
    }

    public function update(Contact $contact, array $where): void
    {
        $data = $this->convert($contact);
        $where_mapped = Mapping::where_stmt($where, $this->mapping_columns());
        $query = $this->newQuery()->update(self::TABLE);
        $query->set($data)
            ->andWhere($where_mapped)
            ->execute();
    }


    public function delete($index): void
    {
        $query = $this->newQuery()->delete(self::TABLE);
        $query->andWhere([$this->index() => $index])
            ->execute();
    }

    public function delete_where(array $where): void
    {
        $where_mapped = Mapping::where_stmt($where, $this->mapping_columns());
        $query = $this->newQuery()->delete(self::TABLE);
        $query->andWhere($where_mapped)
            ->execute();
    }

    public function exists($index): array
    {
        $query = $this->newSelect()->andWhere([$this->index() => $index]);
        $exist = $query->execute()->fetch('assoc');
        if (!$exist) {
            return array();
        }
        return $exist;
    }

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

        $data['vorname'] = $reader->findString('vorname', Mapping::$default_string);
        $data['name'] = $reader->findString('name', Mapping::$default_string);
        $data['strasse'] = $reader->findString('strasse', Mapping::$default_string);
        $data['ort'] = $reader->findString('ort', Mapping::$default_string);
        $data['plz'] = $reader->findString('plz', Mapping::$default_string);
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
}