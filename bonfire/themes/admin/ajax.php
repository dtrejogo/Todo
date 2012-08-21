<div class="scrollable" id="ajax-scroller" style="margin: 18px 0 36px 0">
    <?php
    echo Template::message();
    echo Template::yield();
    ?>
    <br/>
</div>

<script>
    head.js(<?php echo Assets::external_js(null, true) ?>);
    head.js(<?php echo Assets::module_js(true) ?>);
</script>
<?php echo Assets::inline_js(); ?>

<script>
   
    /*
                Ajax form submittal
     */

    $('form.ajax-form').ajaxForm({        
        success:   processJson ,
        dataType:  'json'
    });
	
    /*
                AJAX Setup
     */
    $.ajaxSetup({cache: false});

    /* $('#loader').ajaxStart(function(){
        $('#loader').show();
    });

    $('#loader').ajaxStop(function(){
        $('#loader').hide();
    });*/
    
        
    function processJson(json){
        $('#message').html(json.message);
        $('.fade-me').delay(5000).slideUp(450);
        if (json.success==1){
            if (json.redirect){
                location.href = json.redirect
            }else{
                $('#ajax-content').html(json.view);
            }
            
        }else{
            $('#ajax-content').html(json.view);       
        }
        
        $.ajaxSetup({cache: false});
        $('form.ajax-form').ajaxForm({        
            success:   processJson ,
            dataType:  'json'
        });
    }
</script>