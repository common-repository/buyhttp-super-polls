<?php
function inlineJs()
{
	?>
	<script type="text/javascript">
		jQuery(function($) {
			newPoll = $( "#addNewPoll" ).dialog({
				autoOpen: false,
				height: 200,
				width: 600,
				modal: true,
			});
			
			optionsDialog = $( "#optionsModal" ).dialog({
				autoOpen: false,
				height: 600,
				width: 600,
				modal: true,
				close: function( event, ui ) {
					$("#optionsForm")[0].reset()
					$(".optionsPAdded").remove();
				}
			});
    
			$(document).on("click", ".editPoll", function () {
				var id = $(this).data('id');
				var question = $(this).data('question');
				$(".pollId").val(id);
				$("#pollQuestion").val(question);
				if(id){
					$("#newPollHeader").html( '<?php echo __('Edit Your Poll','bsppolls'); ?>' );
				}
				newPoll.dialog( "open" );
			});
    
			$(document).on("click", ".editOptions", function () {
				var id = $(this).data('id');
				var options = $(this).data('options');
				var question = $(this).data('question');
				$(".pollId").val( id );
				$("#optionsSpan").html( question );
				$.each(options, function( index, value ) {
					if(value.id == "1"){
						$("#option1").val(stripslashes(value.name));
					} else {
						var n = $('.optionsP').length + 1;
				        var box_html = $('<p class="optionsP optionsPAdded"><label for="option' + n + '"><?php echo __('Option','bsppolls'); ?> <span class="optionNumber">' + n + '</span></label> <input type="text" name="options[]" id="option' + n + '" size="60" value="' + stripslashes(value.name) + '"> <a class="removeOption"><?php echo __('Remove Option','bsppolls'); ?></a></p>');
				        box_html.hide();
				        $('p.optionsP:last').after(box_html);
				        box_html.fadeIn('slow');
					}
				});
				optionsDialog.dialog( "open" );
			});
			
			$(document).on("click", ".addOptionButton", function () {
		        var n = $('.optionsP').length + 1;
		        var box_html = $('<p class="optionsP optionsPAdded"><label for="option' + n + '"><?php echo __('Option','bsppolls'); ?> <span class="optionNumber">' + n + '</span></label> <input type="text" name="options[]" id="option' + n + '" size="60"> <a class="removeOption"><?php echo __('Remove Option','bsppolls'); ?></a></p>');
		        box_html.hide();
		        $('p.optionsP:last').after(box_html);
		        box_html.fadeIn('slow');
		        $('input:text:visible:last').focus();
		        if(n == 25){
		        	alert('triggered');
			        $(".addOptionButton").remove();
		        }
		        return false;
		    });
		    
			$(document).on("click", ".removeOption", function () {
			    $(this).parent().css( 'background-color', '#FF6C6C' );
			    $(this).parent().fadeOut("slow", function() {
			        $(this).remove();
			        $('.box-number').each(function(index){
			            $(this).text( index + 1 );
			        });
			    });
			    return false;
			});
			
			$(document).on("click", ".deletePoll", function () {
				var id = $(this).data('id');
				var nonce = $(this).data('nonce');
				var submit = 'action=deletePoll&id=' + id + '&_wpnonce=' + nonce;
				$.ajax({
					type:'POST',
					data:submit,
					url:'<?php echo admin_url('plugins.php?page=managebsp'); ?>',
					success:function(data) {
						if(data.charAt(0) == '1'){
							location.reload(true);
						}
					}
				});
			});
			
			
		});
		
		function stripslashes (str) {

		  return (str + '').replace(/\\(.?)/g, function (s, n1) {
		    switch (n1) {
		    case '\\':
		      return '\\';
		    case '0':
		      return '\u0000';
		    case '':
		      return '';
		    default:
		      return n1;
		    }
		  });
		}
	</script>
	<?php
}


function frontJs()
{
	?>
	<script type="text/javascript">
		jQuery( "#bspForm" ).submit(function( event ) {
			event.preventDefault();
			var submit = jQuery(this).serialize();
			submit += '&action=bspVote';
			jQuery.ajax({
				type:'POST',
				data:submit,
				url:'<?php echo admin_url('admin-ajax.php'); ?>',
				beforeSend:function(){
					jQuery("#bspSubmitFeedback").html("<img src=\"<?php echo includes_url('images/spinner.gif'); ?>\"> <?php echo __('Submitting','bsppolls'); ?>");
				},
				success:function(data) {
					console.log(data);
					jQuery("#bspSubmitFeedback").html("<div class=\"dashicons dashicons-yes\"></div> <?php echo __('Completed','bsppolls'); ?>");
				}
			});
		});
	</script>
	<?php
}


function loadScripts()
{
	wp_enqueue_style("jquery-ui-css", "http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.min.css");
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-dialog');
}


function frontScripts()
{
	wp_enqueue_script('bsp-chart',plugins_url('chart.min.js', __FILE__));
}
