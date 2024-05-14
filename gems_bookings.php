<?php
/**
 * Plugin Name: GEMS Bookings
 * Description: GEMS Bookings form
 * Version: 1.1.0
 * Author: M2-D2
 * Author URI: https://m2-d2.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: gems_bookings
 * Domain Path: /languages/
 * */

define("GEMS_PLUGIN_DIR", plugin_dir_path(__FILE__));
define("GEMS_PLUGIN_VERSION", '1.1.0');

if (!class_exists('Gamajo_Template_Loader')) {
	require plugin_dir_path(__FILE__) . 'includes/class-template-loader.php';
	require plugin_dir_path(__FILE__) . 'includes/class-templates.php';
}

/***********************************************************************
 Default init
 */
add_action( 'init', 'gems_bookings_languages');
add_filter( 'plugin_row_meta', 'gems_bookings_row_meta', 10, 2 );
add_action( 'wp_enqueue_scripts', 'gems_bookings_assets', 9999);
add_filter( 'query_vars', 'gems_query_vars' );

/***********************************************************************
 Admin init
 */
if (is_admin()) {
	add_action('admin_menu', 'gems_bookings_menu');
	add_action('admin_init', 'gems_bookings_register_settings');
	add_action('admin_enqueue_scripts', 'gems_bookings_admin_assets' );
}

function email_template_settings(){
    global $wpdb;
    global $table_prefix;

	$email_settings = get_option('mail_setting_'.get_current_user_id());
	$results = array();

	if (!$email_settings) {
		$email_settings[] = array(
			'emailSubject' 			=> '',
			'emailHeader' 			=> '',
			'emailFooter' 			=> '',
		);
	}
	
	return $email_settings;
}

/***********************************************************************
 Load textdomain
 */
function gems_bookings_languages() {
	load_plugin_textdomain( 'gems_bookings', false, plugin_basename( dirname( __FILE__ ) . '/languages' ) );
}

/***********************************************************************
 Add documentation to plugin description
*/
function gems_bookings_row_meta( $links, $file ) {
	if ( strpos( $file, 'gems_bookings.php' ) !== false ) {
		$new_links = array(
			'<a href="https://www.m2-d2.com/" target="_blank">'.  esc_html__('Documentation', 'gems_bookings') . '</a>'
			);
		
		$links = array_merge( $links, $new_links );
	}
	return $links;
}

/***********************************************************************
 Enqueue assets
*/	
function gems_bookings_assets( $page ) {
	wp_enqueue_script('bootstrap_script', plugins_url('/assets/js/bootstrap.js', __FILE__ ), '', GEMS_PLUGIN_VERSION );
	wp_enqueue_script('bootstrap-datepicker_script', plugins_url('/assets/js/bootstrap-datepicker.js', __FILE__ ), '', GEMS_PLUGIN_VERSION );
	wp_enqueue_script('jquery_select2_script', plugins_url('/assets/js/select2.js', __FILE__ ), '', GEMS_PLUGIN_VERSION );
	wp_enqueue_script('jquery_select2_language_script', plugins_url('/assets/js/i18n/nl.js', __FILE__ ), '', GEMS_PLUGIN_VERSION );
	wp_enqueue_script('gems_bookings_script', plugins_url('/assets/js/gems_bookings.js', __FILE__ ), '', GEMS_PLUGIN_VERSION );

	wp_enqueue_style('bootstrap_style', plugins_url('/assets/css/bootstrap.css', __FILE__ ), '', GEMS_PLUGIN_VERSION );
	wp_enqueue_style('bootstrap-datepicker_style', plugins_url('/assets/css/bootstrap-datepicker.css', __FILE__ ), '', GEMS_PLUGIN_VERSION );
	wp_enqueue_style('select2_style', plugins_url('/assets/css/select2.css', __FILE__ ), '', GEMS_PLUGIN_VERSION );
	wp_enqueue_style('fontawesome_style', plugins_url('/assets/css/fontawesome.css', __FILE__ ), '', GEMS_PLUGIN_VERSION );
	wp_enqueue_style('gems_bookings_style', plugins_url('/assets/css/gems_bookings.css', __FILE__ ), '', GEMS_PLUGIN_VERSION );
}

/***********************************************************************
 Parameter for query vars
*/
function gems_query_vars( $qvars ) {
	$qvars[] = 'event';
	return $qvars;
}

