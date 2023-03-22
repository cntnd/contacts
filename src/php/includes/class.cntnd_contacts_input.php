<?php

namespace Cntnd\Contacts;

/**
 * cntnd_contacts Class
 */
class CntndContactsInput
{
    private $client;
    private $db;

    public function __construct($client)
    {
        $this->client = $client;
        $this->db = new \cDb;
    }

    public function files(): array
    {
        $files = array();
        $client = $this->client;
        $cfgClient = \cRegistry::getClientConfig();
        $path = $cfgClient[$client]["upl"]["path"];

        //$this->uploadDir = $cfgClient[$client]["upl"]["htmlpath"];

        $cfg = \cRegistry::getConfig();

        $sql = "SELECT idupl, filename, dirname  FROM :table WHERE idclient=:idclient AND filetype = 'csv' ORDER BY dirname ASC, filename ASC";
        $values = array(
            'table' => $cfg['tab']['upl'],
            'idclient' => \cSecurity::toInteger($client)
        );
        $this->db->query($sql, $values);
        while ($this->db->nextRecord()) {
            $idupl = $this->db->f('idupl');
            $filename = $this->db->f('dirname') . $this->db->f('filename');
            $file = $path . $filename;
            $files[$idupl] = array('idupl' => $idupl, 'filename' => $filename, 'file' => $file);
        }
        return $files;
    }

    public function headers(?string $csv, ?string $delimiter): array
    {
        if (!empty($csv) && !empty($delimiter)) {
            if (file_exists($csv)) {
                $handle = fopen($csv, "r");
                $headers = fgetcsv($handle, 1000, $delimiter);
                fclose($handle);
                return $headers;
            }
        }
        return array();
    }

    public function folders(): array
    {
        $folders = array();
        $folders = $this->_getAllSubdirectories("", $folders);
        return $folders;
    }

    private function _getAllSubdirectories($directoryPath, array $directories) {
        $cfgClient = \cRegistry::getClientConfig();
        $path = $cfgClient[$this->client]["upl"]["path"];

        $handle = \cDirHandler::read($path . $directoryPath . '/', false, true);

        if (false !== $handle) {
            foreach ($handle as $entry) {
                if (\cFileHandler::fileNameBeginsWithDot($entry) === false) {
                    $directories[] = $directoryPath . '/' . $entry;
                    $directories = $this->_getAllSubdirectories($directoryPath . '/' . $entry, $directories);
                }
            }
        }

        return $directories;
    }
}

?>
