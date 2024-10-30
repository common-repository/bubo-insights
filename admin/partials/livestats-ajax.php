<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php
$starttime = microtime();

global $wpdb;

$timewindow = 'tday';
if( isset($_REQUEST['timewindow']) ) $timewindow = sanitize_text_field( wp_unslash( $_REQUEST['timewindow'] ) );

$who = '';
if( isset($_REQUEST['who']) ) $who = array_map( 'sanitize_text_field' , wp_unslash( $_REQUEST['who'] ) );

$where = '';
if( isset($_REQUEST['where']) ) $where = array_map( 'sanitize_text_field' , wp_unslash( $_REQUEST['where'] ) );

$now = time();
$now_timezoned = current_time( 'timestamp' );
$timezone_delta = $now - $now_timezoned;

$this_month_days = cal_days_in_month(CAL_GREGORIAN, wp_date('n', $now), wp_date('Y',$now) );
$prev_month_days = cal_days_in_month(CAL_GREGORIAN, wp_date('n', $now)-1, wp_date('Y',$now) );
$this_year_days = 29 - cal_days_in_month(CAL_GREGORIAN, 2, wp_date('Y',$now) ) + 365;
$prev_year_days = 29 - cal_days_in_month(CAL_GREGORIAN, 2, wp_date('Y',$now)-1 ) + 365;

$timewindow_chart = array(
    'hour'  => array( 'timebin_count' => 12 ,                   'timebin_seconds' => 300 ,          'timewindow_seconds' => 3600,                       'endtime' => $now + 300 - ($now % 300)                                                              ),
    'day'   => array( 'timebin_count' => 24 ,                   'timebin_seconds' => 3600 ,         'timewindow_seconds' => 86400,                      'endtime' => $now + 3600 - ($now % 3600)                                                            ),
    'week'  => array( 'timebin_count' => 7  ,                   'timebin_seconds' => 86400 ,        'timewindow_seconds' => 86400*7,                    'endtime' => $now + 86400 - ($now % 86400)                                                          ),
    'month' => array( 'timebin_count' => 30 ,                   'timebin_seconds' => 86400 ,        'timewindow_seconds' => 86400*30,                   'endtime' => $now + 86400 - ($now % 86400)                                                          ),
    'year'  => array( 'timebin_count' => 12 ,                   'timebin_seconds' => 86400*30 ,     'timewindow_seconds' => 86400*365,                  'endtime' => $now + 86400 - ($now % 86400)                                                          ),
    
    'all'   => array( 'timebin_count' => 12 ,                   'timebin_seconds' => 86400*365 ,    'timewindow_seconds' => 86400*365*12,               'endtime' => $now                                                                                   ),
    
    'tday'   => array( 'timebin_count' => 24 ,                  'timebin_seconds' => 3600 ,         'timewindow_seconds' => 86400,                      'endtime' => strtotime('tomorrow', $now_timezoned ) + $timezone_delta                               ),
    'tweek'  => array( 'timebin_count' => 7  ,                  'timebin_seconds' => 86400 ,        'timewindow_seconds' => 86400*7,                    'endtime' => strtotime('next week midnight', $now_timezoned) + $timezone_delta                      ),
    'tmonth' => array( 'timebin_count' => $this_month_days ,    'timebin_seconds' => 86400 ,        'timewindow_seconds' => 86400*$this_month_days,     'endtime' => strtotime('first day of next month midnight', $now_timezoned) + $timezone_delta        ),
    'tyear'  => array( 'timebin_count' => 12 ,                  'timebin_seconds' => 86400*30 ,     'timewindow_seconds' => 86400*$this_year_days,      'endtime' => strtotime('1st january next year midnight', $now_timezoned) + $timezone_delta          ),
    
    'pday'   => array( 'timebin_count' => 24 ,                  'timebin_seconds' => 3600 ,         'timewindow_seconds' => 86400,                      'endtime' => strtotime('today', $now_timezoned) + $timezone_delta                                   ),
    'pweek'  => array( 'timebin_count' => 7  ,                  'timebin_seconds' => 86400 ,        'timewindow_seconds' => 86400*7,                    'endtime' => strtotime('this week midnight', $now_timezoned) + $timezone_delta                      ),
    'pmonth' => array( 'timebin_count' => $prev_month_days ,    'timebin_seconds' => 86400 ,        'timewindow_seconds' => 86400*$prev_month_days,     'endtime' => strtotime('first day of this month midnight', $now_timezoned) + $timezone_delta        ),
    'pyear'  => array( 'timebin_count' => 12 ,                  'timebin_seconds' => 86400*30 ,     'timewindow_seconds' => 86400*$prev_year_days,      'endtime' => strtotime('1st january this year midnight', $now_timezoned) + $timezone_delta          ),
);