/***********************************************************************
 Define wordpress options menu
*/
function gems_bookings_menu() {
	add_options_page(
		'GEMS Bookings',   // Title in browser tab
		'GEMS',            // Title in settings menu
		'manage_options',  // Capability needed to see this menu
		'gems_bookings',   // Slug
		'gems_bookings_options'
	);
}

/***********************************************************************
 Register settings
*/
function gems_bookings_register_settings() {
	register_setting('gems-settings-group', 'gems_merchant_key');
	register_setting('gems-settings-group', 'gems_img_endpoint');
	register_setting('gems-settings-group', 'gems_api_endpoint');
}

/***********************************************************************
 Enqueue admin assets
*/	
function gems_bookings_admin_assets( $page ) {
	if( $page == 'settings_page_gems_bookings' ) {
	  wp_enqueue_script( 'gems_bookings_admin_script', plugins_url( 'assets/js/gems_bookings.admin.js' , __FILE__ ), array('jquery'), '0.1', true );
  	}
}

/***********************************************************************
 Register shortcodes
 */
function gems_bookings_shortcode($atts) {
	if (is_string($atts)) {
		$atts = array();
	}
	$atts = shortcode_atts(array(
		'type'               => 'form',
		'template'           => '',
	), $atts, 'gems_bookings');

	ob_start();

	try {

		$GEMS_template = new GEMStemplate(get_option('gems_merchant_key'), get_option('gems_api_endpoint'));

		switch ($atts['type']) {
			case 'form':
				$GEMS_template->bookingform($atts);
				break;
			case '-b-':
				// $sportlinkClient->showStandings($atts);
				break;
			case '-c-':
				// $sportlinkClient->showResults($atts);
				break;
			case '-d-':
				// $sportlinkClient->showMatchDetail($atts);
				break;
		}
	} catch (Exception $e) {
		echo '<div class="sportlink-error"><p>Er kan momenteel geen verbinding worden gemaakt met de Sportlink API</p></div>';
	}

	return ob_get_clean();
}
add_shortcode('gems_bookings', 'gems_bookings_shortcode');


