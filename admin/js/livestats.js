var ajaxUrl = jQuery('main').attr('data-ajax');
var nonce = jQuery('main').attr('data-nonce');
var timezone_delta_hours = parseInt( jQuery('main').attr('data-timezone') );

var responseCache;

var timewindowCache = 'hour';
var multibarsorderCache;
var inactivemetricsCache;
var whoCache = {};
var whoTabCache = jQuery("main").attr("data-whotab");
var whenCache = jQuery("main").attr("data-when");
var whereCache = {};
var whereTabCache = jQuery("main").attr("data-wheretab");

function is_touch_enabled() {
    return ( 'ontouchstart' in window ) || 
           ( navigator.maxTouchPoints > 0 ) || 
           ( navigator.msMaxTouchPoints > 0 );
}

function livestats_cacher() {
    
    whoCache.desktop    = jQuery("#desktop").is(":checked");
    whoCache.mobile     = jQuery("#mobile").is(":checked");
    whoCache.tablet     = jQuery("#tablet").is(":checked");
    whoCache.unknown    = jQuery("#unknown").is(":checked");
    whoCache.win        = jQuery("#win").is(":checked");
    whoCache.apple      = jQuery("#apple").is(":checked");
    whoCache.unix       = jQuery("#unix").is(":checked");
    whoCache.other      = jQuery("#other").is(":checked");
    whoCache.loggedin   = jQuery("#loggedin").is(":checked");
    whoCache.loggedout  = jQuery("#loggedout").is(":checked");
    
    whereCache.referrerslike    = jQuery("#referrers_like").val();
    whereCache.referrersfilter  = jQuery("#referrers_filter").val();
    whereCache.originlike       = jQuery("#origin_like").val();
    whereCache.originfilter     = jQuery("#origin_filter").val();
    whereCache.referredlike     = jQuery("#referred_like").val();
    whereCache.referredfilter   = jQuery("#referred_filter").val();
    
}

function livestats_query(timewindow = 'tday') {
    
    livestats_cacher();

    plotter(timewindow);
    timewindowCache = timewindow;
    jQuery("#loading").show();

    jQuery.ajax(
        ajaxurl,{
            method : "POST",
            dataType : "json",
            data : {
                action: 'bubo_insights_livestats_query',
                timewindow: timewindow,
                who: whoCache,
                where: whereCache
            },
            success: function(response) {
                
                responseCache = response;
                
                thePlot(responseCache);
                
//console.log(response.dump);
//console.log('results found in: ' + Math.round(response.performance * 1000)/1000 + 'ms');
                
                jQuery("#loading").hide();
            },
            error: function(response) {
                console.log(response);				 
                jQuery("#loading").hide();
            }
        }
    );
    
}

function livestats_defaults() {
    
    livestats_cacher();

    var defaults = {};

    if(whoCache) {
        let who = [];
        let whoKeys = Object.keys(whoCache);
        for(i=0;i<whoKeys.length;i++){
            let key = whoKeys[i];
            if(whoCache[key]===true) who.push( whoKeys[i] );
        }
        defaults.who = who;
    }
    
    var disbledList = jQuery('.total.disabled');
    var inactivemetricsDefaultsNew = [];
    for(i=0;i<disbledList.length;i++){
        inactivemetricsDefaultsNew.push(disbledList[i].classList[1])
    }
    inactivemetricsCache = inactivemetricsDefaultsNew;
    
    var multibarList = jQuery('.multimultibar');
    var multibarsorderNew = [];
    for(i=0;i<multibarList.length;i++){
        multibarsorderNew.push(multibarList[i].classList[1])
    }
    multibarsorderCache = multibarsorderNew;
        
    if(inactivemetricsCache) defaults.inactivemetrics = inactivemetricsCache;
    if(multibarsorderCache) defaults.multibarsorder = multibarsorderCache;
    if(whoTabCache) defaults.whotab = [ whoTabCache ];
    if(whenCache) defaults.when = [ whenCache ];
    if(whereCache) defaults.wherepage = [ whereCache.originlike , whereCache.originfilter ];
    if(whereCache) defaults.wherefrom = [ whereCache.referrerslike , whereCache.referrersfilter ];
    if(whereCache) defaults.wheregoto = [ whereCache.referredlike , whereCache.referredfilter ];
    if(whereTabCache) defaults.wheretab = [ whereTabCache ];

    var userid = jQuery("main").attr("data-userid");

    jQuery.ajax(
        ajaxurl,{
            method : "POST",
            dataType : "json",
            data : {
                action: 'bubo_insights_livestats_defaults',
                userid: userid,
                defaults: defaults
            },
            success: function(response) {
                defaultsCache = response;
//                console.log(response);
            },
            error: function(response) {
                console.log(response);	
            }
        }
    );
    
    return 'defaults updated';
    
}

var roundToNearestMinuteInterval = function (date, interval = 5) {
    var coeff = 1000 * 60 * interval // <-- Replace {5} with interval
    return new Date(Math.floor(date.getTime() / coeff) * coeff + coeff);
};

var addZero = function (i) {
  if (i < 10) {i = "0" + i}
  return i;
}

const timebins = {
    'hour': 12,
    'day': 24,
    'week': 7,
    'month': 30,
    'year': 12,
    'all': 12,
    'tday': 24,
    'tweek': 7,
    'tmonth': daysInThisMonth(),
    'tyear': 12,
    'pday': 24,
    'pweek': 7,
    'pmonth': daysInThisMonth('previous'),
    'pyear': 12
};