$who_q['desktop'] = '-';
$who_q['mobile'] = '-';
$who_q['tablet'] = '-';
$who_q['unknown'] = '-';
$who_q['win'] = '-';
$who_q['apple'] = '-';
$who_q['unix'] = '-';
$who_q['other'] = '-';
$who_q['loggedin'] = 2;
$who_q['loggedout'] = 2;
if('true'==$who['desktop']) $who_q['desktop'] = 'd';
if('true'==$who['mobile'])  $who_q['mobile'] = 'm';
if('true'==$who['tablet'])  $who_q['tablet'] = 't';
if('true'==$who['unknown']) $who_q['unknown'] = '?';
if('true'==$who['win'])     $who_q['win'] = 'w';
if('true'==$who['apple'])   $who_q['apple'] = 'a';
if('true'==$who['unix'])    $who_q['unix'] = 'u';
if('true'==$who['other'])   $who_q['other'] = '?';
if('true'==$who['loggedin'])    $who_q['loggedin'] = 1;
if('true'==$who['loggedout'])   $who_q['loggedout'] = 0;


$where_comparison_chart = array(
    'islike' => 'LIKE',
    'notlike' => 'NOT LIKE',
    'isequal' => '=',
    'notequal' => '!='
);
$where_q['referrerslike'] = $where_comparison_chart[$where['referrerslike']];
$where_q['originlike'] = $where_comparison_chart[$where['originlike']];
$where_q['referredlike'] = $where_comparison_chart[$where['referredlike']];

$like_symbol = array(
    'LIKE' => '%',
    'NOT LIKE' => '%',
    '=' => '',
    '!=' => ''
);

$where_q['referrers'] = $like_symbol[$where_q['referrerslike']] . '-' . $like_symbol[$where_q['referrerslike']];
$where_q['origin'] = $like_symbol[$where_q['originlike']] . '-' . $like_symbol[$where_q['originlike']];
$where_q['referred'] = $like_symbol[$where_q['referredlike']] . '-' . $like_symbol[$where_q['referredlike']];
$where_q['referrersswitch'] = 1;
$where_q['originswitch'] = 1;
$where_q['referredswitch'] = 1;
if(!empty($where['referrersfilter'])) {
    $where_q['referrers'] = $like_symbol[$where_q['referrerslike']] . $wpdb->esc_like( esc_sql( $where['referrersfilter'] ) ) . $like_symbol[$where_q['referrerslike']];
    $where_q['referrersswitch'] = 0;
}
if(!empty($where['originfilter'])) {
    $where_q['origin'] = $like_symbol[$where_q['originlike']] . $wpdb->esc_like( esc_sql( $where['originfilter'] ) ) . $like_symbol[$where_q['originlike']];
    $where_q['originswitch'] = 0;
}
if(!empty($where['referredfilter'])) {
    $where_q['referred'] = $like_symbol[$where_q['referredlike']] . $wpdb->esc_like( esc_sql( $where['referredfilter'] ) ) . $like_symbol[$where_q['referredlike']];
    $where_q['referredswitch'] = 0;
}

$where_link_extra_conditions = array(
    'LIKE' => '',
    'NOT LIKE' => ' OR link IS NULL ',
    '=' => '',
    '!=' => ' OR link IS NULL ',
);

