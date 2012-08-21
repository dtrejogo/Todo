//--------------------------------------------------------------------
// THIRD-PARTY FUNCTIONS
//--------------------------------------------------------------------

/*	

	jQuery pub/sub plugin by Peter Higgins (dante@dojotoolkit.org)

	Loosely based on Dojo publish/subscribe API, limited in scope. Rewritten blindly.

	Original is (c) Dojo Foundation 2004-2010. Released under either AFL or new BSD, see:
	http://dojofoundation.org/license for more information.

*/	

;
(function(d){

    // the topic/subscription hash
    var cache = {};

    d.publish = function(/* String */topic, /* Array? */args){
        // summary: 
        //		Publish some data on a named topic.
        // topic: String
        //		The channel to publish on
        // args: Array?
        //		The data to publish. Each array item is converted into an ordered
        //		arguments on the subscribed functions. 
        //
        // example:
        //		Publish stuff on '/some/topic'. Anything subscribed will be called
        //		with a function signature like: function(a,b,c){ ... }
        //
        //	|		$.publish("/some/topic", ["a","b","c"]);
        cache[topic] && d.each(cache[topic], function(){
            this.apply(d, args || []);
        });
    };

    d.subscribe = function(/* String */topic, /* Function */callback){
        // summary:
        //		Register a callback on a named topic.
        // topic: String
        //		The channel to subscribe to
        // callback: Function
        //		The handler event. Anytime something is $.publish'ed on a 
        //		subscribed channel, the callback will be called with the
        //		published array as ordered arguments.
        //
        // returns: Array
        //		A handle which can be used to unsubscribe this particular subscription.
        //	
        // example:
        //	|	$.subscribe("/some/topic", function(a, b, c){ /* handle data */ });
        //
        if(!cache[topic]){
            cache[topic] = [];
        }
        cache[topic].push(callback);
        return [topic, callback]; // Array
    };

    d.unsubscribe = function(/* Array */handle){
        // summary:
        //		Disconnect a subscribed function for a topic.
        // handle: Array
        //		The return value from a $.subscribe call.
        // example:
        //	|	var handle = $.subscribe("/something", function(){});
        //	|	$.unsubscribe(handle);
		
        var t = handle[0];
        cache[t] && d.each(cache[t], function(idx){
            if(this == handle[1]){
                cache[t].splice(idx, 1);
            }
        });
    };

})(jQuery);

//--------------------------------------------------------------------
// !COMMON UI FUNCTIONS
//--------------------------------------------------------------------


head.ready(function(){

    /*
		Notification fades
	*/
    $('.fade-me').delay(5000).slideUp(450);
	
    /* 
		Table Stripes
	*/
    $('table tr').filter(':odd').addClass('odd');
	
    /*
		AJAX Setup
	*/
    $.ajaxSetup({
        cache: false
    });
	
    /*$('#loader').ajaxStart(function(){
        $('#loader').show();
    });
	
    $('#loader').ajaxStop(function(){
        $('#loader').hide();
    });
	*/
    /*
		Hook up ajax links
	*/
    /*$('.ajaxify').live('click', function(e) {
        e.preventDefault();
		
        var url = $(this).attr('href');
		
        $('#ajax-content').load(url);
    }); */
    
   
    $(window).bind('hashchange', function () {
        var hash = window.location.hash || '#index';
            
        var params = hash.split("/"); 
        
        switch (params[0]){
            case '#add':
                $('#ajax-content').load(site_url('admin/content/todo/create'));
                break;
            case "#index":
                if (params[1] && params[2]){
                    $('#ajax-content').load(site_url('admin/content/todo/index/'+params[1]+'/'+params[2]));
                }else{
                    $('#ajax-content').load(site_url('admin/content/todo/'));
                }
                  
                break;
            case "#edit":
                $('#ajax-content').load(site_url('admin/content/todo/edit/'+params[1]));  
                break;
        }

    });
  
    $(window).trigger( "hashchange" );
    

});


function delete_record(id){
    
    if (!(confirm('Are you sure you want to delete this todo?'))){
        return;
    }
    
    $.getJSON(site_url('admin/content/todo/delete/'+id), function(data) {
        if(data.success==1){
            $('#row-'+id).remove();
            $('#message').html(data.message);
            $('.fade-me').delay(5000).slideUp(450);
        }else{
            $('#message').html(data.message);
            $('.fade-me').delay(5000).slideUp(450);
        }
                   
    });
    
}

function done_record(id){
    
    if (!(confirm('Are you done with this entry?'))){
        return;
    }
    
    $.getJSON(site_url('admin/content/todo/done/'+id), function(data) {
        if(data.success==1){
            $('#done-'+id).html('yes')
            $('#done-text-'+id).html('Done')
            $('#message').html(data.message);
            $('.fade-me').delay(5000).slideUp(450);
        }else{
            $('#message').html(data.message);
            $('.fade-me').delay(5000).slideUp(450);
        }
                   
    });
    
}

