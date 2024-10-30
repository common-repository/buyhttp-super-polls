<?php
function bsp_show_results()
{
	add_action('wp_footer','resultsJs');
	global $wpdb;
	if(isset($_GET['poll_id'])){
		$row = $wpdb->get_row("SELECT poll_id,question,options FROM ".$wpdb->prefix."bsppolls WHERE poll_id=".$_GET['poll_id']);
	} else {
		$row = $wpdb->get_row("SELECT poll_id,question,options FROM ".$wpdb->prefix."bsppolls ORDER BY poll_id DESC LIMIT 1");
	}
	$votes = $wpdb->get_var("SELECT COUNT(vote_id) FROM ".$wpdb->prefix."bsppollvotes WHERE poll_id=".$row->poll_id);
	$options = json_decode($row->options,true);
	$settings = get_option( 'bsp_settings' );
	$polls = $wpdb->get_results("SELECT poll_id,question FROM ".$wpdb->prefix."bsppolls ORDER BY poll_id DESC");
	?>
		<h2><?php echo stripslashes($row->question); ?></h2>
		
		<?php 
			if(isset($options)){
				foreach($options as $option) { 
			$optvotes = $wpdb->get_var("SELECT COUNT(vote_id) FROM ".$wpdb->prefix."bsppollvotes WHERE poll_id=".$row->poll_id." AND option_id=".$option['id']);
		?>
		
		<p><b><?php echo stripslashes($option['name']); ?>:</b> <?php echo $optvotes; ?> vote<?php echo ($optvotes == 1 ? '' : 's'); ?></p>
		<?php 	} 
			}	
		?>
		
		<canvas id="myChart" width="200" height="200"></canvas>
		
		<p><b><?php echo __('Total Votes:','bsppolls'); ?> <?php echo $votes; ?></b></p>
		
		<p>&nbsp;</p>
		
		<h3><?php echo __('Choose another poll','bpspolls'); ?></h3>
		
		<select id="bspPollSelect">
			<?php foreach($polls as $poll){ ?>
			<option value="<?php echo add_query_arg('poll_id',$poll->poll_id,the_permalink()); ?>" <?php echo ($row->poll_id == $poll->poll_id ? 'selected="selected"' : ''); ?>><?php echo stripslashes($poll->question); ?></option>
			<?php } ?>
		</select>
		
		
	<?php
	if(isset($settings['linkOnResults'])){
		echo '<p>Poll plugin by <a href="http://www.buyhttp.com" target="_blank">BuyHTTP</a></p>';
	}
	?>
	<?php
}


function resultsJs()
{
	global $wpdb;
	if(isset($_GET['poll_id'])){
		$row = $wpdb->get_row("SELECT poll_id,question,options FROM ".$wpdb->prefix."bsppolls WHERE poll_id=".$_GET['poll_id']);
	} else {
		$row = $wpdb->get_row("SELECT poll_id,question,options FROM ".$wpdb->prefix."bsppolls ORDER BY poll_id DESC LIMIT 1");
	}
	$votes = $wpdb->get_var("SELECT COUNT(vote_id) FROM ".$wpdb->prefix."bsppollvotes WHERE poll_id=".$row->poll_id);
	$options = json_decode($row->options,true);
	$settings = get_option( 'bsp_settings' );
	?>
	<script>
		window.onload = function(){
			var ctx = document.getElementById("myChart").getContext("2d");
			window.myDoughnut = new Chart(ctx).<?php echo $settings['chartStyle']; ?>(data, {responsive : false});
		};
		var colorarr = [
			['#F7464A','#FF5A5E'],
			['#002EB8','#003DF5'],
			['#33FF66','#66FF33'],
			['#6C847A','#86A495'],
			['#D78D3C','#F2A54E'],
			['#B5A3F7','#C2B8DA'],
			['#D7E2A6','#DCF98F'],
			['#55767B','#98C4CF'],
			['#CB7A82','#CB7AA3'],
			['#00314C','#003D4C'],
			['#84895F','#B9C17F'],
			['#F9EAB6','#FCE9C0'],
			['#85B200','#A0D700'],
			['#216C8A','#3E93B5'],
			['#197065','#22A190'],
			['#22A14E','#2CD467'],
			['#3A8C1F','#52C22D'],
			['#778019','#A7B324'],
			['#CC9741','#F5B54E'],
			['#BA5438','#ED6945'],
			['#BF3750','#F24666'],
			['#C93A95','#ED45AF'],
			['#AE34B3','#DE42E3'],
			['#7339AD','#974BE3'],
			['#6352BA','#7964E3']
		];
		var data = [
			<?php 
			$i=0;
			if(isset($options)){
				foreach($options as $option) { 
				$optvotes = $wpdb->get_var("SELECT COUNT(vote_id) FROM ".$wpdb->prefix."bsppollvotes WHERE poll_id=".$row->poll_id." AND option_id=".$option['id']);
			?>
			{
				value: <?php echo $optvotes; ?>,
		        color: colorarr[<?php echo $i; ?>][0],
		        highlight: colorarr[<?php echo $i; ?>][1],
		        label: "<?php echo stripslashes($option['name']); ?>"
			},
			<?php		
				$i++;
				}
			}
			?>
		];
		
		jQuery( "#bspPollSelect" ).change(function() {
			var id = jQuery(this).val();
			console.log(id);
			window.location=id;
		});
	</script>
	<?php
}