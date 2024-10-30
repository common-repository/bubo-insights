<?php

// bubo insights tracking engine - adding the tracking nonce to all the website page
function bubo_insights_tracking_nonce() {
	echo '<noscript id="bubo_insights_nonce" data-nonce="' . esc_attr( wp_create_nonce( 'bubo_insights_tracking' ) ) . '"></noscript>';
}
add_action( 'wp_body_open', 'bubo_insights_tracking_nonce' );

// bubo insights tracking engine - original hashing method chr_hash93 
// translates a low collision sha1 hexadecimal hash into a 10 character long hash code with 93 ascii characters (33to125), replaces " and ' and , and & and < and > with ~ (126) for compatibility and peace of mind
function bubo_insights_chrhash93($input) {
    $chr_hash = '';
    $hash = sha1($input);
    for($i=0;$i<10;$i++) {
        $slice = intval(hexdec(substr($hash, $i*2 , 4)));
        $chr_hash .= chr(round($slice / 704.688)+32);
    }
    $chr_hash = str_replace('"', '~', $chr_hash);
    $chr_hash = str_replace("'", "~", $chr_hash);
    $chr_hash = str_replace(",", "~", $chr_hash);
	$chr_hash = str_replace('&', '~', $chr_hash);
    $chr_hash = str_replace("<", "~", $chr_hash);
    $chr_hash = str_replace(">", "~", $chr_hash);
    return $chr_hash;
}

// bubo insights tracking engine - event logging AJAX
add_action('wp_ajax_bubo_insights_event_log', 'bubo_insights_loggedin_event_log_callback');
add_action('wp_ajax_nopriv_bubo_insights_event_log', 'bubo_insights_loggedout_event_log_callback');

function bubo_insights_loggedin_event_log_callback()  {
	bubo_insights_event_log_callback(1);
}
function bubo_insights_loggedout_event_log_callback() {
	bubo_insights_event_log_callback(0);
}