$query_select =  'Ceil(eventtime / %d) as timebin';
$query_groupby = 'timebin';
/* wip
if( $timewindow_chart[$timewindow]['timebin_seconds'] > 86399 ) {
    $query_select = 'DAY(FROM_UNIXTIME(eventtime + 86400)) as day , MONTH(FROM_UNIXTIME(eventtime + 86400)) as month, YEAR(FROM_UNIXTIME(eventtime + 86400)) as year, Ceil(eventtime / %d) as timebin';
    $query_groupby = 'CONCAT( day, month, year )';
}
*/

$pageview_event_timebins_query_query = 
"SELECT
    " . $query_select . " ,
    count(DISTINCT user) as distinct_visitor_count,
    count(DISTINCT pagesession) as distinct_view_count,
    count(DISTINCT CONCAT(user, origin)) as distinct_visit_count,
    count(DISTINCT( CASE device WHEN 'd' THEN user ELSE null END ) ) as distinct_desktop_count,
    count(DISTINCT( CASE device WHEN 'm' THEN user ELSE null END ) ) as distinct_mobile_count,
    count(DISTINCT( CASE device WHEN 't' THEN user ELSE null END ) ) as distinct_tablet_count,
    count(DISTINCT( CASE device WHEN '?' THEN user ELSE null END ) ) as distinct_unknown_count,
    count(DISTINCT( CASE os WHEN 'w' THEN user ELSE null END ) ) as distinct_win_count,
    count(DISTINCT( CASE os WHEN 'a' THEN user ELSE null END ) ) as distinct_apple_count,
    count(DISTINCT( CASE os WHEN 'u' THEN user ELSE null END ) ) as distinct_unix_count,
    count(DISTINCT( CASE os WHEN '?' THEN user ELSE null END ) ) as distinct_other_count,
    count(DISTINCT( CASE loggedin WHEN 1 THEN user ELSE null END ) ) as distinct_loggedin_count,
    count(DISTINCT( CASE loggedin WHEN 0 THEN user ELSE null END ) ) as distinct_loggedout_count
FROM (
    SELECT * 
    FROM wp_bubo_insights_event_log 
    WHERE ( eventtime >= %d AND eventtime < %d )
    
    AND ( device IS NULL OR device = %s OR device = %s OR device = %s OR device = %s )
    AND ( os IS NULL OR os = %s OR os = %s OR os = %s OR os = %s )
    AND ( loggedin = %d OR loggedin = %d )
    
    AND ( 1 = %d OR origin " . $where_q['originlike'] . " %s )
    AND ( 1 = %d OR referrer " . $where_q['referrerslike'] . " %s )
    AND ( 1 = %d OR link " . $where_q['referredlike'] . " %s " . $where_link_extra_conditions[$where_q['referredlike']] . ")
    
) as selected_timewindow
GROUP BY " . $query_groupby . "
ORDER BY eventtime DESC";

$pageview_event_timebins_query_array = array( 
    $timewindow_chart[$timewindow]['timebin_seconds'],
    $timewindow_chart[$timewindow]['endtime'] - $timewindow_chart[$timewindow]['timewindow_seconds'] , $timewindow_chart[$timewindow]['endtime'],
    
    $who_q['desktop'], $who_q['mobile'], $who_q['tablet'], $who_q['unknown'],
    $who_q['win'], $who_q['apple'], $who_q['unix'], $who_q['other'],
    $who_q['loggedin'], $who_q['loggedout'],
    
    $where_q['originswitch'], $where_q['origin'],
    $where_q['referrersswitch'], $where_q['referrers'],
    $where_q['referredswitch'], $where_q['referred'],
    
    $timewindow_chart[$timewindow]['timebin_seconds'],
);

$pageview_event_timebins_query = $wpdb->get_results( $wpdb->prepare( $pageview_event_timebins_query_query, $pageview_event_timebins_query_array ) );

$click_event_timebins_query_query =
"SELECT
    Ceil(eventtime / %d) as timebin ,
    count(linkbound) as click_count ,
    count(CASE linkbound WHEN 'o' THEN 1 ELSE null END ) as eclick_count,
    count(CASE linkbound WHEN 'i' THEN 1 ELSE null END ) as iclick_count
