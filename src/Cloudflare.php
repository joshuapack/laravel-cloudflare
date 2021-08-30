<?php

namespace Joshuapack\Cloudflare;

use Cloudflare\API\Auth\APIKey as Key;
use Illuminate\Support\Traits\Macroable;
use GuzzleHttp\Exception\ClientException;
use Cloudflare\API\Endpoints\DNS as CF_DNS;
use Cloudflare\API\Endpoints\Firewall as CF_FIREWALL;
use Cloudflare\API\Endpoints\FirewallSettings as CF_FIREWALLSETTINGS;
use Cloudflare\API\Endpoints\Zones as CF_ZONES;
use Cloudflare\API\Endpoints\ZoneSettings as CF_ZONESETTINGS;
use Cloudflare\API\Endpoints\IPs as CF_IPs;
use Cloudflare\API\Adapter\Guzzle as Adapter;

class Cloudflare
{
    use Macroable;

    protected $zoneId;
    protected CF_DNS $dns;
    protected CF_FIREWALL $firewall;
    protected CF_FIREWALLSETTINGS $firewallSettings;
    protected CF_ZONES $zones;
    protected CF_ZONESETTINGS $zoneSettings;
    protected CF_IPs $ips;

    public function __construct($email, $api, $zoneId = null)
    {
        if (!is_string($email) || !is_string($api)) {
            return;
        }

        $key = new Key($email, $api);
        $adapter = new Adapter($key);
        $this->zoneId = $zoneId;
        $this->dns = new CF_DNS($adapter);
        $this->firewall = new CF_FIREWALL($adapter);
        $this->firewallSettings = new CF_FIREWALLSETTINGS($adapter);
        $this->zones = new CF_ZONES($adapter);
        $this->zoneSettings = new CF_ZONESETTINGS($adapter);
        $this->ips = new CF_IPs($adapter);
    }

    public function getZoneId()
    {
        return $this->zoneId;
    }

    public function setZoneId(string $zoneId)
    {
        $this->zoneId = $zoneId;
        return $this;
    }
    /**
     * Get All Zone IDs
     */
    public function listZones(string $name = '', string $status = '', int $page = 1, int $perPage = 20, string $order = '', string $direction = '', string $match = 'all')
    {
        return $this->zones->listZones($name, $status, $page, $perPage, $order, $direction, $match);
    }

    /**
     * DNS Queries
     */
    public function addRecord(string $name, $content = null, string $type = 'A', int $ttl = 0, bool $proxied = true)
    {
        if (!$this->getZoneId() || !is_string($this->getZoneId())) {
            return false;
        }

        if ($content == null && $type = 'A') {
            $content = $_SERVER['SERVER_ADDR'];
        }

        try {
            return $this->dns->addRecord($this->getZoneId(), $type, $name, $content, $ttl, $proxied);
        } catch (ClientException $e) {
            return false;
        }
    }

    public function listRecords(bool $info = false, int $page = 1, int $perPage = 20, string $order = '', string $direction = '', string $type = '', string $name = '', string $content = '', string $match = 'all')
    {
        if (!$this->getZoneId() || !is_string($this->getZoneId())) {
            return false;
        }

        $records = $this->dns->listRecords($this->getZoneId(), $type, $name, $content, $page, $perPage, $order, $direction, $match);

        if ($info) {
            return $records;
        }

        return collect($records->result);
    }

    public function getRecordDetails(string $recordId)
    {
        if (!$this->getZoneId() || !is_string($this->getZoneId())) {
            return false;
        }

        return $this->dns->getRecordDetails($this->getZoneId(), $recordId);
    }

    public function updateRecordDetails(string $recordId, array $details)
    {
        if (!$this->getZoneId() || !is_string($this->getZoneId())) {
            return false;
        }

        return $this->dns->updateRecordDetails($this->getZoneId(), $recordId, $details);
    }

    public function deleteRecord(string $recordId)
    {
        if (!$this->getZoneId() || !is_string($this->getZoneId())) {
            return false;
        }

        return $this->dns->deleteRecord($this->getZoneId(), $recordId);
    }

    /**
     * IP Queries
     */
    public function listIPs()
    {
        return $this->ips->listIPs();
    }

    /**
     * Query Cloudflare DNS API Endpoints directly
     */
    public function queryDNS()
    {
        return $this->dns;
    }

    /**
     * Query Cloudflare Firewall API Endpoints directly
     */
    public function queryFirewall()
    {
        return $this->firewall;
    }

    /**
     * Query Cloudflare Firewall Settings API Endpoints directly
     */
    public function queryFirewallSettings()
    {
        return $this->firewallSettings;
    }

    /**
     * Query Cloudflare Zones API Endpoints directly
     */
    public function queryZones()
    {
        return $this->zones;
    }

    /**
     * Query Cloudflare ZoneSettings API Endpoints directly
     */
    public function queryZoneSettings()
    {
        return $this->zoneSettings;
    }

}