function bubo_insights_event_log_callback($loggedin)  {
	
//	check_ajax_referer( 'bubo_insights_tracking' , 'nonce' );

	$log_status 				= intval( sanitize_text_field($loggedin) );
	if( isset($_SERVER['REMOTE_ADDR']) ) $remote_addr = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
	if( isset($_SERVER["HTTP_USER_AGENT"]) ) $http_user_agent = sanitize_text_field( wp_unslash( $_SERVER["HTTP_USER_AGENT"] ) );
	$user                       = md5($remote_addr.$http_user_agent);
	
	if( isset($_REQUEST['sessiontime']) ) $session_time				= sanitize_text_field( wp_unslash( $_REQUEST['sessiontime'] ) );
	if( isset($_REQUEST['pagesession']) ) $pagesession				= sanitize_text_field( wp_unslash( $_REQUEST['pagesession'] ) );
	
	$event              	= array();
	$event['user']      	= $user;
	$event['loggedin']		= $log_status;
	$event['eventtime']     = time();
	if( isset($_REQUEST['eventtype']) ) 		$event['event']             = substr( sanitize_text_field( wp_unslash( $_REQUEST['eventtype'] ) ), 0, 1);
	if( isset($_REQUEST['referrer']) ) 			$event['referrer']          = sanitize_text_field( wp_unslash( $_REQUEST['referrer'] ) );
	if(empty($event['referrer'])) $event['referrer'] = 'direct';
	if( isset($_REQUEST['origin']) ) 			$event['origin']            = sanitize_text_field( wp_unslash( $_REQUEST['origin'] ) );
	if( isset($_REQUEST['elementcontent']) ) 	$event['elementcontent']    = sanitize_text_field( wp_unslash( $_REQUEST['elementcontent'] ) );
	if( isset($_REQUEST['elementtag']) ) 		$event['elementtag']        = sanitize_text_field( wp_unslash( $_REQUEST['elementtag'] ) );
	if( isset($_REQUEST['elementclass']) ) 		$event['elementclass']      = sanitize_text_field( wp_unslash( $_REQUEST['elementclass'] ) );
	if( isset($_REQUEST['link']) ) {
	    $event['link']                  = sanitize_url( wp_unslash( $_REQUEST['link'] ) );
	    if( $event['event'] == 'c' ) {
    	    $this_site_domain_host          = parse_url( get_site_url(), PHP_URL_HOST );
    	    if( str_contains( $event['link'] , $this_site_domain_host  ) ) {
    	        $event['linkbound']         = 'i';
    	    }
    	    else {
    	        $event['linkbound']         = 'o';
    	    }
	    }
	}
	
	$visitor                = array();
	$visitor['user']        = $user;
	$visitor['loggedin']	= $log_status;
	if( isset($_REQUEST['scale']) )				$visitor['scale']           = intval( sanitize_text_field( wp_unslash( $_REQUEST['scale'] ) ) );
	if( isset($_REQUEST['screenwidth']) )		$visitor['screenwidth']     = intval( sanitize_text_field( wp_unslash( $_REQUEST['screenwidth'] ) ) );
	if( isset($_REQUEST['screenheight']) )		$visitor['screenheight']    = intval( sanitize_text_field( wp_unslash( $_REQUEST['screenheight'] ) ) );
	if( isset($_REQUEST['touchenabled']) )		$visitor['touchenabled']	= intval( sanitize_text_field( wp_unslash( $_REQUEST['touchenabled'] ) ) );
	if($visitor['touchenabled'] == 'true') { $visitor['touchenabled'] = 1; } else { $visitor['touchenabled'] = 0; }
	
	if( isset($_SERVER["HTTP_USER_AGENT"]) ) $os_ua = sanitize_text_field( wp_unslash( $_SERVER["HTTP_USER_AGENT"] ) );
	$open = strpos($os_ua, "(");
	$close = strpos($os_ua, ")");
	$ua_os = substr($os_ua, $open + 1, $close - $open - 1);
	
	if( isset($_SERVER["HTTP_SEC_CH_UA"]) ) $browser_ua = sanitize_text_field( wp_unslash( $_SERVER["HTTP_SEC_CH_UA"] ) );
	$browser = str_replace( '\"', '"', $browser_ua );
	$browser = str_replace( ';v=', '' , $browser );
	$browser = str_replace( '"', '' , $browser );
	$browser = str_replace( ',', ';' , $browser );
	$ua_browser = preg_replace('/[0-9]+/', '', $browser);
	
	if( empty( $ua_browser ) ) {
	    $browser_ua = strtolower( sanitize_text_field( wp_unslash( $_SERVER["HTTP_USER_AGENT"] ) ) );
	    if( str_contains($browser_ua, "firefox") ) {
	        $ua_browser = 'Firefox';
	    }
	    else if( str_contains($browser_ua, "edg") ) {
	        $ua_browser = 'Edge';
	    }
	    else if( str_contains($browser_ua, "samsung") ) {
	        $ua_browser = 'Samsung';
	    }
	    else if( str_contains($browser_ua, "safari") AND !str_contains($browser_ua, "chrom") ) {
	        $ua_browser = 'Safari';
	    }
	    else if( str_contains($browser_ua, "chrom") ) {
	        $ua_browser = 'Chrome';
	    }
	    else {
	        $ua_browser = 'Unknown';
	    }
	}
	
	if( isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]) ) $lang_ua = sanitize_text_field( wp_unslash( $_SERVER["HTTP_ACCEPT_LANGUAGE"] ) );
	$lang = preg_replace('/[0-9]+/', '', $lang_ua);
	$lang = str_replace( 'q=.', '' , $lang );
	$lang = str_replace( ';,', ',' , $lang );
	$lang = str_replace( ';', '' , $lang );
	$ua_lang = str_replace( ',', '; ' , $lang );
	
	$ua_mobile = 0;
	if( isset($_SERVER["HTTP_SEC_CH_UA_MOBILE"]) ) {
	    $ua_mobile = intval( sanitize_text_field( wp_unslash( $_SERVER["HTTP_SEC_CH_UA_MOBILE"] ) ) );
	}

	$visitor['ua_os']               = $ua_os;
	$visitor['ua_browser']          = $ua_browser;
	$visitor['ua_lang']             = $ua_lang;
	$visitor['ua_mobilerequest']    = $ua_mobile;
	
	$os_haystack = strtolower($ua_os);
	if( str_contains($os_haystack, "ipad")
		OR str_contains($os_haystack, "iphone")  
		OR str_contains($os_haystack, "mac") 
	) {
		$os = 'a';
	}
	elseif ( str_contains($os_haystack, "win") 
	) {
		$os = 'w';
	}
	elseif ( str_contains($os_haystack, "android")
			 OR str_contains($os_haystack, "linux")
			 OR str_contains($os_haystack, "cros")
	) {
		$os = 'u';
	}
	else {
		$os = '?';
	}
	$visitor['os']              = $os;
	$event['os']                = $os;
	
	$device_haystack = strtolower($ua_os);
	if( str_contains($os_haystack, "win")
		OR ( str_contains($os_haystack, "mac") AND ! str_contains($os_haystack, "iphone") AND ! str_contains($os_haystack, "ipad") )
		OR str_contains($os_haystack, "cros")
		OR ( str_contains($os_haystack, "linux") AND ! str_contains($os_haystack, "android") )
		OR $visitor['touchenabled'] == 'false'
	) {
		$device = 'd';
		$device_ext = 'DESK';
	}
	elseif ( str_contains($os_haystack, "ipad")
			 OR ( str_contains($os_haystack, "android") AND ($ua_mobile = 0 OR $visitor['scale'] < 2) )
			 OR ( $visitor['screenwidth'] > $visitor['screenheight'] )
	) {
		$device = 't';
		$device_ext = 'TABL';
	}
	elseif ( str_contains($os_haystack, "iphone")
			 OR str_contains($os_haystack, "android")
			 OR str_contains($os_haystack, "windows phone")
	) {
		$device = 'm';
		$device_ext = 'MOBI';
	}
	else {
		$device = '?';
		$device_ext = 'UNKN';
	}
	$visitor['device']          = $device;
	$event['device']            = $device;
	
	$logged_status              = array(' ', '~');
	$user_hash                  = strtoupper(substr($ua_lang, 0, 2)) . strtolower(substr($ua_os, 0, 3))  . $device_ext . $logged_status[$loggedin] . bubo_insights_chrhash93($user);
	$event['user']              = $user_hash;
	$visitor['user']            = $user_hash;
	
	$event['pagesession']       = $pagesession;

	$event['sessionduration']   = $session_time;
	
	//normal tracking
    global $wpdb;    
    $pagesession_id = $wpdb->get_results( //db call ok; no-cache ok
        $wpdb->prepare(
            "SELECT id
            FROM wp_bubo_insights_event_log
            WHERE event = %s
            AND pagesession = %s
            AND user = %s " ,
            array( 
                'p',
                $event['pagesession'],
                $user_hash
            ) 
        )
    );
    
    if( empty($pagesession_id[0]->id) ) {
        
        if( $event['event'] == 'p' ) {
            bubo_insights_eventlog_table_insert_record($event);
            bubo_insights_visitorslog_table_insert_record($visitor);
        }
        
    } else {
        
        $update = $wpdb->update(
            'wp_bubo_insights_event_log',
            array( 'sessionduration' => $session_time	),
            array( 'id' => intval($pagesession_id[0]->id) ),
            array( '%s'	)
        );
        
    }
    
    if( $event['event'] == 'c'AND ! empty($event['link']) ) {
        
        bubo_insights_eventlog_table_insert_record($event);
        bubo_insights_visitorslog_table_insert_record($visitor);
        
    }

    ob_start();