FROM (
    SELECT * 
    FROM wp_bubo_insights_event_log 
    WHERE ( eventtime >= %d AND eventtime < %d AND event = 'c' )
    
    AND ( device IS NULL OR device = %s OR device = %s OR device = %s OR device = %s )
    AND ( os IS NULL OR os = %s OR os = %s OR os = %s OR os = %s )
    AND ( loggedin = %d OR loggedin = %d )
    
    AND ( 1 = %d OR origin " . $where_q['originlike'] . " %s )
    AND ( 1 = %d OR referrer " . $where_q['referrerslike'] . " %s )
    AND ( 1 = %d OR link " . $where_q['referredlike'] . " %s " . $where_link_extra_conditions[$where_q['referredlike']] . ")
    
) as selected_timewindow
GROUP BY Ceil(eventtime / %d)
ORDER BY eventtime DESC";

$click_event_timebins_query_array = array( 
    $timewindow_chart[$timewindow]['timebin_seconds'],
    $timewindow_chart[$timewindow]['endtime'] - $timewindow_chart[$timewindow]['timewindow_seconds'] , $timewindow_chart[$timewindow]['endtime'],
    
    $who_q['desktop'], $who_q['mobile'], $who_q['tablet'], $who_q['unknown'],
    $who_q['win'], $who_q['apple'], $who_q['unix'], $who_q['other'],
    $who_q['loggedin'], $who_q['loggedout'],
    
    $where_q['originswitch'], $where_q['origin'],
    $where_q['referrersswitch'], $where_q['referrers'],
    $where_q['referredswitch'], $where_q['referred'],
    
    $timewindow_chart[$timewindow]['timebin_seconds']
);

$click_event_timebins_query = $wpdb->get_results( $wpdb->prepare( $click_event_timebins_query_query, $click_event_timebins_query_array ) );

$grouped_origin_query_query = 
"SELECT
    count(DISTINCT pagesession) as origin_view_count,
    count(DISTINCT CONCAT(user, origin)) as origin_visit_count,
    count(DISTINCT user) as origin_visitor_count,
    count(DISTINCT (CASE device WHEN 'd' THEN user ELSE null END )) as origin_desktop_count,
    count(DISTINCT (CASE device WHEN 'm' THEN user ELSE null END )) as origin_mobile_count,
    count(DISTINCT (CASE device WHEN 't' THEN user ELSE null END )) as origin_tablet_count,
    count(DISTINCT (CASE device WHEN '?' THEN user ELSE null END )) as origin_unknown_device_count,
    origin
FROM (
    SELECT * 
    FROM wp_bubo_insights_event_log 
    WHERE ( eventtime >= %d AND eventtime < %d )
    
    AND ( device IS NULL OR device = %s OR device = %s OR device = %s OR device = %s )
    AND ( os IS NULL OR os = %s OR os = %s OR os = %s OR os = %s )
    AND ( loggedin = %d OR loggedin = %d )
    
    AND ( 1 = %d OR origin " . $where_q['originlike'] . " %s )
    AND ( 1 = %d OR referrer " . $where_q['referrerslike'] . " %s )
    AND ( 1 = %d OR link " . $where_q['referredlike'] . " %s " . $where_link_extra_conditions[$where_q['referredlike']] . ")
    
) as selected_timewindow
GROUP BY origin
ORDER BY count(DISTINCT user) DESC";

$grouped_origin_query_array = array( 
    $timewindow_chart[$timewindow]['endtime'] - $timewindow_chart[$timewindow]['timewindow_seconds'] , $timewindow_chart[$timewindow]['endtime'],
    
    $who_q['desktop'], $who_q['mobile'], $who_q['tablet'], $who_q['unknown'],
    $who_q['win'], $who_q['apple'], $who_q['unix'], $who_q['other'],
    $who_q['loggedin'], $who_q['loggedout'],
    
    $where_q['originswitch'], $where_q['origin'],
    $where_q['referrersswitch'], $where_q['referrers'],
    $where_q['referredswitch'], $where_q['referred'],
    
);

$grouped_origin_query = $wpdb->get_results(	$wpdb->prepare(	$grouped_origin_query_query , $grouped_origin_query_array ) );

$grouped_link_query_query =
"SELECT
    count( CASE linkbound WHEN 'i' THEN 1 ELSE null END ) as in_link_click_count,
    count( CASE linkbound WHEN 'o' THEN 1 ELSE null END ) as out_link_click_count,
    link