function mail_booking_details($booking_details) {
	global $wpdb;
	global $table_prefix;

	if(isset($_POST['action']) && $_POST['action'] == 'mail_booking_details') {
		# get email template settings
		// $table  	= $table_prefix . 'email_template_settings';
		// $sql    	= $wpdb->prepare("SELECT `emailTemplateId`, `emailSubject`, `emailHeader`, `emailFooter`, `userId` FROM $table WHERE userId = '%s'", (int)get_current_user_id());
		// $results 	= $wpdb->get_results($sql);
		// $email_settings		= array();

		$email_settings = get_option('mail_setting_'.get_current_user_id());

		if (!$email_settings) {
			$email_settings[] = array(
				'emailSubject' 			=> '',
				'emailHeader' 			=> '',
				'emailFooter' 			=> '',
			);
		}

		var_dump($email_settings);
		var_dump('====================================');
		var_dump($booking_details);
		// mail booking details
		$name = 'Yanick';
		$email = 'kevineasky@gmail.com';
		$message = '
			<html>
				<head>
					<title>Review Request Reminder</title>
				</head>
				<body>
					<p>'.$email_settings["email_header"].'</p>
					<div id="form_section9" class="collapse" aria-labelledby="heading9" data-parent="#booking_form">
						<div class="card-body">

						<div class="card summary-card">
							<div class="row">
								<div class="box-padding-mob col-12 mb-3 mob-hide summ-head-box">
									<h3 class="form-label-blue"><span class="badge badge-highlight">01</span><span class="summ-heading">bezoekers</span></h3>
								</div>
								<div class="col-12 table-responsive overflow-y-clip mob-hide">

									<div class="row form-fields-rows">
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Volwassene(n)</p>
											<span id="summary_adults_count">0</span>    
										</div>
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Kinderen</p>
											<span id="summary_children_count">0</span>
										</div>
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Baby s</p>
											<span id="summary_children_under_3_count">0</span>
										</div>                                                    
									</div>
								</div>
								<div class="col-12 my-3 mob-hide summ-head-box">
									<h3 class="form-label-blue"><span class="badge badge-highlight">02</span><span class="summ-heading"><!-- -->Bezoekersinformatie</span></h3>
								</div>
								<div class="col-12 table-responsive overflow-y-clip mob-hide">

									<div class="row form-fields-rows">
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Naam</p>
											Groepsleider: <span id="booking_visitor_title_div"></span>&nbsp;<span id="booking_visitor_name_div"></span><br>   
										</div>
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Contactgegevens</p>
											<div class="d-flex">
												<div class="mr-2">
													<i class="fa-solid fa-location-dot"></i>
												</div>
												<div class="address"><span id="booking_visitor_address_div"></span><br></div>
											</div>
										</div>
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Geboortedatum &amp; Nationaliteit</p>
											<span id="booking_visitor_birthdate_div">
										</div>                                                    
									</div>

									<div class="row form-fields-rows">
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Naam</p>
											Thuisblijver: <span id="booking_stayathome_title_div"></span>&nbsp;<span id="booking_stayathome_name_div"></span><br>   
										</div>
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Contactgegevens</p>
											<div class="d-flex">
												<div class="mr-2">
													<i class="fa-solid fa-location-dot"></i>
												</div>
												<div class="address"><span id="booking_stayathome_address_div"></span><br></div>
											</div>
										</div>
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Geboortedatum &amp; Nationaliteit</p>
											<span id="booking_stayathome_birthdate_div">
										</div>                                                    
									</div>

								</div>
								<div class="col-12 my-3 mob-hide summ-head-box">
									<h3 class="form-label-blue"><span class="badge badge-highlight">03</span><span class="summ-heading"><!-- -->Startbewijzen</span></h3>
								</div>
								<div class="col-12 table-responsive overflow-y-clip mob-hide">

									<div class="row form-fields-rows">
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Challenge</p>
										</div>
										<div class="col-md-6 col-lg-8 col-xl-8">
											<p>Aantal startbewijzen</p>
										</div>
									</div>

									<div id="summary_bibs_div">
									</div>

								</div>

								<div class="col-12 my-3 mob-hide summ-head-box">
									<h3 class="form-label-blue"><span class="badge badge-highlight">04</span><span class="summ-heading"><!-- -->Datums</span></h3>
								</div>
								<div class="col-12 table-responsive overflow-y-clip mob-hide">

									<div class="row form-fields-rows">
										<div class="col-md-6 col-lg-8 col-xl-8">
											<p>Vertrek</p>
											<span id="summary_departure_date">-</span>
										</div>
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Aankomst</p>
											<span id="summary_arrival_date">-</span>
										</div>
									</div>

								</div>

								<div class="col-12 my-3 mob-hide summ-head-box">
									<h3 class="form-label-blue"><span class="badge badge-highlight">05</span><span class="summ-heading">Hotel</span></h3>
								</div>
								<div class="col-12 table-responsive overflow-y-clip mob-hide">

									<div class="row form-fields-rows">
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Hotel naam</p>
											<span id="summary_hotel_name">-</span>
										</div>
										<div class="col-md-6 col-lg-8 col-xl-8">
											<p>Prijs</p>
											<span id="summary_room_price">-</span>
										</div>
									</div>

								</div>

								<div class="col-12 my-3 mob-hide summ-head-box">
									<h3 class="form-label-blue"><span class="badge badge-highlight">06</span><span class="summ-heading">Extras</span></h3>
								</div>
								<div class="col-12 mob-hide">
									<h4 class="body-14  regular-400 gray-1 mb-1">Extras van hotel</h4>
								</div>
								<div class="col-12 table-responsive overflow-y-clip mob-hide">

									<div class="row form-fields-rows">
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Opties</p>
										</div>
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Personen</p>
										</div>
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Prijs</p>
										</div>
									</div>

									<div id="summary_extra_div">
									</div>

								</div>
								<div class="col-12 mt-3 mob-hide">
									<h4 class="body-14  regular-400 gray-1 mb-1">Extras buiten het hotel</h4>
								</div>
								<div class="col-12 table-responsive overflow-y-clip mob-hide">

									<div class="row form-fields-rows">
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Opties</p>
										</div>
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Personen</p>
										</div>
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Prijs</p>
										</div>
									</div>

									<div id="summary_nonextra_div">
									</div>


								</div>
								<div class="col-12 my-3 mob-hide summ-head-box">
									<h3 class="form-label-blue"><span class="badge badge-highlight">07</span><span class="summ-heading"><!-- -->Transport</span></h3>
								</div>
								<div class="col-12 table-responsive overflow-y-clip mob-hide">

									<div class="row form-fields-rows">
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Vlucht</p>
										</div>
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Vertrek</p>
										</div>
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Aankomst</p>
										</div>
									</div>

									<div id="summary_flight_div">
									</div>


								</div>
								<div class="col-12 my-3 mob-hide summ-head-box">
									<h3 class="form-label-blue"><span class="badge badge-highlight">08</span><span class="summ-heading"><!-- -->Verzekering</span></h3>
								</div>
								<div class="col-12 table-responsive overflow-y-clip mob-hide">

									<div class="row form-fields-rows">
										<div class="col-md-6 col-lg-4 col-xl-4">
											<p>Verzekering</p>
										</div>
										<div class="col-md-6 col-lg-8 col-xl-8">
											<p>Prijs</p>
										</div>
									</div>

									<div id="summary_insurance_div">
									</div>

								</div>
								<div class="col-12 my-3 box-padding-mob">
									<h3 class="form-label-blue">Overige kosten</h3>
								</div>
								<div class="col-12">
									<div class="row mb-1">
										<div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 body-14 medium-500 gray-6">
											SGR fee
										</div>
										<div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6">
											+ € <span id="booking_sgr_fee_div"></span> per persoon
										</div>
									</div>
									<div class="row mb-1">
										<div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 body-14 medium-500 gray-6">
											Administratiekosten verzekering
										</div>
										<div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6">
											+ <span id="booking_insurance_fee_div"></span> % per verzekering
										</div>
									</div>
									<div class="row">
										<div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 body-14 medium-500 gray-6">
											Calamiteitenfonds
										</div>
										<div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6">
											+ € <span id="booking_calamity_fund_div"></span>
										</div>
									</div>
								</div>
								<div class="col-12">
									<hr>
								</div>
								<div class="col-12">
									<div class="row mb-2">
										<div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 caption text-black">
											Totaal
										</div>
										<div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 caption theme-primary">
											€ <span>0.00</span>
										</div>
									</div>
								</div>
							</div>
					<p>'.$email_settings["email_footer"].'</p>
				</body>
			</html>
		';

		//php mailer variables
		$from = get_option('admin_email');
		$subject = $email_settings['email_subject'];
		$headers = 'From: '. $from . "\r\n" .
			'Reply-To: ' . $email . "\r\n";

		// //Here put your Validation and send mail
		$sent = wp_mail($email, $subject, strip_tags($message), $headers);
			
		// if($sent) {
		// //message sent!       
		// }
		// else  {
		// //message wasn't sent       
		// }
	}
	add_action( 'wp_ajax_mail_booking_details', 'mail_booking_details' );
		
}
/***********************************************************************
 Rendering options page
 */
