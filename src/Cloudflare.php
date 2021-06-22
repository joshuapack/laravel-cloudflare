<?php

namespace Joshuapack\Cloudflare;

use Cloudflare\API\Auth\APIKey as Key;
use Illuminate\Support\Traits\Macroable;
use GuzzleHttp\Exception\ClientException;
use Cloudflare\API\Endpoints\DNS as CF_DNS;
use Cloudflare\API\Endpoints\IPs as CF_IPs;
use Cloudflare\API\Adapter\Guzzle as Adapter;

class Cloudflare
{
    use Macroable;

    protected $zone;
    protected $dns;
    protected $ips;

    public function __construct($email, $api, $zone = null)
    {
        $key = new Key($email, $api);
        $adapter = new Adapter($key);
        $this->zone = $zone;
        $this->dns = new CF_DNS($adapter);
        $this->ips = new CF_IPs($adapter);
    }

    public function getZone()
    {
        return $this->zone;
    }

    public function setZone($zone)
    {
        $this->zone = $zone;
        return $this;
    }

    /*
     * DNS Queries
     */
    public function addRecord($name, $content = null, $type = 'A', $ttl = 0, $proxied = true)
    {
        if (!$this->zone) {
            return false;
        }

        if ($content == null && $type = 'A') {
            $content = $_SERVER['SERVER_ADDR'];
        }

        try {
            return $this->dns->addRecord($this->zone, $type, $name, $content, $ttl, $proxied);
        } catch (ClientException $e) {
            return false;
        }
    }

    public function listRecords($info = false, $page = 0, $perPage = 20, $order = '', $direction = '', $type = '', $name = '', $content = '', $match = 'all')
    {
        if (!$this->zone) {
            return false;
        }

        $records = $this->dns->listRecords($this->zone, $type, $name, $content, $page, $perPage, $order, $direction);

        if ($info) {
            return $records;
        }

        return collect($records->result);
    }

    public function getRecordDetails($recordId)
    {
        if (!$this->zone) {
            return false;
        }

        return $this->dns->getRecordDetails($this->zone, $recordId);
    }

    public function updateRecordDetails($recordId, array $details)
    {
        if (!$this->zone) {
            return false;
        }

        return $this->dns->updateRecordDetails($this->zone, $recordId, $details);
    }

    public function deleteRecord($recordId)
    {
        if (!$this->zone) {
            return false;
        }

        return $this->dns->deleteRecord($this->zone, $recordId);
    }

    /*
     * IP Queries
     */
    public function listIPs()
    {
        return $this->ips->listIPs();
    }
}