FROM (
    SELECT * 
    FROM wp_bubo_insights_event_log 
    WHERE ( eventtime >= %d AND eventtime < %d AND event = 'c' )
    
    AND ( device IS NULL OR device = %s OR device = %s OR device = %s OR device = %s )
    AND ( os IS NULL OR os = %s OR os = %s OR os = %s OR os = %s )
    AND ( loggedin = %d OR loggedin = %d )
    
    AND ( 1 = %d OR origin " . $where_q['originlike'] . " %s )
    AND ( 1 = %d OR referrer " . $where_q['referrerslike'] . " %s )
    AND ( 1 = %d OR link " . $where_q['referredlike'] . " %s " . $where_link_extra_conditions[$where_q['referredlike']] . ")
    
) as selected_timewindow
WHERE link IS NOT NULL
GROUP BY link
ORDER BY count( CASE linkbound WHEN 'o' THEN 1 ELSE null END ) DESC";

$grouped_link_query_array = array( 
    $timewindow_chart[$timewindow]['endtime'] - $timewindow_chart[$timewindow]['timewindow_seconds'] , $timewindow_chart[$timewindow]['endtime'],
    
    $who_q['desktop'], $who_q['mobile'], $who_q['tablet'], $who_q['unknown'],
    $who_q['win'], $who_q['apple'], $who_q['unix'], $who_q['other'],
    $who_q['loggedin'], $who_q['loggedout'],
    
    $where_q['originswitch'], $where_q['origin'],
    $where_q['referrersswitch'], $where_q['referrers'],
    $where_q['referredswitch'], $where_q['referred'],
    
);

$grouped_link_query = $wpdb->get_results( $wpdb->prepare( $grouped_link_query_query, $grouped_link_query_array ) );


$site_url = sanitize_url( get_site_url() );
$site_domain = str_replace( 'http://' , '' , $site_url  );
$site_host = '%' . $wpdb->esc_like( esc_sql( $site_domain ) ) . '%'; 

$grouped_referrer_query_query =
"SELECT
    count(DISTINCT CONCAT(user, origin)) as referral_count,
    referrer
FROM (
    SELECT * 
    FROM wp_bubo_insights_event_log 
    WHERE ( eventtime >= %d AND eventtime < %d )
    
    AND ( device IS NULL OR device = %s OR device = %s OR device = %s OR device = %s )
    AND ( os IS NULL OR os = %s OR os = %s OR os = %s OR os = %s )
    AND ( loggedin = %d OR loggedin = %d )
    
    AND ( 1 = %d OR origin " . $where_q['originlike'] . " %s )
    AND ( 1 = %d OR referrer " . $where_q['referrerslike'] . " %s )
    AND ( 1 = %d OR link " . $where_q['referredlike'] . " %s " . $where_link_extra_conditions[$where_q['referredlike']] . ")
    
) as selected_timewindow
WHERE referrer NOT LIKE %s
GROUP BY referrer
ORDER BY count(DISTINCT CONCAT(user, origin)) DESC";

$grouped_referrer_query_array = array( 
    $timewindow_chart[$timewindow]['endtime'] - $timewindow_chart[$timewindow]['timewindow_seconds'] , $timewindow_chart[$timewindow]['endtime'],
    
    $who_q['desktop'], $who_q['mobile'], $who_q['tablet'], $who_q['unknown'],
    $who_q['win'], $who_q['apple'], $who_q['unix'], $who_q['other'],
    $who_q['loggedin'], $who_q['loggedout'],
    
    $where_q['originswitch'], $where_q['origin'],
    $where_q['referrersswitch'], $where_q['referrers'],
    $where_q['referredswitch'], $where_q['referred'],
    
    $site_host
);

$grouped_referrer_query = $wpdb->get_results( $wpdb->prepare( $grouped_referrer_query_query, $grouped_referrer_query_array ) );


