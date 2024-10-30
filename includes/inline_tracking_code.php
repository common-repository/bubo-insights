<?php

function bubo_insights_inline_tracking_script() {
    ob_start();
    ?>
    var ajaxurl = "/wp-admin/admin-ajax.php";
    var nonce = jQuery("#bubo_insights_nonce").attr("data-nonce");
    var pagesession = getPagesession();
    var initTimeMs = Date.now();
    var starttime = initTimeMs;
    var sessiontime = 0;
    	
    function buboSessionTimer() {
        sessiontime += ( Math.round( ( Date.now() - starttime ) / 10 ) / 100);
        starttime = Date.now();
    }
    	
    function buboEventLog(e, eventtype) {
        	    
    	var action = "bubo_insights_event_log";
    	var touchenabled = "ontouchstart" in document.documentElement;
    	var scale = window.devicePixelRatio;
    	var screenwidth = screen.availWidth;
    	var screenheight = screen.availHeight;
    	var eventtimeMs = Date.now();
    	var inittime = Math.floor(initTimeMs / 1000)
    	var eventtime = Math.floor(eventtimeMs / 1000);
    	var eventwait = ( Math.round( ( Date.now() - initTimeMs ) / 10 ) / 100);
    	var referrer = document.referrer;
    	var origin = window.location.href;
    	var link = null;
    	var elementtag = null;
    	var elementclass = null;
    	var elementcontent = null;
    
    	if( (eventtype == 'click') || (eventtype == 'tap') ) {
    		link = e.currentTarget.href;
    		elementtag = jQuery(e.target).prop("tagName").toLowerCase();
    		elementclass = e.target.className;
    		if(elementtag == 'img'){
    		    elementcontent = jQuery(e.target).attr('src');
    		}
    		else {
    		    elementcontent = jQuery(e.target)[0].innerText;
    		}
    	}
    	
    	jQuery.ajax(
    		ajaxurl, {
    			method : "POST",
    			dataType : "json",
    			data : {
    				action: 		action,
    				nonce:			nonce,
    				pagesession:    pagesession,
    				inittime:       inittime,
    				sessiontime:    sessiontime,
    				eventtype: 	    eventtype,
    				eventtime: 	    eventtime,
    				eventwait: 	    eventwait,
    				touchenabled:   touchenabled,
    				scale:			scale,
    				screenwidth:    screenwidth,
    				screenheight:   screenheight,
    				referrer: 	    referrer,
    				origin: 		origin,
    				link: 			link,
    				elementtag:     elementtag,
    				elementclass:   elementclass,
    				elementcontent: elementcontent
    			},
    			success: function(response) {
    			console.log(response);
    			},
    			error: function(response) {
    				console.log("!");				 
    			}
    		}
    	);
    	
    }
    
    function getPagesession() {
        const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789~!£$%\/()=?^+*#@°ç-_";
        let result = "";
        for (let i = 0; i < 4; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return result;
    }
    
    jQuery(document).ready( 
        function(initTimex) {
        
        document.addEventListener("visibilitychange", () => {
        	buboEventLog(null, document.visibilityState);
        	if(document.visibilityState == 'hidden') {
        	    buboSessionTimer();
        	}
        	if(document.visibilityState == 'visible') {
        	    starttime = Date.now();
        	}
        });
        
        buboEventLog(null, 'pageload');
        
        jQuery('body').on( "mousedown", "a", function(e) { buboSessionTimer(); buboEventLog(e, 'click'); } );
        jQuery('body').on( "tap", "a", function(e) { buboSessionTimer(); buboEventLog(e, 'tap'); } );
        
        window.onbeforeunload = function(e) { buboSessionTimer(); buboEventLog(null, 'unload'); };  
        
        console.log("Page loaded");
        
        }
    );
    <?php
    $script = ob_get_clean();
    return $script;
}



?>