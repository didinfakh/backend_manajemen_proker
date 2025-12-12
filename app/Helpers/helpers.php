<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('getQueryLog')) {
    /**
     * Mendapatkan query log dengan parameter yang sudah digabung
     * 
     * @param bool $enableLogging Aktifkan query logging jika belum aktif
     * @return array Array berisi query log dengan SQL dan parameter yang sudah digabung
     */
    function getQueryLog(bool $enableLogging = false): array
    {
        // Aktifkan query logging jika belum aktif
        if ($enableLogging) {
            DB::enableQueryLog();
        }

        $queryLog = DB::getQueryLog();
        $formattedLogs = [];

        foreach ($queryLog as $log) {
            $sql = $log['query'];
            $bindings = $log['bindings'] ?? [];
            $time = $log['time'] ?? 0;

            // Replace parameter placeholder dengan nilai sebenarnya
            $formattedSql = $sql;
            foreach ($bindings as $binding) {
                $value = is_numeric($binding) ? $binding : "'" . addslashes($binding) . "'";
                $formattedSql = preg_replace('/\?/', $value, $formattedSql, 1);
            }

            $formattedLogs[] = [
                'sql' => $sql,
                'bindings' => $bindings,
                'formatted_sql' => $formattedSql,
                'time' => $time . 'ms',
            ];
        }

        return $formattedLogs;
    }
}

if (!function_exists('getLastQuery')) {
    /**
     * Mendapatkan query terakhir dengan parameter yang sudah digabung
     * 
     * @param bool $enableLogging Aktifkan query logging jika belum aktif
     * @return array|null Query log terakhir atau null jika tidak ada
     */
    function getLastQuery(bool $enableLogging = false): ?array
    {
        $logs = getQueryLog($enableLogging);
        return !empty($logs) ? end($logs) : null;
    }
}

if (!function_exists('ddQueryLog')) {
    /**
     * Dump dan die query log dengan parameter yang sudah digabung
     * 
     * @param bool $enableLogging Aktifkan query logging jika belum aktif
     * @return void
     */
    function ddQueryLog(bool $enableLogging = false): void
    {
        dd(getQueryLog($enableLogging));
    }
}