//setting the timebins as array keys
$pageview_event_timebins = array();
foreach($pageview_event_timebins_query as $pageview_event_timebins_query_row) {
    $pageview_event_timebins[$pageview_event_timebins_query_row->timebin] = $pageview_event_timebins_query_row;
}
$click_event_timebins = array();
foreach($click_event_timebins_query as $click_event_timebins_query_row) {
    $click_event_timebins[$click_event_timebins_query_row->timebin] = $click_event_timebins_query_row;
}

//fetching the maxes
$distinctviews = array( 0 );
$distinctvisits = array( 0 );
$distinctvisitors = array( 0 );

$distinctdesktop = array( 0 );
$distinctmobile = array( 0 );
$distincttablet = array( 0 );
$distinctunknown = array( 0 );
$distinctwin = array( 0 );
$distinctapple = array( 0 );
$distinctunix = array( 0 );
$distinctother = array( 0 );
$distinctloggedin = array( 0 );
$distinctloggedout = array( 0 );

$originviews = array( 0 );
$originvisits = array( 0 );
$originvisitors = array( 0 );
$origindesktop = array( 0 );
$originmobile = array( 0 );
$origintablet = array( 0 );
$originunknown = array( 0 );

$inlinkclicks = array( 0 );
$outlinkclicks = array( 0 );
$referrals = array( 0 );
$referrers = array();
$p = 0;
foreach($pageview_event_timebins_query as $pageview_event_timebin) {
    $distinctviews[$p] = $pageview_event_timebin->distinct_view_count;
    $distinctvisits[$p] = $pageview_event_timebin->distinct_visit_count;
    $distinctvisitors[$p] = $pageview_event_timebin->distinct_visitor_count;
    $distinctdesktop[$p] = $pageview_event_timebin->distinct_desktop_count;
    $distinctmobile[$p] = $pageview_event_timebin->distinct_mobile_count;
    $distincttablet[$p] = $pageview_event_timebin->distinct_tablet_count;
    $distinctunknown[$p] = $pageview_event_timebin->distinct_unknown_count;
    $distinctwin[$p] = $pageview_event_timebin->distinct_win_count;
    $distinctapple[$p] = $pageview_event_timebin->distinct_apple_count;
    $distinctunix[$p] = $pageview_event_timebin->distinct_unix_count;
    $distinctother[$p] = $pageview_event_timebin->distinct_other_count;
    $distinctloggedin[$p] = $pageview_event_timebin->distinct_loggedin_count;
    $distinctloggedout[$p] = $pageview_event_timebin->distinct_loggedout_count;
    $p++;
}
$o = 0;
foreach($grouped_origin_query as $grouped_origin) {
    $originviews[$o] = $grouped_origin->origin_view_count;
    $originvisits[$o] = $grouped_origin->origin_visit_count;
    $originvisitors[$o] = $grouped_origin->origin_visitor_count;
    $origindesktop[$o] = $grouped_origin->origin_desktop_count;
    $originmobile[$o] = $grouped_origin->origin_mobile_count;
    $origintablet[$o] = $grouped_origin->origin_tablet_count;
    $originunknown[$o] = $grouped_origin->origin_unknown_count;
    $o++;
}
$l = 0;
foreach($grouped_link_query as $grouped_link) {
    $inlinkclicks[$l] = $grouped_link->in_link_click_count;
    $outlinkclicks[$l] = $grouped_link->out_link_click_count;
    $l++;
}
$r = 0;
foreach($grouped_referrer_query as $grouped_referrer) {
    $referrals[$r] = $grouped_referrer->referral_count;
    if($grouped_referrer->referrer != 'direct') $referrers[$r] = $grouped_referrer->referrer;
    $r++;
}

$time = $timewindow_chart[$timewindow]['endtime'];
$reference_timebin = ceil( $time / $timewindow_chart[$timewindow]['timebin_seconds'] );

