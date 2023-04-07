<?php

use Contacts\Helpers\Str;
use Contacts\Source\Source;
use CSVDB\CSVDB;
use CSVDB\Helpers\CSVConfig;
use ParseCsv\Csv;


cInclude('module', 'includes/data.audience.php');
cInclude('module', 'includes/AudienceConverter.php');

class Mailchimp implements Source
{
    private Csv $csv;
    private CSVDB $csvdb;
    private string $directory;

    /**
     * @throws Exception
     */
    public function __construct(string $directory)
    {
        if (!is_dir($directory)) {
            throw new \Exception("Directory ($directory) is not valid");
        }

        $this->csv = new Csv();
        $this->csvdb = new CSVDB($directory . "/mailchimp.csv", new CSVConfig(2, CSVConfig::ENCODING, ";", true, true, false));
        $this->directory = $directory;
    }

    /**
     * @throws \League\Csv\InvalidArgument
     * @throws \League\Csv\CannotInsertRecord
     * @throws \League\Csv\Exception
     */
    public function load(): array
    {
        $dir = $this->directory;
        $files = scandir($dir, SCANDIR_SORT_DESCENDING);
        foreach ($files as $filename) {
            $file = $dir . "/" . $filename;
            if (is_file($file)) {
                $path_parts = pathinfo($file);
                if (strtolower($path_parts['extension']) == "csv" && strtolower($path_parts['filename']) !== "mailchimp") {
                    $this->load_csv($file);
                } elseif (strtolower($path_parts['extension']) == "zip") {
                    $this->unzip($file);
                }
            }
        }
        return $this->csvdb->select()->get(new AudienceConverter());
    }

    private function unzip(string $file): void
    {
        $zip = new ZipArchive;
        if ($zip->open($file) === TRUE) {
            $zip->extractTo($this->directory);
            $zip->close();
            unlink($file);
        } else {
            throw new \Exception("Failed to extract Zip ($file)");
        }
    }

    /**
     * @throws \League\Csv\InvalidArgument
     * @throws \League\Csv\CannotInsertRecord
     * @throws \League\Csv\Exception
     */
    private function load_csv(string $file): void
    {
        $unsubscribe = $this->is_unsubscribe($file);
        $this->csv->offset = 1;
        $this->csv->parseFile($file);
        foreach ($this->csv->data as $record) {
            $audience = new Audience($record, date("d.m.Y H:i:s", time()), $unsubscribe);
            $this->csvdb->upsert($audience->record());
        }
        unlink($file);
    }

    private function is_unsubscribe(string $file): bool
    {
        $path_parts = pathinfo($file);
        return Str::starts_with($path_parts['filename'], "unsubscribed");
    }

    public function last_load(): ?string
    {
        return null;
    }

    public function name(): string
    {
        return "Mailchimp";
    }

    public function archive($index): void
    {
        $this->csvdb->delete(["email"=>$index]);
    }

    public function headers(): array
    {
        return array_keys(get_class_vars(Audience::class));
    }
}