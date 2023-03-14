<?php

use Contacts\Source\Source;


cInclude('module', 'includes/data.infomail.php');

class ContenidoInfomail implements Source
{
    private $db;
    const TABLE = "infomail";

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
            $data[] = Infomail::of($this->db->toArray());
        }
        return $data;
    }

    public function last_load(): ?string
    {
        return null;
    }

    public function name(): string
    {
        return "Contenido: Infomail";
    }

    public function archive($index): void
    {
        $sql = "DELETE FROM :table WHERE id=:index ";
        $values = array(
            'table' => self::TABLE,
            'index' => \cSecurity::toString($index)
        );
        $this->db->query($sql, $values);
    }
}