//    var_dump($already_visited_checker[0]);
	$response = ob_get_clean(); 		
	
	echo wp_json_encode( esc_html( $response ) );

	die();
}

// bubo insights tracking engine - new event logging
function bubo_insights_eventlog_table_insert_record($event_record) {
	
    global $wpdb;

    $table_name = 'wp_bubo_insights_event_log';
    
    $columns = array(
        'event',
        'pagesession',
        'sessionduration',
        'eventtime',
        'user',
        'loggedin',
        'device',
        'os',
        'referrer',
        'origin',
        'elementcontent',
        'elementtag',
        'elementclass',
        'link',
        'linkbound'
    );
    
    $insert_array = array();
    foreach($columns as $column) {
       if(!empty($event_record[$column] OR $event_record[$column] == 0)) {
           $insert_array[$column] = $event_record[$column];
       } 
    }

    $wpdb->insert(
        $table_name,
        $insert_array
    );
}

// bubo insights tracking engine - event log table in wp database
function bubo_insights_eventlog_table() {
    global $wpdb;

    $table_name = 'wp_bubo_insights_event_log';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        event char(1),
        pagesession varchar(4),
        sessionduration DECIMAL(9,2),
        eventtime int(11) unsigned,
        device varchar(10),
        os varchar(10),
        user tinytext,
        loggedin tinyint(4),
        referrer text,
        origin text,
        elementcontent text,
        elementtag tinytext,
        elementclass tinytext,
        link text,
        linkbound char(1),
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

// bubo insights tracking engine - new visitors logging
function bubo_insights_visitorslog_table_insert_record($visitor_record) {
    global $wpdb;

    $table_name = 'wp_bubo_insights_visitors_log';
    
    $columns = array(
        'user',
        'loggedin',
        'device',
        'os',
        'scale',
        'screenwidth',
        'screenheight',
        'touchenabled',
        'ua_os',
        'ua_browser',
        'ua_lang',
        'ua_mobilerequest'
    );
    
    $insert_array = array();
    foreach($columns as $column) {
       if(!empty($visitor_record[$column] OR $visitor_record[$column] == 0)) {
           $insert_array[$column] = $visitor_record[$column];
       } 
    }

    $wpdb->insert(
        $table_name,
        $insert_array
    );
}

// bubo insights tracking engine - visitors log table in wp database
function bubo_insights_visitorslog_table() {
    global $wpdb;

    $table_name = 'wp_bubo_insights_visitors_log';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user tinytext UNIQUE,
        loggedin tinyint(4),
        device varchar(10),
        os varchar(10),
        scale int(2),
        screenwidth int(5),
        screenheight int(5),
        touchenabled int(2),
        ua_os tinytext,
        ua_browser tinytext,
        ua_lang tinytext,
        ua_mobilerequest tinyint(4),
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

// bubo insights tracking engine - register all tables
function bubo_insights_register_db_tables() {
    bubo_insights_eventlog_table();
    bubo_insights_visitorslog_table();
    
    bubo_insights_drop_all_deprecated_tables();
}

// bubo insights tracking engine - drop all tables
function bubo_insights_drop_all_tables() {
    global $wpdb;

    $wpdb->query( "DROP TABLE IF EXISTS `wp_bubo_insights_event_log`" );
    $wpdb->query( "DROP TABLE IF EXISTS `wp_bubo_insights_visitors_log`" );   
}

// bubo insights tracking engine - drop all DEPRECATED tables
function bubo_insights_drop_all_deprecated_tables() {
    global $wpdb;
    $wpdb->query( "DROP TABLE IF EXISTS `wp_bubo_insights_digested_log`" );
}

?>