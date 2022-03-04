<?php
declare(strict_types=1);

class OnecomHealthMonitorCron extends OnecomHealthMonitor
{
    private $hm_transient = 'ocsh_site_scan_result';


    public function init()
    {
        add_filter('cron_schedules', [$this, 'cron_interval']);
        add_action('onecom_hm_hook_overall_log', [$this, 'onecom_hm_hook_overall_log_exec']);
        add_action('onecom_hm_hook_daily_scan', [$this, 'onecom_hm_hook_daily_exec']);
        $this->schedule_tasks();
    }

    public function cron_interval($schedules)
    {
        $schedules['onecom_hm_weekly'] = array(
            'interval' => 604800, // 1 week in seconds
            'display' => 'Once in a week',
        );
        $schedules['onecom_hm_daily'] = array(
            'interval' => 86400, // 24 hours in seconds.
            'display' => 'Once in a day',
        );

        return $schedules;
    }

    public function schedule_tasks()
    {
        if (!wp_next_scheduled('onecom_hm_hook_overall_log') && (!empty(get_site_transient($this->hm_transient)))) {
            wp_schedule_event(time(), 'onecom_hm_weekly', 'onecom_hm_hook_overall_log');
        }
        if (!wp_next_scheduled('onecom_hm_hook_daily_scan')) {
            wp_schedule_event(time(), 'onecom_hm_daily', 'onecom_hm_hook_daily_scan');
        }
    }

    public function onecom_hm_hook_overall_log_exec()
    {
        if (!class_exists('OCPushStats')) {
            return;
        }
        $scan_result = get_site_transient($this->hm_transient);
        $score = $this->calculate_score($scan_result);
        $ignored = json_encode(get_option($this->resolved_option, []));
        \OCPushStats::push_health_monitor_stats_request('scan', 'blog', OCPushStats::get_subdomain(), '1', $scan_result, ['item_source' => 'health_monitor', 'score' => "{$score['score']}", 'ignored_checks' => "{$ignored}"]);
    }

    public function onecom_hm_hook_daily_exec()
    {
        $this->run_scan();
    }

    // run all the scans, reusing API module
    public function run_scan()
    {
        if (!class_exists('OnecomPluginsApi')) {
            require_once ONECOM_WP_PATH . '/modules/api/class-onecom-plugins-api.php';
        }
        $api = new OnecomPluginsApi();
        $api->health_monitor_scan();
    }
}