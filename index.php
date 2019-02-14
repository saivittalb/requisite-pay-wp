<?php
/*
Plugin Name: Requisite Pay WP
Plugin URL: https://github.com/saivittalb/requisite-pay-wp
Description: A tool built for making your client pay you by adding opacity to the body tag and decrease it every day until their site completely fades away.
Version: 1.0
Author: Sai Vittal B
Author URL: https://github.com/saivittalb/
*/
function run_req_pay() {
	$options = get_option('req_pay_wp_settings', array() );
	$due = null;
	$deadline = null;
	if(isset($options['req_pay_wp_due_date'])){
		$due = $options['req_pay_wp_due_date'] ;
	}
	if(isset($options['req_pay_wp_deadline'])){
		$deadline = $options['req_pay_wp_deadline'] ;
	}
	if($due && $deadline){ 
	?>
	<script type="text/javascript">
	
		(function(){
			var due_date = new Date('<?php echo esc_attr( $due ); ?>'); //set this variable according to your context
			var days_deadline = <?php echo esc_attr( $deadline ); ?>;  //set this variable according to your context
			
			var current_date = new Date();
			var utc1 = Date.UTC(due_date.getFullYear(), due_date.getMonth(), due_date.getDate());
			var utc2 = Date.UTC(current_date.getFullYear(), current_date.getMonth(), current_date.getDate());
			var days = Math.floor((utc2 - utc1) / (1000 * 60 * 60 * 24));
			
			if(days > 0) {
				var days_late = days_deadline-days;
				var opacity = (days_late*100/days_deadline)/100;
					opacity = (opacity < 0) ? 0 : opacity;
					opacity = (opacity > 1) ? 1 : opacity;
				if(opacity >= 0 && opacity <= 1) {
					document.getElementsByTagName("BODY")[0].style.opacity = opacity;
				}
			}
		})();
	</script>
	<?php
	}
}

add_action( 'wp_footer', 'run_req_pay' );
add_action( 'admin_menu', 'req_pay_wp_add_admin_menu' );
add_action( 'admin_init', 'req_pay_wp_settings_init' );
function req_pay_wp_add_admin_menu(  ) { 
	add_options_page( 'Requisite-Pay-WP', 'Requisite-Pay-WP', 'manage_options', 'requisite-pay-wp', 'req_pay_wp_options_page' );
}
function req_pay_wp_settings_init(  ) { 
	register_setting( 'pluginPage', 'req_pay_wp_settings' );
	add_settings_section(
		'req_pay_wp_pluginPage_section', 
		__( 'Client did not pay?', 'Requisite Pay WP' ), 
		'req_pay_wp_settings_section_callback', 
		'pluginPage'
	);
	add_settings_field( 
		'req_pay_wp_due_date', 
		__( 'Due Date (02/14/2019)', 'Requisite Pay WP' ), 
		'req_pay_wp_due_date_render', 
		'pluginPage', 
		'req_pay_wp_pluginPage_section' 
	);
	add_settings_field( 
		'req_pay_wp_deadline', 
		__( 'Days Deadline - # of days', 'Requisite Pay WP' ), 
		'req_pay_wp_deadline_render', 
		'pluginPage', 
		'req_pay_wp_pluginPage_section' 
	);
}
function req_pay_wp_due_date_render(  ) { 
	$options = get_option( 'req_pay_wp_settings' );
	?>
	<input type='date' name='req_pay_wp_settings[req_pay_wp_due_date]' value='<?php echo $options['req_pay_wp_due_date']; ?>'>
	<?php
}
function req_pay_wp_deadline_render(  ) { 
	$options = get_option( 'req_pay_wp_settings' );
	?>
	<input type='text' name='req_pay_wp_settings[req_pay_wp_deadline]' value='<?php echo $options['req_pay_wp_deadline']; ?>'>
	<?php
}
function req_pay_wp_settings_section_callback(  ) { 
	echo __( 'Add opacity to the body tag and decrease it every day until their site completely fades away.<br>Set a due date and customize the number of days you offer them until the website is fully vanished.<br>Contribute at <a href="https://github.com/saivittalb/requisite-pay-wp">https://github.com/saivittalb/requisite-pay-wp</a><br><h4>This will only work if you set the below values!</h4>', 'Requisite Pay WP' );
}
function req_pay_wp_options_page(  ) { 
	?>
	<form action='options.php' method='post'>

		<h2>Requisite-Pay-WP</h2>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>

	</form>
	<?php
}