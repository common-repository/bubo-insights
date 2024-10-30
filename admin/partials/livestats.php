<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php
$bubo_insights_page = "livestats";
$timezone_delta_hours = ( time() - current_time( 'timestamp' ) ) / ( 60*60 );
$userid = get_current_user_id();

$defaults = (object) array(
    'inactivemetrics' => array( 'views', 'visitors' ),
     'who' => array( 'desktop' , 'mobile', 'tablet', 'unknown', 'win', 'apple', 'unix', 'other', 'loggedout' ),
     'whotab' => array( 'devices' ),
     'when' => array( 'tday' ),
     'wherepage' => array( 'islike', '' ),
     'wherefrom' => array( 'islike', '' ),
     'wheregoto' => array( 'islike', '' ),
     'wheretab' => array( 'page')
);
$saved_defaults = get_user_meta( $userid, 'wp_bubo_insights_livestats_defaults', true );
if(!empty($saved_defaults)) {
    $saved_defaults_decoded = json_decode( $saved_defaults );
    if($saved_defaults_decoded->multibarsorder)     $defaults->multibarsorder = $saved_defaults_decoded->multibarsorder;
    if($saved_defaults_decoded->inactivemetrics)    $defaults->inactivemetrics = $saved_defaults_decoded->inactivemetrics;
    if($saved_defaults_decoded->who)                $defaults->who = $saved_defaults_decoded->who;
    if($saved_defaults_decoded->whotab)             $defaults->whotab = $saved_defaults_decoded->whotab;
    if($saved_defaults_decoded->when)               $defaults->when = $saved_defaults_decoded->when;
    if($saved_defaults_decoded->wherepage)          $defaults->wherepage = $saved_defaults_decoded->wherepage;
    if($saved_defaults_decoded->wherefrom)          $defaults->wherefrom = $saved_defaults_decoded->wherefrom;
    if($saved_defaults_decoded->wheregoto)          $defaults->wheregoto = $saved_defaults_decoded->wheregoto;
    if($saved_defaults_decoded->wheretab)           $defaults->wheretab = $saved_defaults_decoded->wheretab;
}

if( is_array( $defaults->multibarsorder ) )     $multibarsorder     = implode( ',' , $defaults->multibarsorder );
if( is_array( $defaults->inactivemetrics ) )    $inactivemetrics    = implode( ',' , $defaults->inactivemetrics );
if( is_array( $defaults->who ) )                $who                = implode( ',' , $defaults->who );
if( is_array( $defaults->whotab ) )             $whotab             = implode( ',' , $defaults->whotab );
if( is_array( $defaults->when ) )               $when               = implode( ',' , $defaults->when );
if( is_array( $defaults->wherepage ) )          $wherepage          = implode( ',' , $defaults->wherepage );
if( is_array( $defaults->wherefrom ) )          $wherefrom          = implode( ',' , $defaults->wherefrom );
if( is_array( $defaults->wheregoto ) )          $wheregoto          = implode( ',' , $defaults->wheregoto );
if( is_array( $defaults->wheretab ) )           $wheretab           = implode( ',' , $defaults->wheretab );

?>

<main
    id="stats"
    class="bubo_insights_admin_page"
    data-ajax="<?php echo esc_url( get_site_url() ); ?>/wp-admin/admin-ajax.php"
    data-nonce="<?php echo esc_attr( wp_create_nonce( 'bubo_insights_stats' ) ); ?>"
    data-timezone="<?php echo esc_attr( $timezone_delta_hours ); ?>"
    data-userid="<?php echo esc_attr( $userid ); ?>"
    
    data-multibarsorder="<?php echo esc_attr( $multibarsorder ); ?>"
    data-inactivemetrics="<?php echo esc_attr( $inactivemetrics ); ?>"
    data-who="<?php echo esc_attr( $who ); ?>"
    data-whotab="<?php echo esc_attr( $whotab ); ?>"
    data-when="<?php echo esc_attr( $when ); ?>"
    data-wherepage="<?php echo esc_attr( $wherepage ); ?>"
    data-wherefrom="<?php echo esc_attr( $wherefrom ); ?>"
    data-wheregoto="<?php echo esc_attr( $wheregoto ); ?>"
    data-wheretab="<?php echo esc_attr( $wheretab ); ?>"
