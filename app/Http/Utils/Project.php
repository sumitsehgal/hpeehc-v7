<?php
namespace App\Http\Utils;

use Illuminate\Support\Facades\DB;

class Project {
    protected $sites;

    public function getSites() {
        if(!empty($this->sites)) {
            return $this->sites;
        }
        $siteBase = $this->getSiteBase();
        $dh = opendir($siteBase);
        if (!$dh) die("Cannot read directory '$siteBase'.");

        while (false !== ($sfname = readdir($dh))) {
            if (substr($sfname, 0, 1) == '.') continue;
            if ($sfname == 'CVS'            ) continue;
            $sitedir = "$siteBase/$sfname";
            if (!is_dir($sitedir)               ) continue;
            if (!is_file("$sitedir/sqlconf.php")) continue;
            $this->sites[$sfname] = $sfname;

        }
        closedir($dh);
        ksort($this->sites);
        return $this->sites;
    }


    public function getWebroot() {
        $webserver_root = dirname(dirname(__FILE__));
        $webserver_root = str_replace("\\","/",$webserver_root);
        return $webserver_root;
    }

    public function getSiteBase() {
        $OE_SITES_BASE = $this->getWebroot().'/sites';
        return $OE_SITES_BASE;
    }

}

?>