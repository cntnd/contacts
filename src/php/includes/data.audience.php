<?php

use Contacts\Data\Data;
use Contacts\Data\Mapping;
use Selective\ArrayReader\ArrayReader;

class Audience implements Data
{
    public string $vorname;
    public string $name;
    public string $email;
    public bool $newsletter;
    public string $timestamp;

    /**
     * The constructor.
     *
     * @param array $data The data
     */
    public function __construct(array $data, string $timestamp, bool $unsubscribe = false)
    {
        $reader = new ArrayReader($data);

        $this->vorname = $reader->findString('First Name', $reader->findString('vorname', Mapping::$default_string));
        $this->name = $reader->findString('Last Name', $reader->findString('name', Mapping::$default_string));
        $this->email = $reader->findString('Email Address', $reader->findString('email', Mapping::$default_string));
        $this->newsletter = !$unsubscribe;
        $this->timestamp = $timestamp;
    }

    public static function of(array $data): Data
    {
        return new Audience($data, $data['timestamp'], !$data['newsletter']);
    }

    public function record(): array
    {
        return (array)$this;
    }

    public function identifier(): string
    {
        return $this->name . " " . $this->vorname;
    }

    public function timestamp(): ?string
    {
        return $this->timestamp;
    }

    public function index()
    {
        return $this->email;
    }
}