<?php


use Contacts\Data\Mapping;
use Contacts\Repository\Repository;
use CSVDB\CSVDB;
use CSVDB\Helpers\CSVConfig;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\InvalidArgument;

class CSVDBRepository extends Repository
{
    private CSVDB $csvdb;

    public function __construct(string $csv, CSVConfig $config)
    {
        $this->csvdb = new CSVDB($csv, $config);
    }

    public function contacts(): array
    {
        return $this->csvdb->select()->get();
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
    public function upsert(array $contact): void
    {
        $this->csvdb->upsert($this->to_record($contact));
    }

    /**
     * @throws InvalidArgument
     * @throws Exception
     */
    public function update(array $contact, array $where): void
    {
        $data = $this->to_record($contact);
        $where_mapped = Mapping::where_stmt($where, $this->mapping_columns());
        $this->csvdb->update($data, $where_mapped);
    }

    /**
     * @throws InvalidArgument
     * @throws Exception
     */
    public function delete($index): void
    {
        $this->csvdb->delete([$this->index() => $index]);
    }

    /**
     * @throws InvalidArgument
     * @throws Exception
     */
    public function delete_where(array $where): void
    {
        $where_mapped = Mapping::where_stmt($where, $this->mapping_columns());
        $this->csvdb->delete($where_mapped);
    }

    public function mapping_columns(): array
    {
        // todo
        return [
            'Nachname' => 'Nachname',
            'Vorname' => 'Vorname',
            'Strasse' => 'Strasse',
            'PLZ' => 'PLZ',
            'Ort' => 'Ort',
            'Telefon' => 'Telefon',
            'E-Mail' => 'E-Mail',
            'Geburtstag' => 'Geburtstag',
            'Newsletter' => 'Newsletter',
            'Infomail spontan unterwegs' => 'Infomail spontan unterwegs',
            'Freunde' => 'Freunde',
            'WL/BL' => 'WL/BL',
            'Dienstleister' => 'Dienstleister',
            'Diverse' => 'Diverse'
        ];
    }

    public function exists($index): array
    {
        $exist = $this->csvdb->select()->where([$this->index() => $index])->get();
        if (!$exist) {
            return array();
        }
        return $exist[0];
    }

    public function headers(): array
    {
        return $this->csvdb->headers();
    }

    public function data_types(): array
    {
        return $this->csvdb->getDatatypes();
    }

    public function dump(string $records): void
    {
        $this->csvdb->dump($records."\n");
    }
}