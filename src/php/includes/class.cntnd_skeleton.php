<?php

namespace Cntnd\Skeleton;

require_once("class.cntnd_util.php");

/**
 * cntnd_SKELETON Class
 */
class CntndSkeleton extends CntndUtil
{

    private $lang;
    private $client;
    private $db;
    private $folders = array();

    function __construct($lang, $client)
    {
        $this->lang = $lang;
        $this->client = $client;
        $this->db = new \cDb;

        // contenido config
        $cfg = \cRegistry::getConfig();

        // folders

        $sql = "SELECT DISTINCT dirname FROM :table WHERE idclient=:idclient ORDER BY dirname ASC";
        $values = array(
            'table' => $cfg['tab']['upl'],
            'idclient' => \cSecurity::toInteger($client)
        );
        $this->db->query($sql, $values);
        while ($this->db->nextRecord()) {
            $this->folders[] = $this->db->f('dirname');
        }

        // endregion
    }

    public function folders()
    {
        return $this->folders;
    }
}

?>