function daysInThisMonth(monthNumber = 'this', yearFourDigits) {
    var now = new Date();
    if( monthNumber == 'this' ) {
        return new Date(now.getFullYear(), now.getMonth()+1, 0).getDate();
    }
    else if(monthNumber == 'previous') {
        return new Date(now.getFullYear(), now.getMonth(), 0).getDate();
    }
    else {
        if(! yearFourDigits) yearFourDigits = now.getFullYear();
        return new Date(yearFourDigits, monthNumber, 0).getDate();
    }
}

function plotter(timewindow = 'day') {
    
    const date = new Date();
    const d = roundToNearestMinuteInterval(date , 5);

    if( timewindow == 'tday' ) {
        d.setHours(23 , 0 , 0);
    }
    else if( timewindow == 'tweek' ) {
        d.setDate(d.getDate()-d.getDay()+7);
    }
    else if( timewindow == 'tmonth' ) {
        d.setDate(1);
        d.setMonth(d.getMonth()+1);
        d.setDate(d.getDate()-1);
    }
    else if( timewindow == 'tyear' ) {
        d.setMonth(0);
        d.setFullYear(d.getFullYear()+1);
        d.setMonth(d.getMonth()-1);
    }
    else if( timewindow == 'pday' ) {
        d.setHours(23 , 0 , 0);
        d.setDate(d.getDate()-1);
    }
    else if( timewindow == 'pweek' ) {
        d.setDate(d.getDate()-d.getDay());
    }
    else if( timewindow == 'pmonth' ) {
        d.setDate(1);
        d.setMonth(d.getMonth());
        d.setDate(d.getDate()-1);
    }
    else if( timewindow == 'pyear' ) {
        d.setMonth(0);
        d.setFullYear(d.getFullYear());
        d.setMonth(d.getMonth()-1);
    }
    
    const weekdays = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    const month = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
    
    jQuery("#x-axis").html('');
    jQuery("#visitorbars").html('');
    jQuery("#viewbars").html('');
    jQuery("#visitbars").html('');
    jQuery("#clickbars").html('');
    jQuery("#eclickbars").html('');
    jQuery("#iclickbars").html('');
    
    jQuery("#viewcounters").html('');
    jQuery("#visitcounters").html('');
    jQuery("#visitorcounters").html('');
    jQuery("#clickcounters").html('');
    jQuery("#eclickcounters").html('');
    jQuery("#iclickcounters").html('');
    
    for (let i = 0; i < timebins[timewindow]; i++) {
        
        var currentTimebin = '';
        if(timewindow == 'hour') {
            d.setMinutes(d.getMinutes()-5);
            currentTimebin = d.getHours()+':'+addZero(d.getMinutes());
        }
        else if(timewindow == 'day' || timewindow == 'tday' || timewindow == 'pday') {
            currentTimebin = d.getHours()+'<span>:00</span>';
            d.setHours(d.getHours()-1);
        }
        else if(timewindow == 'week' || timewindow == 'tweek' || timewindow == 'pweek') {
            currentTimebin = weekdays[d.getDay()]+' '+d.getDate()+' '+month[d.getMonth()];
            d.setDate(d.getDate()-1);
        }
        else if(timewindow == 'month' || timewindow == 'tmonth' || timewindow == 'pmonth') {
            currentTimebin = d.getDate()+' '+month[d.getMonth()];
            d.setDate(d.getDate()-1);
        }
        else if(timewindow == 'year' || timewindow == 'tyear'|| timewindow == 'pyear') {
            currentTimebin = month[d.getMonth()];
            d.setMonth(d.getMonth()-1);
        }
        else {
            currentTimebin = d.getFullYear();
            d.setFullYear(d.getFullYear()-1);
        }
        
        jQuery("#x-axis").append('<div class="x-unit '+timewindow +'_tw" ><span>'+currentTimebin+'</span></div>');
        jQuery("#visitorbars").append('<div class="visitors"><span>x</span></div>');
        jQuery("#viewbars").append('<div class="views"><span>x</span></div>');
        jQuery("#visitbars").append('<div class="session"><span>x</span></div>');
        jQuery("#clickbars").append('<div class="clicks"><span>x</span></div>');
        jQuery("#eclickbars").append('<div class="eclicks"><span>x</span></div>');
        jQuery("#iclickbars").append('<div class="iclicks"><span>x</span></div>');
        
        
        jQuery("#viewcounters").append('<div class="counter views ' + metricDisabledStatus('views') + '" style="bottom:0%;right:' + (100/(timebins[timewindow] - 1)) * i + '%"></div>');
        jQuery("#visitcounters").append('<div class="counter visits ' + metricDisabledStatus('visits') + '" style="bottom:0%;right:' + (100/(timebins[timewindow] - 1)) * i + '%"></div>');
        jQuery("#visitorcounters").append('<div class="counter visitors ' + metricDisabledStatus('visitors') + '" style="bottom:0%;right:' + (100/(timebins[timewindow] - 1)) * i + '%"></div>');
        jQuery("#clickcounters").append('<div class="counter clicks ' + metricDisabledStatus('clicks') + '" style="bottom:0%;right:' + (100/(timebins[timewindow] - 1)) * i + '%"></div>');
        jQuery("#eclickcounters").append('<div class="counter eclicks ' + metricDisabledStatus('eclicks') + '" style="bottom:0%;right:' + (100/(timebins[timewindow] - 1)) * i + '%"></div>');
        jQuery("#iclickcounters").append('<div class="counter iclicks ' + metricDisabledStatus('iclicks') + '" style="bottom:0%;right:' + (100/(timebins[timewindow] - 1)) * i + '%"></div>');
        
    }
    
}

