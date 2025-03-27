<?php

namespace Joshuapack\Cloudflare;

use Cloudflare\API\Auth\Auth;
use Cloudflare\API\Auth\APIKey as Key;
use Cloudflare\API\Auth\APIToken as Token;
use Illuminate\Support\Traits\Macroable;
use GuzzleHttp\Exception\ClientException;
use Cloudflare\API\Endpoints\AccessRules;
use Cloudflare\API\Endpoints\AccountMembers;
use Cloudflare\API\Endpoints\AccountRoles;
use Cloudflare\API\Endpoints\Accounts;
use Cloudflare\API\Endpoints\Certificates;
use Cloudflare\API\Endpoints\Crypto;
use Cloudflare\API\Endpoints\CustomHostnames;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Endpoints\DNSAnalytics;
use Cloudflare\API\Endpoints\Firewall;
use Cloudflare\API\Endpoints\FirewallSettings;
use Cloudflare\API\Endpoints\IPs;
use Cloudflare\API\Endpoints\LoadBalancers;
use Cloudflare\API\Endpoints\Membership;
use Cloudflare\API\Endpoints\PageRules;
use Cloudflare\API\Endpoints\Pools;
use Cloudflare\API\Endpoints\Railgun;
use Cloudflare\API\Endpoints\SSL;
use Cloudflare\API\Endpoints\TLS;
use Cloudflare\API\Endpoints\UARules;
use Cloudflare\API\Endpoints\User;
use Cloudflare\API\Endpoints\WAF;
use Cloudflare\API\Endpoints\ZoneLockdown;
use Cloudflare\API\Endpoints\ZoneSettings;
use Cloudflare\API\Endpoints\ZoneSubscriptions;
use Cloudflare\API\Endpoints\Zones;
use Cloudflare\API\Adapter\Guzzle as Adapter;

class Cloudflare
{
    use Macroable;

    protected $zoneId;
    protected AccessRules $accessRules;
    protected AccountMembers $accountMembers;
    protected AccountRoles $accountRoles;
    protected Accounts $accounts;
    protected Certificates $certificates;
    protected Crypto $crypto;
    protected CustomHostnames $customHostnames;
    protected DNS $dns;
    protected DNSAnalytics $dnsAnalytics;
    protected Firewall $firewall;
    protected FirewallSettings $firewallSettings;
    protected IPs $ips;
    protected LoadBalancers $loadBalancers;
    protected Membership $membership;
    protected PageRules $pageRules;
    protected Pools $pools;
    protected Railgun $railgun;
    protected SSL $ssl;
    protected TLS $tls;
    protected UARules $uaRules;
    protected User $user;
    protected WAF $waf;
    protected ZoneLockdown $zoneLockdown;
    protected ZoneSettings $zoneSettings;
    protected ZoneSubscriptions $zoneSubscriptions;
    protected Zones $zones;

    public function __construct($email, $api, $token = null, $zoneId = null)
    {
        $auth = $this->getAuth($email, $api, $token);
        if (empty($auth)) {
            // Ideally it throws exception, but for now, it silently stops.
            return;
        }

        $adapter = new Adapter($auth);
        $this->zoneId = $zoneId;
        $this->accessRules = new AccessRules($adapter);
        $this->accountMembers = new AccountMembers($adapter);
        $this->accountRoles = new AccountRoles($adapter);
        $this->accounts = new Accounts($adapter);
        $this->certificates = new Certificates($adapter);
        $this->crypto = new Crypto($adapter);
        $this->customHostnames = new CustomHostnames($adapter);
        $this->dns = new DNS($adapter);
        $this->dnsAnalytics = new DNSAnalytics($adapter);
        $this->firewall = new Firewall($adapter);
        $this->firewallSettings = new FirewallSettings($adapter);
        $this->ips = new IPs($adapter);
        $this->loadBalancers = new LoadBalancers($adapter);
        $this->membership = new Membership($adapter);
        $this->pageRules = new PageRules($adapter);
        $this->pools = new Pools($adapter);
        $this->railgun = new Railgun($adapter);
        $this->ssl = new SSL($adapter);
        $this->tls = new TLS($adapter);
        $this->uaRules = new UARules($adapter);
        $this->user = new User($adapter);
        $this->waf = new WAF($adapter);
        $this->zoneLockdown = new ZoneLockdown($adapter);
        $this->zoneSettings = new ZoneSettings($adapter);
        $this->zoneSubscriptions = new ZoneSubscriptions($adapter);
        $this->zones = new Zones($adapter);
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
     * Query Cloudflare ZoneSettings API Endpoints directly
     */
    public function queryZoneSettings()
    {
        return $this->zoneSettings;
    }

    /**
     * Query Cloudflare Zones API Endpoints directly
     */
    public function queryZones()
    {
        return $this->zones;
    }

    /**
     * Query any Cloudflate endpoint directly
     */
    public function queryCloudflare($prop)
    {
        if (property_exists($this, $prop)) {
            return $this->$prop;
        } else {
            return false;
        }
    }

    /**
     * Create authentication using API token or email and key combination.
     *
     * The preferred authorization scheme for interacting with the Cloudflare API is using token.
     * Check on how to create token here: https://developers.cloudflare.com/fundamentals/api/get-started/create-token/
     * 
     * The previous authorization scheme for interacting with the Cloudflare API is using email in conjunction with a Global API key.
     *
     * When possible, use API tokens instead of Global API keys.
     */
    private function getAuth($email, $api, $token): ?Auth
    {
        if (!empty($token)) {
            return new Token($token);
        }
        
        if (!empty($email) && !empty($api)) {
            return new Key($email, $api);
        }

        return null;
    }
}
