<?php
$oc_hm_status = 'status';
$oc_hm_desc = 'desc';
$oc_hm_score = 'score';
$oc_hm_title = 'title';


if (!function_exists('oc_sh_save_result')) {
    function oc_sh_save_result($stage, $oc_hm_status, $finish = 0)
    {
        global $oc_hm_score;
        $result = get_site_transient('ocsh_site_scan_result');
        $time = time();
        if (!$result) {
            $result = [];
        }
        $result['time'] = $time;
        $result[$stage] = $oc_hm_status;
        $save = set_site_transient('ocsh_site_scan_result', $result, 4 * HOUR_IN_SECONDS);

        if ($finish == 1) {
            unset($result['time']);
            $health = [];
            $health['issues'] = $result;
            $health[$oc_hm_score] = round(oc_sh_calculate_score($result)[$oc_hm_score]);

            /* save health monitor result */
            oc_sh_log_entry('== one.com Health Monitor Scan ==');
            oc_sh_log_entry(json_encode($health), 1);

            (class_exists('OCPushStats') ? \OCPushStats::push_health_monitor_stats_request('scan', 'blog', OCPushStats::get_subdomain(), '1', $health) : '');
        }

        return $save;
    }
}

if (!function_exists('oc_sh_calculate_score')) {
    function oc_sh_calculate_score($transient)
    {
        global $oc_hm_score;
        if (!$transient || empty($transient)) {
            return 0;
        }
        $ignored_checks = get_site_option('oc_marked_resolved', []);
        if (empty($ignored_checks)) {
            $ignored_checks = [];
        }
        @$time = $transient['time'];
        unset($transient['time']);

        $success = 0;
        $todo = 0;

        foreach ($transient as $check => $score) {
            if (in_array(str_replace(['ocsh_fix_', 'ocsh_fix_check_', 'check_'], '', $check), $ignored_checks)){
                continue;
            }

            if ($score == OC_RESOLVED) {
                $success++;
            } elseif ($score == OC_OPEN) {
                $todo++;
            }
        }
        $percent = floor(($success * 100) / count($transient));
        if ($percent == '100.00') {
            $percent = 100;
        }


        return [
            $oc_hm_score => $percent,
            'time' => $time,
            'todo' => $todo
        ];
    }
}