function thePlot(response) {
    
    if(!response) return;
    
    var timewindow = timewindowCache;
    
    var ymax = Math.max( 
        response.viewbarmax * ! jQuery(".total.views").hasClass("disabled"),
        response.visitbarmax * ! jQuery(".total.visits").hasClass("disabled"),
        response.visitorbarmax * ! jQuery(".total.visitors").hasClass("disabled"),
        response.eclickbarmax * ! jQuery(".total.eclicks").hasClass("disabled"),
        response.iclickbarmax * ! jQuery(".total.iclicks").hasClass("disabled")
    ); 
    
    if(response.max > 0 && response.max < 5) ymax = 5;
    if(response.max == 0) ymax = 1;
    var yOrderOfMAgnitude = Math.pow( 10, ( Math.floor( Math.log(ymax) / Math.LN10 + 0.000000001 ) ) );
    ymax = ( Math.ceil( (2 * ymax) / yOrderOfMAgnitude ) / 2 ) * yOrderOfMAgnitude;

    
    var yAxis = jQuery('#y-axis');
    yAxis.html('');
    for (let i = 0; i <= Math.min(5, ymax); i++) {
        var yUnit =  '<div class="y-unit ' + i + '"><span>' + Math.max( (1 - i) , Math.ceil( ( ymax /  Math.max( 1 , Math.min(5, ymax) ) ) * ( Math.min(5, ymax) - i) ) ) + '</span></div>';
        yAxis.append( yUnit );
    }
    
    var viewbars = jQuery('#viewbars div');
    var visitbars = jQuery('#visitbars div');
    var visitorbars = jQuery('#visitorbars div');
    var eclickbars = jQuery('#eclickbars div');
    var iclickbars = jQuery('#iclickbars div');
    
    for (let i = 0; i < timebins[timewindow]; i++) {
        viewbars[i].innerHTML = '<span>' + response.viewbars[i] + '</span>';
        viewbars[i].style.height =  ( ( 95 / ymax ) * response.viewbars[i] + 5 ) + '%';
        
        visitbars[i].innerHTML = '<span>' + response.visitbars[i] + '</span>';
        visitbars[i].style.height =  ( ( 95 / ymax ) * response.visitbars[i] + 5 ) + '%';
        
        visitorbars[i].innerHTML = '<span>' + response.visitorbars[i] + '</span>';
        visitorbars[i].style.height =  ( ( 95 / ymax ) * response.visitorbars[i] + 5 ) + '%';
                            
        eclickbars[i].innerHTML = '<span>' + response.eclickbars[i] + '</span>';
        eclickbars[i].style.height =  ( ( 95 / ymax ) * response.eclickbars[i] + 5 ) + '%';
                            
        iclickbars[i].innerHTML = '<span>' + response.iclickbars[i] + '</span>';
        iclickbars[i].style.height =  ( ( 95 / ymax ) * response.iclickbars[i] + 5 ) + '%';
    }
    
    var viewcounters = jQuery('#viewcounters div');
    var visitcounters = jQuery('#visitcounters div');
    var visitorcounters = jQuery('#visitorcounters div');
    var eclickcounters = jQuery('#eclickcounters div');
    var iclickcounters = jQuery('#iclickcounters div');
    
    var columnModulePosition = 100 / ( timebins[timewindow] - 1  );
    var columnModuleHeight = 96.5 / ymax;
    
    for (let i = 0; i < timebins[timewindow]; i++) {
        viewcounters[i].innerHTML = response.viewbars[i];
        viewcounters[i].style.right =  ( columnModulePosition * i ) + '%';
        viewcounters[i].style.bottom =  ( columnModuleHeight * response.viewbars[i] ) + '%';
        
        visitcounters[i].innerHTML = response.visitbars[i];
        visitcounters[i].style.right =  ( columnModulePosition * i ) + '%';
        visitcounters[i].style.bottom =  ( columnModuleHeight * response.visitbars[i] ) + '%';
        
        visitorcounters[i].innerHTML = response.visitorbars[i];
        visitorcounters[i].style.right =  ( columnModulePosition * i ) + '%';
        visitorcounters[i].style.bottom =  ( columnModuleHeight * response.visitorbars[i] ) + '%';
        
        eclickcounters[i].innerHTML = response.eclickbars[i];
        eclickcounters[i].style.right =  ( columnModulePosition * i ) + '%';
        eclickcounters[i].style.bottom =  ( columnModuleHeight * response.eclickbars[i] ) + '%';
        
        iclickcounters[i].innerHTML = response.iclickbars[i];
        iclickcounters[i].style.right =  ( columnModulePosition * i ) + '%';
        iclickcounters[i].style.bottom =  ( columnModuleHeight * response.iclickbars[i] ) + '%';
    }
    
    var viewpoly = '';
    var visitpoly = '';
    var visitorpoly = '';
    var eclickpoly = '';
    var iclickpoly = '';
    
    for (let i = 0; i < timebins[timewindow] ; i++) {
        
        var preindex = (timebins[timewindow] - i - 0.5);
        var index = (timebins[timewindow] - i - 1);
        var postindex = (timebins[timewindow] - i - 1.5);
        
        viewpoly += ' ' + preindex + ',' + response.viewbars[i] + ' ' + index + ',' + response.viewbars[i] + ' C ' + postindex + ',' + response.viewbars[i];
        visitpoly += ' ' + preindex + ',' + response.visitbars[i] + ' ' + index + ',' + response.visitbars[i] + ' C ' + postindex + ',' + response.visitbars[i];
        visitorpoly += ' ' + preindex + ',' + response.visitorbars[i] + ' ' + index + ',' + response.visitorbars[i] + ' C ' + postindex + ',' + response.visitorbars[i];
        eclickpoly += ' ' + preindex + ',' + response.eclickbars[i] + ' ' + index + ',' + response.eclickbars[i] + ' C ' + postindex + ',' + response.eclickbars[i];
        iclickpoly += ' ' + preindex + ',' + response.iclickbars[i] + ' ' + index + ',' + response.iclickbars[i] + ' C ' + postindex + ',' + response.iclickbars[i];
    }
    var i = timebins[timewindow] - 1;
    viewpoly += ' ' + postindex + ',' + response.viewbars[i] + ' ' + postindex + ',' + response.viewbars[i];
    visitpoly += ' ' + postindex + ',' + response.visitbars[i] + ' ' + postindex + ',' + response.visitbars[i]
    visitorpoly += ' ' + postindex + ',' + response.visitorbars[i] + ' ' + postindex + ',' + response.visitorbars[i]
    eclickpoly += ' ' + postindex + ',' + response.eclickbars[i] + ' ' + postindex + ',' + response.eclickbars[i]
    iclickpoly += ' ' + postindex + ',' + response.iclickbars[i] + ' ' + postindex + ',' + response.iclickbars[i]
    
    jQuery("#polylines").html('');
    jQuery("#polylines").append(polyLiner(viewpoly, ymax, 'views ' + metricDisabledStatus('views') ));
    jQuery("#polylines").append(polyLiner(visitpoly, ymax, 'visits ' + metricDisabledStatus('visits') ));
    jQuery("#polylines").append(polyLiner(visitorpoly, ymax, 'visitors ' + metricDisabledStatus('visitors') ));
    jQuery("#polylines").append(polyLiner(eclickpoly, ymax, 'eclicks ' + metricDisabledStatus('eclicks') ));
//    jQuery("#polylines").append(polyLiner(iclickpoly, ymax, 'iclicks ' + metricDisabledStatus('iclicks') ));
    
    
    jQuery(".totalcount.views").html( numberToFourDigits( response.views ) );
    jQuery(".totalcount.visits").html( numberToFourDigits( response.visits ) );
    jQuery(".totalcount.visitors").html( numberToFourDigits( response.visitors ) );
    jQuery(".totalcount.clicks").html( numberToFourDigits(  response.eclicks ) );
    jQuery(".totalcount.iclicks").html( numberToFourDigits( response.iclicks ) );
    jQuery(".totalcount.eclicks").html( numberToFourDigits( response.eclicks ) );
    jQuery(".totalcount.referrers").html( numberToFourDigits( response.referrers ) );
    jQuery(".totalcount.referrals").html( numberToFourDigits( response.referrals ) );
    
    var multibarsViews = '';
    var multibarsVisits = '';
    
    
    var multibarsDevices    = '';
    var multibarsOs         = '';
    var multibarsLogstatus  = '';
    
    var multibarsIclicks = '';
    var multibarsEclicks = '';
    var multibarsReferrers = '';
    
    if(response.desktop > 0) {
        var multibarBar = '<div class="multibarbar visitors" style="width:' + 99 * response.desktop / response.devicesmax + '%"></div>';
        var multibarText = '<span class="multibartext"><span>[' + numberToFourDigits(response.desktop) + '] Desktop</span><span class="multipercentage">' + Math.round( 100 * response.desktop / response.devices ) + '%</span></span>';
        var multibar = '<div class="multibar visitors" style="order:' + response.desktop + '">' + multibarBar + multibarText + '</div>';
        multibarsDevices = multibarsDevices + multibar;
    }
    if(response.mobile > 0) {
        var multibarBar = '<div class="multibarbar visitors" style="width:' + 99 * response.mobile / response.devicesmax + '%"></div>';
        var multibarText = '<span class="multibartext"><span>[' + numberToFourDigits(response.mobile) + '] Mobile</span><span class="multipercentage">' + Math.round( 100 * response.mobile / response.devices ) + '%</span></span>';
        var multibar = '<div class="multibar visitors" style="order:' + response.mobile + '">' + multibarBar + multibarText + '</div>';
        multibarsDevices = multibarsDevices + multibar;
    }
    if(response.tablet > 0) {
        var multibarBar = '<div class="multibarbar visitors" style="width:' + 99 * response.tablet / response.devicesmax + '%"></div>';
        var multibarText = '<span class="multibartext"><span>[' + numberToFourDigits(response.tablet) + '] Tablet</span><span class="multipercentage">' + Math.round( 100 * response.tablet / response.devices ) + '%</span></span>';
        var multibar = '<div class="multibar visitors" style="order:' + response.tablet + '">' + multibarBar + multibarText + '</div>';
        multibarsDevices = multibarsDevices + multibar;
    }
    if(response.unknown > 0) {
        var multibarBar = '<div class="multibarbar visitors" style="width:' + 99 * response.unknown / response.devicesmax + '%"></div>';
        var multibarText = '<span class="multibartext"><span>[' + numberToFourDigits(response.unknown) + '] Unknown Devices</span><span class="multipercentage">' + Math.round( 100 * response.unknown / response.devices ) + '%</span></span>';
        var multibar = '<div class="multibar visitors" style="order:' + response.unknown + '">' + multibarBar + multibarText + '</div>';
        multibarsDevices = multibarsDevices + multibar;
    }
    
    if(response.win > 0) {
        var multibarBar = '<div class="multibarbar visitors" style="width:' + 99 * response.win / response.osmax + '%"></div>';
        var multibarText = '<span class="multibartext"><span>[' + numberToFourDigits(response.win) + '] Windows</span><span class="multipercentage">' + Math.round( 100 * response.win / response.os ) + '%</span></span>';
        var multibar = '<div class="multibar visitors" style="order:' + response.win + '">' + multibarBar + multibarText + '</div>';
        multibarsOs = multibarsOs + multibar;
    }
    if(response.apple > 0) {
        var multibarBar = '<div class="multibarbar visitors" style="width:' + 99 * response.apple / response.osmax + '%"></div>';
        var multibarText = '<span class="multibartext"><span>[' + numberToFourDigits(response.apple) + '] iOS/MacOS</span><span class="multipercentage">' + Math.round( 100 * response.apple / response.os ) + '%</span></span>';
        var multibar = '<div class="multibar visitors" style="order:' + response.apple + '">' + multibarBar + multibarText + '</div>';
        multibarsOs = multibarsOs + multibar;
    }
    if(response.unix > 0) {
        var multibarBar = '<div class="multibarbar visitors" style="width:' + 99 * response.unix / response.osmax + '%"></div>';
        var multibarText = '<span class="multibartext"><span>[' + numberToFourDigits(response.unix) + '] Android/Linux</span><span class="multipercentage">' + Math.round( 100 * response.unix / response.os ) + '%</span></span>';
        var multibar = '<div class="multibar visitors" style="order:' + response.unix + '">' + multibarBar + multibarText + '</div>';
        multibarsOs = multibarsOs + multibar;
    }
    if(response.other > 0) {
        var multibarBar = '<div class="multibarbar visitors" style="width:' + 99 * response.other / response.osmax + '%"></div>';
        var multibarText = '<span class="multibartext"><span>[' + numberToFourDigits(response.other) + '] Other OS</span><span class="multipercentage">' + Math.round( 100 * response.other / response.os ) + '%</span></span>';
        var multibar = '<div class="multibar visitors" style="order:' + response.other + '">' + multibarBar + multibarText + '</div>';
        multibarsOs = multibarsOs + multibar;
    }
    
    if(response.loggedin > 0) {
        var multibarBar = '<div class="multibarbar visitors" style="width:' + 99 * response.loggedin / response.logstatusmax + '%"></div>';
        var multibarText = '<span class="multibartext"><span>[' + numberToFourDigits(response.loggedin) + '] Logged in Users</span><span class="multipercentage">' + Math.round( 100 * response.loggedin / response.logstatus ) + '%</span></span>';
        var multibar = '<div class="multibar visitors" style="order:' + response.loggedin + '">' + multibarBar + multibarText + '</div>';
        multibarsLogstatus = multibarsLogstatus + multibar;
    }
    if(response.loggedout > 0) {
        var multibarBar = '<div class="multibarbar visitors" style="width:' + 99 * response.loggedout / response.logstatusmax + '%"></div>';
        var multibarText = '<span class="multibartext"><span>[' + numberToFourDigits(response.loggedout) + '] Visitors</span><span class="multipercentage">' + Math.round( 100 * response.loggedout / response.logstatus ) + '%</span></span>';
        var multibar = '<div class="multibar visitors" style="order:' + response.loggedout + '">' + multibarBar + multibarText + '</div>';
        multibarsLogstatus = multibarsLogstatus + multibar;
    }
    
    if(response.originviewbox[0]) {
        
        for (let i = 0; i < response.originviewbox.length; i++) {
            if(response.originviewbox[i]) {
                var multibarBar = '<div class="multibarbar views" style="width:' + 99 * response.originviewbox[i].origin_view_count / response.originviewmax + '%"></div>';
                var multibarText = '<span>[' + numberToFourDigits(response.originviewbox[i].origin_view_count) + '] ' + response.originviewbox[i].origin.replace('https://' , '').replace('http://' , '') + '</span>';
                var multibarLink = '<b><a href="' + response.originviewbox[i].origin + '" target="_blank" rel="noopener" >[➚]</a></b>';
                var multibar = '<div class="multibar views" style="order:' + response.originviewbox[i].origin_view_count + '">' + multibarBar + multibarText + multibarLink + '</div>';
                multibarsViews = multibarsViews + multibar;
                
                var multibarBar = '<div class="multibarbar visits" style="width:' + 99 * response.originviewbox[i].origin_visit_count / response.originvisitmax + '%"></div>';
                var multibarText = '<span>[' + numberToFourDigits(response.originviewbox[i].origin_visit_count) + '] ' + response.originviewbox[i].origin.replace('https://' , '').replace('http://' , '') + '</span>';
                var multibarLink = '<b><a href="' + response.originviewbox[i].origin + '" target="_blank" rel="noopener" >[➚]</a></b>';
                var multibar = '<div class="multibar visits" style="order:' + response.originviewbox[i].origin_visit_count + '">' + multibarBar + multibarText + multibarLink + '</div>';
                multibarsVisits = multibarsVisits + multibar;
            }
        }
        
    }
    
    if(response.linkviewbox[0]) {
        
        for (let i = 0; i < response.linkviewbox.length; i++) {
            
            var multibarBar = '<div class="multibarbar iclicks" style="width:' + 99 * response.linkviewbox[i].in_link_click_count / response.inlinkclickmax + '%"></div>';
            var multibarText = '<span>[' + numberToFourDigits(response.linkviewbox[i].in_link_click_count) + '] ' + response.linkviewbox[i].link + '</span>';
            var multibarLink = '<b><a href="' + response.linkviewbox[i].link + '" target="_blank" rel="noopener" >[➚]</a></b>';
            var multibar = '<div class="multibar iclicks" style="order:' + response.linkviewbox[i].in_link_click_count + '">' + multibarBar + multibarText + multibarLink + '</div>';
            if(response.linkviewbox[i].in_link_click_count == 0) multibar = '';
            multibarsIclicks = multibarsIclicks + multibar;
            
            var multibarBar = '<div class="multibarbar eclicks" style="width:' + 99 * response.linkviewbox[i].out_link_click_count / response.outlinkclickmax + '%"></div>';
            var multibarText = '<span>[' + numberToFourDigits(response.linkviewbox[i].out_link_click_count) + '] ' + response.linkviewbox[i].link + '</span>';
            var multibarLink = '<b><a href="' + response.linkviewbox[i].link + '" target="_blank" rel="noopener" >[➚]</a></b>';
            var multibar = '<div class="multibar eclicks" style="order:' + response.linkviewbox[i].out_link_click_count + '">' + multibarBar + multibarText + multibarLink + '</div>';
            if(response.linkviewbox[i].out_link_click_count == 0) multibar = '';
            multibarsEclicks = multibarsEclicks + multibar;
            
        }
        
    }
    
    if(response.referrerviewbox[0]) {
        
        for (let i = 0; i < response.referrerviewbox.length; i++) {
            
            if(! response.referrerviewbox[i].referrer) response.referrerviewbox[i].referrer = 'direct';
            
            var multibarBar = '<div class="multibarbar referrers" style="width:' + 99 * response.referrerviewbox[i].referral_count / response.referralmax + '%"></div>';
            
            var multibarLink = '<b><a href="' + response.referrerviewbox[i].referrer + '" target="_blank" rel="noopener" >[➚]</a></b>';
            if(response.referrerviewbox[i].referrer == 'direct') var multibarLink = '';
            var multibarText = '<span class="multibartext"><span>[' + numberToFourDigits(response.referrerviewbox[i].referral_count) + '] ' + response.referrerviewbox[i].referrer + ' ' + multibarLink + '</span></span>';
            var multibarPercentage = '<span class="multipercentage">' + Math.round( 100 * response.referrerviewbox[i].referral_count / response.referrals ) + '%</span></div>';
            if(response.referrerviewbox[i].referrer == 'direct') multibarLink = '';
            var multibar = '<div class="multibar referrers" style="order:' + response.referrerviewbox[i].referral_count + '">' + multibarBar + multibarText + multibarPercentage;
            if(response.referrerviewbox[i].referral_count == 0) multibar = '';
            multibarsReferrers = multibarsReferrers + multibar;
            
        }
        
    }
    
    if(multibarsViews == '') multibarsViews = 'No views in this period.';
    if(multibarsVisits == '') multibarsVisits = 'No visits in this period.';
    if(multibarsDevices == '') multibarsDevices = 'No visitors in this period.';
    if(multibarsOs == '') multibarsOs = 'No visitors in this period.';
    if(multibarsLogstatus == '') multibarsLogstatus = 'No visitors in this period.';
    if(multibarsIclicks == '') multibarsIclicks = 'No inbound clicks in this period.';
    if(multibarsEclicks == '') multibarsEclicks = 'No outbound clicks in this period.';
    if(multibarsReferrers == '') multibarsReferrers = 'No referrers in this period.';
    
    jQuery(".multimultibar.visits .multibars #views").html( multibarsViews );
    jQuery(".multimultibar.visits .multibars #visits").html( multibarsVisits );
    
    jQuery(".multimultibar.visitors .multibars #devices").html( multibarsDevices );
    jQuery(".multimultibar.visitors .multibars #os").html( multibarsOs );
    jQuery(".multimultibar.visitors .multibars #logstatus").html( multibarsLogstatus );
    
    jQuery(".multimultibar.clicks .multibars #internal_clicks").html( multibarsIclicks );
    jQuery(".multimultibar.clicks .multibars #external_clicks").html( multibarsEclicks );
    jQuery(".multimultibar.referrers .multibars #referrals").html( multibarsReferrers );
}

