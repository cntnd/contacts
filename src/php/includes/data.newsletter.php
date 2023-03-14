<?php

use Contacts\Data\Data;
use Contacts\Data\Mapping;
use Selective\ArrayReader\ArrayReader;

class Newsletter implements Data
{
    public string $vorname;
    public string $name;
    public string $strasse;
    public string $plz;
    public string $ort;
    public string $email;

    public bool $infomail_spontan;
    public bool $newsletter;

    private ?string $timestamp;
    private int $index;

    /**
     * The constructor.
     *
     * @param array $data The data
     */
    public function __construct(array $data = [])
    {
        $reader = new ArrayReader($data);

        $this->vorname = $reader->findString('vorname', Mapping::$default_string);
        $this->name = $reader->findString('name', Mapping::$default_string);
        $this->strasse = $reader->findString('strasse', Mapping::$default_string);
        $this->plz = $reader->findString('plz', Mapping::$default_string);
        $this->ort = $reader->findString('ort', Mapping::$default_string);
        $this->email = $reader->findString('email');

        $this->infomail_spontan = $this->infomail($reader->findString('meldung_infomail'));
        $this->newsletter = $this->newsletter($reader->findString('meldung_newsletter'));

        $this->timestamp = $reader->findString('pifa_timestamp');
        $this->index = $reader->findInt('id');
    }

    private function newsletter(?string $val): bool
    {
        if (!empty($val)) {
            return ($val == "anmeldung-newsletter");
        }
        return Mapping::$default_bool;
    }

    private function infomail(?string $val): bool
    {
        if (!empty($val)) {
            return ($val == "anmeldung-infomail");
        }
        return Mapping::$default_bool;
    }

    public function record(): array
    {
        return (array)$this;
    }

    public static function of(array $data): Data
    {
        return new Newsletter($data);
    }

    public function timestamp(): ?string
    {
        return $this->timestamp;
    }

    public function identifier(): string
    {
        return $this->name . " " . $this->vorname;
    }

    public function index(): int
    {
        return $this->index;
    }
}