$timebins = array();
for($i = 0; $i < $timewindow_chart[$timewindow]['timebin_count']; $i++) {
    $timebins[$i] = $reference_timebin - $i;
}


    $i=0;
    $visitorbars    = array( 0 );
    $viewbars       = array( 0 );
    $visitbars      = array( 0 );
    $clickbars      = array( 0 );
    $iclickbars     = array( 0 );
    $eclickbars     = array( 0 );
    foreach($timebins as $timebin) {
        $visitor_count = $pageview_event_timebins[$timebin]->distinct_visitor_count;
        if(empty($visitor_count)) $visitor_count = 0;
		$visitorbars[$i] = $visitor_count;
		
		$view_count = $pageview_event_timebins[$timebin]->distinct_view_count;
		if(empty($view_count)) $view_count = 0;
		$viewbars[$i] = $view_count;
		
		$visit_count = $pageview_event_timebins[$timebin]->distinct_visit_count;
		if(empty($visit_count)) $visit_count = 0;
		$visitbars[$i] = $visit_count;
		
		$click_count = $click_event_timebins[$timebin]->click_count;
		if(empty($click_count)) $click_count = 0;
		$clickbars[$i] = $click_count;
		
		$iclick_count = $click_event_timebins[$timebin]->iclick_count;
		if(empty($iclick_count)) $iclick_count = 0;
		$iclickbars[$i] = $iclick_count;
		
		$eclick_count = $click_event_timebins[$timebin]->eclick_count;
		if(empty($eclick_count)) $eclick_count = 0;
		$eclickbars[$i] = $eclick_count;
		
		$i++;
		
    }
    

ob_start();
    var_dump($where_q['referrerslike']);
$response['dump']           = ob_get_clean();

$response['visitorbars']    = $visitorbars;
$response['viewbars']       = $viewbars;
$response['visitbars']      = $visitbars;
$response['eclickbars']     = $eclickbars;
$response['iclickbars']     = $iclickbars;

$response['viewbarmax']         = max($viewbars);
$response['visitbarmax']        = max($visitbars);
$response['visitorbarmax']      = max($visitorbars);
$response['eclickbarmax']       = max($eclickbars);
$response['iclickbarmax']       = max($iclickbars);

$response['max']            = max( max($viewbars) ,  max($clickbars) );

$response['views']          = array_sum($distinctviews);
$response['visits']         = array_sum($distinctvisits);
$response['visitors']       = array_sum($distinctvisitors);

$response['devicesmax']     = max( array_sum($distinctdesktop) , array_sum($distinctmobile) , array_sum($distincttablet) , array_sum($distinctunknown) );
$response['osmax']          = max( array_sum($distinctwin) , array_sum($distinctapple) , array_sum($distinctunix) , array_sum($distinctother) );
$response['logstatusmax']   = max( array_sum($distinctloggedin) , array_sum($distinctloggedout) );

$response['devices']     = array_sum( array( array_sum($distinctdesktop) , array_sum($distinctmobile) , array_sum($distincttablet) , array_sum($distinctunknown) ) );
$response['os']          = array_sum( array( array_sum($distinctwin) , array_sum($distinctapple) , array_sum($distinctunix) , array_sum($distinctother) ) );
$response['logstatus']   = array_sum( array( array_sum($distinctloggedin) , array_sum($distinctloggedout) ) );

$response['desktop']        = array_sum($distinctdesktop);
$response['mobile']         = array_sum($distinctmobile);
$response['tablet']         = array_sum($distincttablet);
$response['unknown']        = array_sum($distinctunknown);
$response['win']            = array_sum($distinctwin);
$response['apple']          = array_sum($distinctapple);
$response['unix']           = array_sum($distinctunix);
$response['other']          = array_sum($distinctother);
$response['loggedin']       = array_sum($distinctloggedin);
$response['loggedout']      = array_sum($distinctloggedout);

$response['iclicks']        = array_sum($inlinkclicks);
$response['eclicks']        = array_sum($outlinkclicks);
$response['referrers']      = count($referrers);

$response['originviewbox']      = $grouped_origin_query;
$response['originviewmax']      = max($originviews);
$response['originvisitmax']     = max($originvisits);
$response['originvisitormax']   = max($originvisitors);

$response['linkviewbox']        = $grouped_link_query;
$response['inlinkclickmax']     = max($inlinkclicks);
$response['outlinkclickmax']    = max($outlinkclicks);

$response['referrerviewbox']    = $grouped_referrer_query;
$response['referrals']          = array_sum($referrals);
$response['referralmax']        = max($referrals);

$response['performance']    = microtime() - $starttime;

echo wp_json_encode( $response );

?>