function metricDisabledStatus(className) {
    if( jQuery(".total." + className).hasClass("disabled") ) {
        return 'disabledmetric';
    }
    else {
        return '';
    }
}

function numberToFourDigits(number) {
    var numberWithFourDigits = Math.round(number);
    if( number > 999 ) numberWithFourDigits = Math.floor( numberWithFourDigits / 1000 ) + 'K';
    if( number > 999999 ) numberWithFourDigits = Math.floor( numberWithFourDigits / 1000000 ) + 'M';
    if( number > 999999999 ) numberWithFourDigits = Math.floor( numberWithFourDigits / 1000000000 ) + 'B';
    return numberWithFourDigits;
}

function polyLiner(data, height = 10, className) {
    let polylineD = 'M  ' + data;
    let polyline = '<svg' + 	
    '    width="' + ( timebins[timewindowCache] - 1 ) + '" ' +
    '    height="' + height + '" ' +
	'	viewbox="0 0 ' + ( timebins[timewindowCache] - 1 ) + ' ' + height + '" ' +
	'	preserveAspectRatio="none" ' +
	'	> ' +
	'	<path class="' + className + '" d="' + polylineD  + '" vector-effect="non-scaling-stroke" /> ' +
	' </svg>';
	return polyline;
}

/*
function pieCharter(targetid, data = [360], labels = ['','','',''] , colors = ['Coral','MediumSeaGreen','SteelBlue','Orange','Silver']) {
    var canvas = document.getElementById( targetid );
    var ctx = canvas.getContext("2d");
    var lastend = 0;
    var data = data;
    var myTotal = 0;
    var myColor = colors;
    var labels = labels;
    
    for(var e = 0; e < data.length; e++)
    {
      myTotal += data[e];
    }
    
    // make the chart 10 px smaller to fit on canvas
    var off = 4
    var w = (canvas.width - off) / 2
    var h = (canvas.height - off) / 2
    for (var i = 0; i < data.length; i++) {
      ctx.fillStyle = myColor[i];
      ctx.strokeStyle ='transparent';
      ctx.lineWidth = 1;
      ctx.beginPath();
      ctx.moveTo(w,h);
      var len =  (data[i]/myTotal) * 2 * Math.PI
      var r = h - off / 2
      ctx.arc(w , h, r, lastend,lastend + len,false);
      ctx.lineTo(w,h);
      ctx.fill();
      ctx.stroke();
      ctx.fillStyle ='black';
      ctx.font = "12px Arial";
      ctx.textAlign = "center";
      ctx.textBaseline = "middle";
      var mid = lastend + len / 2
      ctx.fillText(labels[i],w + Math.cos(mid) * (0.6*r) , h + Math.sin(mid) * (0.6*r));
      lastend += Math.PI*2*(data[i]/myTotal);
    }
}
*/

