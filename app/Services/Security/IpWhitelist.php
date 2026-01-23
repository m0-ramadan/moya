<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class IpWhitelist
{
    /**
     * Check if IP is allowed
     */
    // public function isAllowed(string $ip, array $allowedIps): bool
    // {
    //     // Cache the check for performance
    //     $cacheKey = 'ip_whitelist_' . md5($ip . implode('', $allowedIps));

    //     return Cache::remember($cacheKey, 300, function () use ($ip, $allowedIps) {
    //         foreach ($allowedIps as $allowedIp) {
    //             if ($this->ipMatches($ip, $allowedIp)) {
    //                 return true;
    //             }
    //         }

    //         Log::warning('IP address not whitelisted', [
    //             'ip' => $ip,
    //             'allowed_ips' => $allowedIps
    //         ]);

    //         return false;
    //     });
    // }

    /**
     * Check if IP matches pattern (supports CIDR)
     */
    private function ipMatches(string $ip, string $pattern): bool
    {
        // Direct match
        if ($ip === $pattern) {
            return true;
        }

        // CIDR notation
        if (strpos($pattern, '/') !== false) {
            list($subnet, $mask) = explode('/', $pattern);
            return $this->ipInCidr($ip, $subnet, (int)$mask);
        }

        // Wildcard notation
        if (strpos($pattern, '*') !== false) {
            $pattern = str_replace('*', '.*', preg_quote($pattern, '/'));
            return preg_match('/^' . $pattern . '$/', $ip) === 1;
        }

        return false;
    }

    /**
     * Check if IP is in CIDR range
     */
    private function ipInCidr(string $ip, string $cidrNetwork, int $cidrMask): bool
    {
        $ipLong = ip2long($ip);
        $networkLong = ip2long($cidrNetwork);

        if ($ipLong === false || $networkLong === false) {
            return false;
        }

        $mask = -1 << (32 - $cidrMask);
        $networkLong &= $mask;

        return ($ipLong & $mask) === $networkLong;
    }

    /**
     * Get Paymob's production IPs
     */
    public function getPaymobIps(): array
    {
        return [
            // Paymob production IPs (example - get actual IPs from Paymob documentation)
            '52.19.128.0/19',
            '52.210.0.0/18',
            '54.76.0.0/15',
            '54.247.0.0/16',
            '63.35.128.0/17',
            '99.80.0.0/15',
            '176.34.0.0/16',
            '34.240.0.0/13',
            '34.248.0.0/13',
            '52.16.0.0/15',
            '52.48.0.0/14',
            '52.208.0.0/13',
            '54.72.0.0/15',
            '54.76.0.0/15',
            '54.78.0.0/16',
            '54.154.0.0/16',
            '54.155.0.0/16',
            '54.170.0.0/15',
            '54.216.0.0/14',
            '54.220.0.0/15',
            '54.228.0.0/15',
            '54.246.0.0/16',
            '63.35.0.0/16',
            '79.125.0.0/17',
            '99.80.0.0/15',
            '176.34.128.0/17',
            '18.202.0.0/15',
            '34.250.0.0/16',
            '52.30.0.0/15',
            '52.48.0.0/14',
            '52.208.0.0/13',
            '54.72.0.0/15',
            '54.76.0.0/15',
            '54.78.0.0/16',
            '54.154.0.0/16',
            '54.155.0.0/16',
            '54.170.0.0/15',
            '54.216.0.0/14',
            '54.220.0.0/15',
            '54.228.0.0/15',
            '54.246.0.0/16'
        ];
    }
}
