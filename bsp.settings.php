<?php
function bsp_add_admin_menu(  ) { 

	add_options_page( 'BuyHTTP Super Polls', 'BuyHTTP Super Polls', 'manage_options', 'bsppolls', 'bsp_options_page' );

}


function bsp_settings_exist(  ) { 

	if( false == get_option( 'bsp_settings' ) ) { 

		add_option( 'bsp_settings' );

	}

}


function bsp_settings_init(  ) { 

	register_setting( 'pluginPage', 'bsp_settings' );

	add_settings_section(
		'bsp_pluginPage_section', 
		__( 'To be able to display your poll results to visitors you must enter the URL of the page you put your [bsp_show_results] shortcode on', 'bsppolls' ), 
		'bsp_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'pageUrl', 
		__( 'Page or Post ID', 'bsppolls' ), 
		'bsp_text_field_0_render', 
		'pluginPage', 
		'bsp_pluginPage_section' 
	);

	add_settings_field( 
		'chartStyle', 
		__( 'Chart.js style', 'bsppolls' ), 
		'bsp_select_field_0_render', 
		'pluginPage', 
		'bsp_pluginPage_section' 
	);

	add_settings_field( 
		'linkOnWidget', 
		__( 'Show credit link on widget', 'bsppolls' ), 
		'bsp_checkbox_field_1_render', 
		'pluginPage', 
		'bsp_pluginPage_section' 
	);

	add_settings_field( 
		'linkOnResults', 
		__( 'Show credit link on results page', 'bsppolls' ), 
		'bsp_checkbox_field_2_render', 
		'pluginPage', 
		'bsp_pluginPage_section' 
	);

	add_settings_field( 
		'oneVotePerIp', 
		__( 'One vote per IP per poll', 'bsppolls' ), 
		'bsp_checkbox_field_3_render', 
		'pluginPage', 
		'bsp_pluginPage_section' 
	);


}


function bsp_text_field_0_render(  ) { 

	$options = get_option( 'bsp_settings' );
	?>
	<input type='text' name='bsp_settings[pageUrl]' value='<?php echo $options['pageUrl']; ?>'>
	<?php

}


function bsp_select_field_0_render(  ) { 

	$options = get_option( 'bsp_settings' );
	if(empty($options)){
		$options['chartStyle'] = 'doughnut';
	}
	?>
	<select name='bsp_settings[chartStyle]'>
		<option value='Doughnut' <?php selected( $options['chartStyle'], 'Doughnut' ); ?>>Doughnut</option>
		<option value='Pie' <?php selected( $options['chartStyle'], 'Pie' ); ?>>Pie</option>
		<option value='PolarArea' <?php selected( $options['chartStyle'], 'PolarArea' ); ?>>Polar Area</option>
	</select>
	<?php

}


function bsp_checkbox_field_1_render(  ) { 

	$options = get_option( 'bsp_settings' );
	if(empty($options)){
		$options['linkOnWidget'] = '1';
	}
	?>
	<input type='checkbox' name='bsp_settings[linkOnWidget]' <?php checked( $options['linkOnWidget'], 1 ); ?> value='1'>
	<?php

}


function bsp_checkbox_field_2_render(  ) { 

	$options = get_option( 'bsp_settings' );
	if(empty($options)){
		$options['linkOnResults'] = '1';
	}
	?>
	<input type='checkbox' name='bsp_settings[linkOnResults]' <?php checked( $options['linkOnResults'], 1 ); ?> value='1'>
	<?php

}


function bsp_checkbox_field_3_render(  ) { 

	$options = get_option( 'bsp_settings' );
	if(empty($options)){
		$options['oneVotePerIp'] = '1';
	}
	?>
	<input type='checkbox' name='bsp_settings[oneVotePerIp]' <?php checked( $options['oneVotePerIp'], 1 ); ?> value='1'>
	<?php

}


function bsp_settings_section_callback(  ) { 

	echo __( '', 'bsppolls' );

}


function bsp_options_page(  ) { 

	?>
	<form action='options.php' method='post'>
		
		<h2>BuyHTTP Super Polls</h2>
		
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
		
	</form>
	<?php

}