function metricVisibility(e) {
    if(e) {
        var targetClass = e.currentTarget.classList[1];
        jQuery(".total."+targetClass).toggleClass("disabled");
        jQuery("path."+targetClass).toggleClass("disabledmetric");
        jQuery(".counters ."+targetClass).toggleClass("disabledmetric");
        jQuery("#bars ."+targetClass).toggleClass("disabledmetric");
        
        plotter(timewindowCache);
        thePlot(responseCache);
    }
}

jQuery(document).ready( function() {
    
    // filters
    jQuery(".filterpanel").tabs();
    
    // whofilters
    jQuery("#whofilter").on('change', 'input', function(e) { 
        livestats_query(timewindowCache);
        livestats_defaults();
    } );
    // whotabs
    jQuery("#whofilter").on( 'click', 'a', function(evt) {
        whoTabCache = evt.currentTarget.getAttribute("data-tab");
        livestats_defaults();
    });
    
    // whenfilters
    jQuery(".time_button").on( "click" , function(evt) {
        livestats_query(evt.target.value);
        whenCache = evt.target.value;
        livestats_defaults();
        jQuery(".time_button").removeClass("selected");
        jQuery("#"+evt.currentTarget.id).addClass("selected");
        var timeTabName = jQuery("#"+evt.currentTarget.id).attr("data-tab");
        jQuery("a[href='#"+timeTabName+"']").click();
    });
    
    // wherefilters
    jQuery(".wherefilterbutton").hide();
    
    jQuery(".wherefilterinput_origin").on('change input', function(e) { 
        jQuery(".wherefilterbutton_origin").show();
        whereCache.originfilter = jQuery(".wherefilterbutton_origin").val();
    } );
    jQuery(".wherefilterbutton_origin").on('click', function(e) { 
        jQuery(".wherefilterbutton_origin").hide();
        livestats_query(timewindowCache);
        livestats_defaults();
    } );
    jQuery(".wherefilterclear_origin").on('click', function(e) { 
        jQuery(".wherefilterinput_origin").val('');
        livestats_query(timewindowCache);
        whereCache.originfilter = '';
        livestats_defaults();
    } );
    jQuery("#origin_like").on('change', function(e) { 
        whereCache.originlike = jQuery("#origin_like").val();
        livestats_defaults();
    } );
    
    jQuery(".wherefilterinput_referrers").on('change input', function(e) { 
        jQuery(".wherefilterbutton_referrers").show();
    } );
    jQuery(".wherefilterbutton_referrers").on('click', function(e) { 
        jQuery(".wherefilterbutton_referrers").hide();
        livestats_query(timewindowCache);
        livestats_defaults();
    } );
    jQuery(".wherefilterclear_referrers").on('click', function(e) { 
        jQuery(".wherefilterinput_referrers").val('');
        livestats_query(timewindowCache);
        livestats_defaults();
    } );
    jQuery("#referrers_like").on('change', function(e) { 
        whereCache.referrerslike = jQuery("#referrers_like").val();
        livestats_defaults();
    } );
    
    jQuery(".wherefilterinput_referred").on('change input', function(e) { 
        jQuery(".wherefilterbutton_referred").show();
    } );
    jQuery(".wherefilterbutton_referred").on('click', function(e) { 
        jQuery(".wherefilterbutton_referred").hide();
        livestats_query(timewindowCache);
        livestats_defaults();
    } );
    jQuery(".wherefilterclear_referred").on('click', function(e) { 
        jQuery(".wherefilterinput_referred").val('');
        livestats_query(timewindowCache);
        livestats_defaults();
    } );
    jQuery("#referred_like").on('change', function(e) { 
        whereCache.referredlike = jQuery("#referred_like").val();
        livestats_defaults();
    } );
    
    // wheretabs
    jQuery("#wherefilter").on( 'click', 'a', function(evt) {
        whereTabCache = evt.currentTarget.getAttribute("data-tab");
        livestats_defaults();
    });
    
    // toggle visibility for metrics
    jQuery(".total").on('click', function(e) {
        metricVisibility(e);
        if ( e.originalEvent === undefined ) {
        } else {
            livestats_defaults();
        }
    } );
    
    // multibars order tracker
    jQuery('.multimultimultibar').on('drag', function( event, ui ) {
        jQuery('.multimultimultibar').click();
    });
    jQuery('.multimultimultibar').on('click', function(e) {
        setTimeout(() => {
            livestats_defaults();
        }, "1000");
    });
    
    //default view of the page
    var inactivemetricsDefaults = jQuery("main").attr("data-inactivemetrics").split(',');
    if(inactivemetricsDefaults != '') {
        for(i=0;i<inactivemetricsDefaults.length;i++) {
            
           jQuery(".total."+inactivemetricsDefaults[i]).click(); 
        }
    }
    var whoTabDefaults = jQuery("main").attr("data-whotab").split(','); 
    for(i=0;i<whoTabDefaults.length;i++) {
       jQuery("#"+whoTabDefaults[i]+"_tab").click();
    }
    var wherePageDefaults = jQuery("main").attr("data-wherepage").split(',');
    jQuery("#origin_like").val(wherePageDefaults[0]);
    jQuery("#origin_filter").val(wherePageDefaults[1]);
    var whereFromDefaults = jQuery("main").attr("data-wherefrom").split(',');
    jQuery("#referrers_like").val(whereFromDefaults[0]);
    jQuery("#referrers_filter").val(whereFromDefaults[1]);
    var whereGotoDefaults = jQuery("main").attr("data-wheregoto").split(',');
    jQuery("#referred_like").val(whereGotoDefaults[0]);
    jQuery("#referred_filter").val(whereGotoDefaults[1]);
    var whereTabDefaults = jQuery("main").attr("data-wheretab").split(','); 
    for(i=0;i<whereTabDefaults.length;i++) {
       jQuery("#"+whereTabDefaults[i]+"_tab").click();
    }
    // this at last to trigger the query
    var whenDefaults = jQuery("main").attr("data-when").split(',');
    for(i=0;i<whenDefaults.length;i++) {
       jQuery("#get_"+whenDefaults[i]).click();
    }

    
    // multibars UI desktop
    if( ! is_touch_enabled() ) {
        
        jQuery(".multimultimultibar").sortable({
            handle: ".multibartitle",
            revert: true,
            start: function(e, ui){
                ui.placeholder.height(ui.item.height());
                ui.placeholder.css('visibility', 'visible');
                ui.placeholder.css('filter', 'opacity(25%)');
                ui.placeholder.css('border', '2px dotted black');
            },
            drag: function( event, ui ) {}
        });
        jQuery(".multimultibar h2").disableSelection();
        
    }    
    // multibars UI mobile
    jQuery('.multimultibar').on( "click" , ".metricUp" , function(evt){
      var current = jQuery(evt.delegateTarget);
      current.prev().before(current);
    });
    jQuery('.multimultibar').on( "click" , ".metricDown" , function(evt){
      var current = jQuery(evt.delegateTarget);
      current.next().after(current);
    });
    // multibars showall
    jQuery("body").on('click', '.showall', function(e) { 
        let eclass = e.target.getAttribute("data-class");
        if(jQuery(".multimultibar."+eclass+" .showall")[0].innerHTML == "+ Show more +") {
            jQuery(".multimultibar."+eclass+" .multibars").addClass("multibarslarge");
            jQuery(".multimultibar."+eclass+" .showall")[0].innerHTML = "+ Show all +";
        }
        else if(jQuery(".multimultibar."+eclass+" .showall")[0].innerHTML == "+ Show all +") {
            jQuery(".multimultibar."+eclass+" .multibars").addClass("multibarsopen");
            jQuery(".multimultibar."+eclass+" .multibars").removeClass("multibarslarge");
            jQuery(".multimultibar."+eclass+" .showall")[0].innerHTML = "- Show less -";
        }
        else if(jQuery(".multimultibar."+eclass+" .showall")[0].innerHTML == "- Show less -") {
            jQuery(".multimultibar."+eclass+" .multibars").removeClass("multibarsopen");
            jQuery(".multimultibar."+eclass+" .showall")[0].innerHTML = "+ Show more +";
        }
    } );
    //multibars tabbing
    jQuery( "#visits_viewbox" ).tabs();
    jQuery( "#visitors_viewbox" ).tabs();
    jQuery( "#referrers_viewbox" ).tabs();
    jQuery( "#clicks_viewbox" ).tabs();
    
});