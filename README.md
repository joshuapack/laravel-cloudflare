# Laravel Cloudflare
The Cloudflare API for Laravel 8

[See documentation](https://github.com/joshuapack/laravel-cloudflare/wiki)

### How to Install
First do `composer require joshuapack/laravel-cloudflare`

Get the following information.
1. Your Cloudflare email, simple, the one you use to log in with.
2. Your Cloudflare API Key, found in your account settings.
3. The Zone ID for the domain you want to edit, this is on the main page for the domain.

Put them in your `.env` as the following, obviously and respectively.
1. `CLOUDFLARE_EMAIL`
2. `CLOUDFLARE_API_KEY`
3. `CLOUDFLARE_ZONE_ID`

### How to use
There is a Facade `CloudFlare`

You can also use `$cf = app()->make('laravel-cloudflare')`

Then use `$cf` to make the calls like `$cf->setZoneId('12312312312312312312323')`

For example, you could then list the records like so
`$cf->listRecords()`
Which would return a collection of your records for that zone.

### Current Getters/Setters
 - `zoneId`

### Current Methods
 - `listZones`
 - `addRecord`
 - `listRecords`
 - `getRecordDetails`
 - `updateRecordDetails`
 - `deleteRecord`

### Direct Queries
You can see all API calls https://github.com/cloudflare/cloudflare-php for direct querying, however, we only have a couple available at this time.

 - `queryDNS`
 - `queryFirewall`
 - `queryFirewallSettings`
 - `queryZones`
 - `queryZoneSettings`


### Questions
If you have any questions feel free to ask in the issues tab on github. Same with adding more direct queries or other methods.