function gems_bookings_options() {
?>
	<div class="wrap">

		<h2>
			GEMS - Settings
			&nbsp;
    		<a class="add-new-h2 shortcode_copy" shortcode="[gems_bookings]"><?php esc_html_e('Copy shortcode', 'gems_bookings'); ?></a>
		</h2>

		<?php
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';
		?>

		<h2 class="nav-tab-wrapper">
			<a href="?page=gems_bookings&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Settings', 'gems_bookings'); ?></a>
			<a href="?page=gems_bookings&tab=mailtemplate" class="nav-tab <?php echo $active_tab == 'mailtemplate' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Email Template', 'gems_bookings'); ?></a>

		</h2>
	</div>

	<form method="post" action="options.php">
		<?php
		if ($active_tab == 'settings') {
			settings_fields('gems-settings-group');
			do_settings_sections('gems-settings-group');

		?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_html_e('Merchant key', 'gems_bookings'); ?></th>
					<td>
						<input type="text" name="gems_merchant_key" value="<?php echo esc_attr(get_option('gems_merchant_key')); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e('Image endpoint', 'gems_bookings'); ?></th>
					<td>
						<input type="text" name="gems_img_endpoint" value="<?php echo esc_attr(get_option('gems_img_endpoint')); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e('Base API endpoint', 'gems_bookings'); ?></th>
					<td>
						<input type="text" name="gems_api_endpoint" value="<?php echo esc_attr(get_option('gems_api_endpoint')); ?>" />
					</td>
				</tr>

			</table>

		<?php
			submit_button();
		}
			?>
	</form>
	<?php
		if ($active_tab == 'mailtemplate') {
			$template_loader = new GEMS_Template_Loader();
			$template_loader->get_template_part('email-template');
			?>
		      <form action="">
				<h3>Configure your email content</h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Email Subject', 'gems_bookings'); ?></th>
						<td>
							<textarea name="email_subject" id="email_subject" rows="3" cols="100"><?php echo esc_attr(email_template_settings()['email_subject']); ?></textarea>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Email Header', 'gems_bookings'); ?></th>
						<td>
							<textarea name="email_header" id="email_header" rows="6" cols="100"><?php echo esc_attr(email_template_settings()['email_header']); ?></textarea>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Email Footer', 'gems_bookings'); ?></th>
						<td>
							<textarea name="email_footer" id="email_footer" rows="6" cols="100"><?php echo esc_attr(email_template_settings()['email_footer']); ?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row"></th>
						<td>
							<input type="submit" value="Save" id="email-settings">
						</td>
					</tr>
				</table>
			  </form>
			<?php
		}
	?>
	<script>
		var $jQ = jQuery.noConflict();
		jQuery(document).ready(function($) {
			function saveEmailSettings(userId, subject, header, footer) {
				var url = "<?php echo admin_url('admin-ajax.php'); ?>";
				$jQ.ajax({
					method: "POST",
					dataType: "json",
					url:  url,
					data: { action: 'save_email_settings', user_id: userId, email_subject: subject, email_header: header, email_footer: footer },
					success: function(data) {
						// var result = JSON.parse(data);
						alert("Email settings saved successfully");
					},
					error: function(xhr, status, error) {
						if(xhr.status == 200)
							alert('Email settings saved successfully');
						else
							alert('Error saving email settings');
					}
				});
			}

			var saveBtn = document.getElementById('email-settings')
			saveBtn.addEventListener('click', function(e) {
				e.preventDefault();
				var userId = parseInt("<?php echo get_current_user_id(); ?>")
				var subject = document.querySelector('#email_subject').value;
				var header = document.querySelector('#email_header').value;
				var footer = document.querySelector('#email_footer').value;
				if(subject && header && footer)
					saveEmailSettings(userId, subject, header, footer);
				else
					alert('Please fill all fields');
			});
		});
	</script>
<?php
}

