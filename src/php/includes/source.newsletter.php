<?php

use Cntnd\Contacts\CntndUtil;
use Contacts\Source\Source;

require_once("class.cntnd_util.php");
cInclude('module', 'includes/data.newsletter.php');

class ContenidoNewsletter extends CntndUtil implements Source
{
    private $db;
    const TABLE = "newsletter";
    const ARCHIVE_TABLE = "cntnd_contacts_archive";

    public function __construct()
    {
        $this->db = new \cDb;
    }

    public function load(): array
    {
        $sql = "SELECT * FROM :table ORDER BY pifa_timestamp ASC";
        $values = array(
            'table' => self::TABLE
        );
        $this->db->query($sql, $values);
        $data = [];
        while ($this->db->next_record()) {
            $data[] = Newsletter::of($this->db->toArray());
        }
        return $data;
    }

    public function last_load(): ?string
    {
        return null;
    }

    public function name(): string
    {
        return "Contenido: Newsletter";
    }

    public function archive($index, $key = null): void
    {
        if (!empty($key)) {
            $this->archive_stmt($index, $key);
        }
        $sql = "DELETE FROM :table WHERE id=:index ";
        $values = array(
            'table' => self::TABLE,
            'index' => \cSecurity::toString($index)
        );
        $this->db->query($sql, $values);
    }

    private function archive_stmt($index, $key): void
    {
        $sql = "SELECT * FROM :table WHERE id = :id";
        $values = array(
            'table' => self::TABLE,
            'id' => $index
        );
        $this->db->query($sql, $values);
        while ($this->db->next_record()) {
            $data = $this->db->toArray();
        }

        if (!empty($data)) {
            $sql = "INSERT INTO :table (idart, source, serializeddata) VALUES (:idart,':source',':data')";
            $values = array(
                'table' => self::ARCHIVE_TABLE,
                'idart' => $key,
                'source' => get_class($this),
                'data' => self::escapeData(json_encode($data))
            );
            $this->db->query($sql, $values);
        }
    }

    public function headers(): array
    {
        return array_keys(get_class_vars(Newsletter::class));
    }
}