>
    
    <div id="header">
        
        <nav id="bubo_insights_navbar">
            <?php do_action('bubo_insights_navbar', $bubo_insights_page ); ?>
        </nav>
		    
		<h1>Live Stats</h1>
		
	</div>
    
    <section>
        
        <details open>
            
            <summary class="filters_display"><big><strong>Filter data by selecting:</strong></big></summary>
            
            <br>
            
            <div class="inputs" >
                
                <?php
                $who_tag_panels = array(
                    'device'        =>  array('label' => 'Devices',        'hidden' => ''),
                    'os'            =>  array('label' => 'OS',             'hidden' => 'hidden'),
                    'logstatus'     =>  array('label' => 'Log Status',     'hidden' => 'hidden')
    			);
    			$who_tags = array(
                    'device'        =>  array(
                        'desktop'       => array('checked' => in_array('desktop', $defaults->who),        'name' => 'desktop',    'label' => 'Desktop'),
                        'tablet'        => array('checked' => in_array('tablet', $defaults->who),         'name' => 'tablet',     'label' => 'Tablet'),
                        'mobile'        => array('checked' => in_array('mobile', $defaults->who),         'name' => 'mobile',     'label' => 'Mobile'),
                        'unknown'       => array('checked' => in_array('unknown', $defaults->who),        'name' => 'unknown',    'label' => 'Unknown'),
                    ),
    				'os'            =>  array(
                        'apple'         => array('checked' => in_array('apple', $defaults->who),          'name' => 'apple',      'label' => 'Apple'),
                        'win'           => array('checked' => in_array('win', $defaults->who),            'name' => 'win',        'label' => 'Windows'),
                        'unix'          => array('checked' => in_array('unix', $defaults->who),           'name' => 'unix',       'label' => 'Linux'),
                        'other'         => array('checked' => in_array('other', $defaults->who),          'name' => 'other',      'label' => 'Other'),
    				),
    				'logstatus'    =>  array(
                        'loggedin'      => array('checked' => in_array('loggedin', $defaults->who),       'name' => 'loggedin',   'label' => 'Logged in'),
                        'loggedout'     => array('checked' => in_array('loggedout', $defaults->who),      'name' => 'loggedout',  'label' => 'Logged out'),
                    )
    			);
    			?>
                <div id="whofilter" class="filterpanel" >
    				
                    <ul class="filtertab" >
                        <?php foreach(array_keys($who_tags) as $who_tag_name) : ?>
                            <li>
                                <a id="<?php echo esc_attr( $who_tag_name ); ?>_tab" data-tab="<?php echo esc_attr( $who_tag_name ); ?>" href="#<?php echo esc_attr( $who_tag_name ); ?>"  >
                                    <?php echo esc_html( $who_tag_panels[$who_tag_name]['label'] ); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
    				<?php foreach(array_keys($who_tags) as $who_tag_name) : ?>
                        
                        <div id="<?php echo esc_attr( $who_tag_name ); ?>" >
                            
                            <?php foreach($who_tags[$who_tag_name] as $who_tag_element) : ?> 
                                
                                <label for="<?php echo esc_attr( $who_tag_element['name'] ); ?>">
                                    <?php echo esc_attr( $who_tag_element['label'] ); ?>
                                    <label
                                        class="switch"
                                        for="<?php echo esc_attr( $who_tag_element['name'] ); ?>"
                                    >
                                    <input
                                        id="<?php echo esc_attr( $who_tag_element['name'] ); ?>"
                                        type="checkbox"
                                        <?php if($who_tag_element['checked']===true) echo 'checked'; ?>
                                    >
                                    <span class="slider"></span>
                                    </label>
                                </label>
                                
                            <?php endforeach; ?>
                            
                        </div>
                        
    				<?php endforeach; ?>
    				
                </div>
                
                
                <?php
                $when_mode_panels = array(
                    'hourly'    =>  array(  'class' => 'referrers',         'label' => 'Coming from',   'hidden' => 'hidden'),
                    'daily'     =>  array(  'class' => 'session',           'label' => 'Page',          'hidden' => ''),
                    'weekly'    =>  array(  'class' => 'externalclicks',    'label' => 'Going to',      'hidden' => 'hidden'),
                    'monthly'   =>  array(  'class' => 'referrers',         'label' => 'Coming from',   'hidden' => 'hidden'),
                    'yearly'    =>  array(  'class' => 'session',           'label' => 'Page',          'hidden' => ''),
                    'alltime'   =>  array(  'class' => 'externalclicks',    'label' => 'Going to',      'hidden' => 'hidden')
    			);
    			$when_mode_inputs = array(
    				'from'  =>  array( '' ),
                    'page'  =>  array( '' ),
    				'goto'  =>  array( '' )
    			);
    			?>
                
                <div id="whenfilter" class="filterpanel" >
                    <ul class="filtertab" >
                        <li><a href="#hourly"  >Hourly</a></li>
                        <li><a href="#daily"   >Daily</a></li>
                        <li><a href="#weekly"  >Weekly</a></li>
                        <li><a href="#monthly" >Monthly</a></li>
                        <li><a href="#yearly"  >Yearly</a></li>
                        <li><a href="#alltime" >All time</a></li>
                    </ul>
                    
                    <div id="hourly">
                        <button id="get_hour"       class="time_button"     value="hour"    data-tab="hourly"   >Last 60 min</button>
                    </div>
                    <div id="daily">
                        <button id="get_pday"       class="time_button"     value="pday"    data-tab="daily"    >Yesterday</button>
                        <button id="get_day"        class="time_button"     value="day"     data-tab="daily"    >Last 24 h</button>
                        <button id="get_tday"       class="time_button"     value="tday"    data-tab="daily"    >Today</button>
                    </div>
                    <div id="weekly">
                        <button id="get_pweek"      class="time_button"     value="pweek"   data-tab="weekly"   >Last week</button>
                        <button id="get_week"       class="time_button"     value="week"    data-tab="weekly"   >Last 7 days</button>
                        <button id="get_tweek"      class="time_button"     value="tweek"   data-tab="weekly"   >This week</button>
                    </div>
                    <div id="monthly">
                        <button id="get_pmonth"     class="time_button"     value="pmonth"  data-tab="monthly"  >Last month</button>
                        <button id="get_month"      class="time_button"     value="month"   data-tab="monthly"  >Last 30 days</button>
                        <button id="get_tmonth"     class="time_button"     value="tmonth"  data-tab="monthly"  >This month</button>
                    </div>
                    <div id="yearly">
                        <button id="get_pyear"      class="time_button"     value="pyear"   data-tab="yearly"   >Last year</button>
                        <button id="get_year"       class="time_button"     value="year"    data-tab="yearly"   >Last 365 days</button>
                        <button id="get_tyear"      class="time_button"     value="tyear"   data-tab="yearly"   >This year</button>
                    </div>
                    <div id="alltime">
                        <button id="get_all"        class="time_button"     value="all"     data-tab="alltime"  >Since Recording</button>
                    </div>
                </div>
                
                <?php
                $where_input_panels = array(
                    'referrers' =>  array(  'class' => 'referrers',         'label' => 'Referrer',      'hidden' => 'hidden'            ),
                    'origin'    =>  array(  'class' => 'visits',            'label' => 'Page',          'hidden' => ''                  ),
                    'referred'  =>  array(  'class' => 'clicks',            'label' => 'Referred link', 'hidden' => 'hidden'            )
    			);
    			$where_inputs = array(
    				'referrers' =>  array( '' ),
                    'origin'    =>  array( '' ),
    				'referred'  =>  array( '' )
    			);
    			?>
                <div id="wherefilter" class="filterpanel" >
    				
                    <ul class="filtertab" >
                        <?php foreach(array_keys($where_inputs) as $where_input_name) : ?>
                            <li class="<?php echo esc_html( $where_input_panels[$where_input_name]['class'] ); ?>">
                                <a id="<?php echo esc_attr( $where_input_name ); ?>_tab" data-tab="<?php echo esc_attr( $where_input_name ); ?>" href="#<?php echo esc_attr( $where_input_name ); ?>"  >
                                    <?php echo esc_html( $where_input_panels[$where_input_name]['label'] ); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
    				<?php foreach(array_keys($where_inputs) as $where_input_name) : ?>
                        
                        <div id="<?php echo esc_attr( $where_input_name ); ?>" class="<?php echo esc_html( $where_input_panels[$where_input_name]['class'] ); ?>" >
                            
                            <?php foreach($where_inputs[$where_input_name] as $where_input_element) : ?> 
                                
                                <select id="<?php echo esc_attr( $where_input_name ); ?>_like" >
                                    <option value="islike"      >Contains:</option>
                                    <option value="notlike"     >Not contains:</option>
                                    <option value="isequal"     >Exactly this:</option>
                                    <option value="notequal"    >Exclude this:</option>
    							</select>
    							<input id="<?php echo esc_attr( $where_input_name ); ?>_filter" class="wherefilterinput_<?php echo esc_html( $where_input_name ); ?>" type="text" placeholder="e.g. https://www..." >
    							<button id="<?php echo esc_attr( $where_input_name ); ?>_clear"  value="x" class="wherefilterclear_<?php echo esc_html( $where_input_name ); ?> <?php echo esc_html( $where_input_panels[$where_input_name]['class'] ); ?>" >x</button>
    							<button id="<?php echo esc_attr( $where_input_name ); ?>_go"  value="Add Filter" class="wherefilterbutton wherefilterbutton_<?php echo esc_html( $where_input_name ); ?> <?php echo esc_html( $where_input_panels[$where_input_name]['class'] ); ?>" >Add Filter</button>
                                
                            <?php endforeach; ?>
                            
                        </div>
                        
    				<?php endforeach; ?>
    				
                </div>
                
            </div>
            
        </details>
        
        <br>
        
        <?php 
        $metrics = array(
            'views'     => array( 'label' => 'Total views' ),
            'visits'    => array( 'label' => 'Unique pageviews' ),
            'visitors'  => array( 'label' => 'Visitors' ),
//            'iclicks'   => array( 'label' => 'in.Clicks' ),
            'eclicks'   => array( 'label' => 'out.Clicks' )
        );
        ?>
        
        <div class="statslegend">
            <?php foreach( array_keys($metrics) as $metric ) : ?>
                <div class="total <?php echo esc_attr( $metric ); ?>">
                    <span><span class="totalcheck">✓</span> <?php echo esc_attr( $metrics[$metric]['label'] ); ?></span>
                    <span class="totalcount <?php echo esc_attr( $metric ); ?>">0</span>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div id="plot">
            
            <span id="loading" >LOADING...<img class="rotating" width="45" height="45" src="<?php echo esc_url( plugins_url('/assets/stopwatch_loading.svg', __FILE__) ); ?>" ></span>
            
            <div id="x-axis"></div>
            <div id="y-axis">
                <div class="y-unit 0" ><span></span></div>
                <div class="y-unit 1" ><span></span></div>
                <div class="y-unit 2" ><span></span></div>
                <div class="y-unit 3" ><span></span></div>
                <div class="y-unit 4" ><span></span></div>
            </div>
            
            <div id="eclickbars" STYLE="DISPLAY:NONE;"></div>
            <div id="iclickbars" STYLE="DISPLAY:NONE;"></div>
            <div id="viewbars" STYLE="DISPLAY:NONE;"></div>
            <div id="visitbars" STYLE="DISPLAY:NONE;"></div>
            <div id="visitorbars" STYLE="DISPLAY:NONE;"></div>
            
            <div id="polylines"></div>
            
            <div id="viewcounters" class="counters"></div>
            <div id="visitcounters" class="counters"></div>
            <div id="visitorcounters" class="counters"></div>
            <div id="eclickcounters" class="counters"></div>
            <div id="iclickcounters" class="counters" STYLE="DISPLAY:NONE;"></div>
            

		</div>
		
		<div class="multimultimultibar" >
            
            <?php   
            $metrics = array(
                
                'visits' => array(
                    
                    'label' => 'Visits',
                    'tabs' => array( 
                        'Unique Pageviews' => 'visits' ,
                        'Total Views' => 'views'
                    )
                    
                ),
                
                'clicks' => array(
                    
                    'label' => 'Clicks',
                    'tabs' => array( 
                        'Outbound clicks' => 'external_clicks'
                    )
                    
                ),
                
                'referrers' => array(
                    
                    'label' => 'Referrers', 
                    'tabs' => array(
                        'Referrals' => 'referrals'
                    )
                    
                ),
                
                'visitors' => array(
                    
                    'label' => 'Visitors',
                    'tabs' => array(
                        'Devices' => 'devices' ,
                        'Operative Systems' => 'os' ,
                        'Log Status' => 'logstatus'
                    )
                    
                )
                
            );
            ?>
            
            <?php 
            if( is_array($defaults->multibarsorder) ) {
                $multibar_metrics = array_intersect( $defaults->multibarsorder , array_keys($metrics) );
            } else
            $multibar_metrics = array_keys($metrics);
            ?>
            
			<?php foreach($multibar_metrics as $metric_key) : ?>
                <div class="multimultibar <?php echo esc_attr( $metric_key ); ?>" >
                    
					<div class="multibartitle">
                        <h2>
                            <div class="totalcount <?php echo esc_attr( $metric_key ) ?>" ><span>?</span></div>
                            <?php echo esc_html( $metrics[$metric_key]['label'] ); ?>
                        </h2>
                        <div class="multibarnav">
                            <button class="metricUp"><</button>
                            <button class="metricDown">></button>
                        </div>
					</div>
					
					<div class="multibars" >
                        
                        <?php if( ! empty( $metrics[$metric_key]['tabs']) ) : ?>
                            <div id="<?php echo esc_attr( $metric_key ) ?>_viewbox" class="viewbox_tabs">
                                <ul class="<?php echo esc_attr( $metric_key ) ?>">
                                    <?php foreach( array_keys( $metrics[$metric_key]['tabs'] ) as $tab_key ): ?>
                                    <li><a href="#<?php echo esc_attr( $metrics[$metric_key]['tabs'][$tab_key] ) ?>"><?php echo esc_attr( $tab_key ) ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php foreach( array_keys( $metrics[$metric_key]['tabs'] ) as $tab_key ): ?>
                                    <div id="<?php echo esc_attr( $metrics[$metric_key]['tabs'][$tab_key] ) ?>"></div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div id="<?php echo esc_attr( $metric_key ) ?>_viewbox" class="viewbox_tabs">
                                <div id="<?php echo esc_attr( $metric_key ); ?>"></div>
                            </div>
                        <?php endif; ?>
                            
                        <div class="multibar  <?php echo esc_attr( $metric_key ) ?>" ></div>
                        
                    </div>
                    
					<button class="showall" data-class="<?php echo esc_attr( $metric_key ); ?>" >+ Show more +</button>
                    
                </div>
			<?php endforeach; ?>
			
        </div>
		
    </section>

</main>