// save email template settings
function save_email_settings() {
	global $wpdb;
    global $table_prefix;

	if(isset($_POST['action'])  && $_POST['action'] == 'save_email_settings'){
		$subject = $_POST['email_subject'];
		$header = $_POST['email_header'];
		$footer = $_POST['email_footer'];
		$user_id = $_POST['user_id'];

		$mail_settings = array(
			'email_subject'    => $subject,
			'email_header' => $header,
			'email_footer' => $footer
		);


		// $sql = $wpdb->update(
		// 	$table_prefix.'email_template_settings',
		// 	array(
		// 		'emailSubject'    => $subject,
		// 		'emailHeader' => $header,
		// 		'emailFooter' => $footer
		// 	),
		// 	array('userId' => get_current_user_id())
		// ); 

		// get wp options


		# insert new record if no record found
		// if($sql == 0){
		// 	$sql = $wpdb->insert(
		// 		$table_prefix.'email_template_settings',
		// 		array(
		// 			'userId' => get_current_user_id(),
		// 			'emailSubject'    => $subject,
		// 			'emailHeader' => $header,
		// 			'emailFooter' => $footer
		// 		)
		// 	);
		// }

		if(!get_option('mail_setting_'.$user_id)){
			add_option('mail_setting_'.$user_id, $mail_settings);
		}
		else{
			update_option('mail_setting_'.$user_id, $mail_settings);
		}

		return true;

	}
	return true;
}
add_action( 'wp_ajax_save_email_settings', 'save_email_settings' );


/***********************************************************************
 Template loader
 */
class GEMS_Template_Loader extends Gamajo_Template_Loader {

	protected $filter_prefix = 'gems_bookings';  // Prefix for filter names.

	protected $theme_template_directory = 'gems_bookings'; // Directory name where custom templates for this plugin should be found in the theme.

	protected $plugin_directory = GEMS_PLUGIN_DIR; // Reference to the root directory path of this plugin.

	protected $plugin_template_directory = 'templates'; // Directory name where templates are found in this plugin. e.g. 'templates' or 'includes/templates', etc.
}
