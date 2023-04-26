<?php

namespace Cntnd\Contacts;

require_once("class.cntnd_util.php");

class CntndContactsOutput extends CntndUtil
{
    private $idart;
    private $db;

    const TABLE = "cntnd_contacts";

    public function __construct($idart)
    {
        $this->idart = $idart;
        $this->db = new \cDb;
    }

    public function store(array $data): void
    {
        // required
        $this->store_required($data['required']);

        // data_type
        $this->store_data_type($data['data_type']);

        // mappings
        foreach ($data['mappings'] as $source => $mappings) {
            $this->store_mappings(array_filter($mappings), $source);
        }
    }

    private function store_required(array $data): void
    {
        $serializedata = array_keys($data);
        $values = array(
            'table' => self::TABLE,
            'idart' => \cSecurity::toInteger($this->idart),
            'source' => 'default',
            'type' => 'required',
            'data' => self::escapeData(json_encode($serializedata))
        );
        $this->store_values($values);
    }

    private function store_data_type(array $data): void
    {
        $values = array(
            'table' => self::TABLE,
            'idart' => \cSecurity::toInteger($this->idart),
            'source' => 'default',
            'type' => 'data_type',
            'data' => self::escapeData(json_encode($data))
        );
        $this->store_values($values);
    }

    private function store_mappings(array $data, string $source): void
    {
        $values = array(
            'table' => self::TABLE,
            'idart' => \cSecurity::toInteger($this->idart),
            'source' => $source,
            'type' => 'mapping',
            'data' => self::escapeData(json_encode($data))
        );
        $this->store_values($values);
    }

    private function store_values(array $values): void
    {
        $this->db->query("SELECT idart FROM :table WHERE idart=:idart AND source = ':source' AND type = ':type'", $values);
        if (!$this->db->nextRecord()) {
            $sql = "INSERT INTO :table (idart, type, source, serializeddata) VALUES (:idart,':type',':source',':data')";
        } else {
            $sql = "UPDATE :table SET serializeddata=':data' WHERE idart=:idart AND source = ':source' AND type = ':type'";
        }
        //var_dump($this->db->prepare($sql, $values));
        $this->db->query($sql, $values);
    }

    public function required(): array
    {
        $data = [];
        $sql = "SELECT serializeddata FROM :table WHERE idart=:idart AND source = 'default' AND type = 'required'";
        $values = array(
            'table' => self::TABLE,
            'idart' => \cSecurity::toInteger($this->idart)
        );
        $this->db->query($sql, $values);
        while ($this->db->nextRecord()) {
            if (is_string($this->db->f('serializeddata'))) {
                $data = self::unescapeData($this->db->f('serializeddata'));
            }
        }
        return $data;
    }

    public function data_types(): array
    {
        $data = [];
        $sql = "SELECT serializeddata FROM :table WHERE idart=:idart AND source = 'default' AND type = 'data_type'";
        $values = array(
            'table' => self::TABLE,
            'idart' => \cSecurity::toInteger($this->idart)
        );
        $this->db->query($sql, $values);
        while ($this->db->nextRecord()) {
            if (is_string($this->db->f('serializeddata'))) {
                $data = self::unescapeData($this->db->f('serializeddata'));
            }
        }
        return $data;
    }

    public function source_mapping(string $source): array
    {
        $data = [];
        $sql = "SELECT serializeddata FROM :table WHERE idart=:idart AND source = ':source' AND type = 'mapping'";
        $values = array(
            'table' => self::TABLE,
            'idart' => \cSecurity::toInteger($this->idart),
            'source' => $source
        );
        $this->db->query($sql, $values);
        while ($this->db->nextRecord()) {
            if (is_string($this->db->f('serializeddata'))) {
                $data = self::unescapeData($this->db->f('serializeddata'));
            }
        }
        return $data;
    }

    public function source_mappings(): array
    {
        $data = [];
        $sql = "SELECT * FROM :table WHERE idart=:idart AND type = 'mapping'";
        $values = array(
            'table' => self::TABLE,
            'idart' => \cSecurity::toInteger($this->idart)
        );
        $this->db->query($sql, $values);
        while ($this->db->nextRecord()) {
            if (is_string($this->db->f('serializeddata'))) {
                $data[$this->db->f('source')] = self::unescapeData($this->db->f('serializeddata'));
            }
        }

        return $data;
    }

    public function delete(): void
    {
        $sql = "DELETE FROM :table WHERE idart=:idart";
        $values = array(
            'table' => self::TABLE,
            'idart' => \cSecurity::toInteger($this->idart)
        );
        $this->db->query($sql, $values);
    }
}

?>
