<?php

class Kkd_Pff_Rave_Admin {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action('admin_menu', 'kkd_pff_rave_add_settings_page');
		add_action('admin_init', 'kkd_pff_rave_register_setting_page');

		add_action('admin_menu', 'kkd_pff_rave_add_student_page');
		// add_action('admin_post_upload_student_data', 'kkd_pff_rave_upload_student_data');

		function kkd_pff_rave_add_settings_page() {
			add_submenu_page('edit.php?post_type=rave_form', 'Api Keys Settings', 'Api Keys Settings', 'edit_posts', basename(__FILE__), 'kkd_pff_rave_setting_page');
		}

		function kkd_pff_rave_register_setting_page() {
			register_setting( 'kkd-pff-rave-settings-group', 'rave_mode' );
			register_setting( 'kkd-pff-rave-settings-group', 'rave_sandbox_public_key' );
			register_setting( 'kkd-pff-rave-settings-group', 'rave_sandbox_secret_key' );
			register_setting( 'kkd-pff-rave-settings-group', 'rave_live_public_key' );
			register_setting( 'kkd-pff-rave-settings-group', 'rave_live_secret_key' );

			register_setting( 'kkd-pff-rave-settings-group', 'rave_show_validation_value' );
			register_setting( 'kkd-pff-rave-settings-group', 'rave_validation_value_name' );
			register_setting( 'kkd-pff-rave-settings-group', 'rave_validation_param_name' );
			register_setting( 'kkd-pff-rave-settings-group', 'rave_recurring_live_public_key' );
			register_setting( 'kkd-pff-rave-settings-group', 'rave_recurring_live_secret_key' );
		}

		function kkd_pff_rave_add_student_page() {
			add_submenu_page('edit.php?post_type=rave_form', 'Student Record', 'Student Record', 'edit_posts', 'student.php', 'kkd_pff_rave_student_page');
		}

		// function kkd_pff_rave_register_student_page() {
		// 	register_setting( 'kkd-pff-rave-student-group', 'rave_upload' );
		// }

		function kkd_pff_rave_check_selected($name, $txncharge){
			if ($name == $txncharge) {
				$result = "selected";
			}else{
				$result = "";
			}
			return $result;
		}

		function kkd_pff_rave_student_page() {
			echo "<h1>Student Section</h1>";
			echo "<h3>You can upload student data in this section. <span style='color:green'>Only CSV format is allowed.</span><br> Download sample format <a href='../wp-content/plugins/rave-wordpress-payment-forms-with-validation-master/assets/csv_format/sample_format.csv' download>here</a> for easy upload or save your file to <span style='color:green'>.CSV</span> before uploading.</h3>";

			//form to uplodd the Excel sheet to DB
			$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
			$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			?>
			<form action="<?php echo $url ?>" method="post" enctype="multipart/form-data">
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Upload Excel Sheet</th>
						<td>
							<!-- <input type="text" name="text_file"><br> -->
							<input type="file" name="csv_file" id="csv_file" accept=".csv">
							<input type="hidden" name="action" value="upload_student_data" />
						</td>
					</tr>
					<tr valign="top">
						<td>
							<?php submit_button("Upload"); ?>
						</td>
					</tr>
				</table>
			</form>

			<?php
			//setting wordpress table
			global $wpdb;
			$tablename=$wpdb->prefix.'rave_forms_student_table';
			
			// read csv file and upload
			if (isset($_POST['action']) && $_POST['action'] == "upload_student_data") {
				$file = $_FILES['csv_file']['tmp_name'];
				
				if ($file == NULL) {
					
					echo "<span style='color:red'> No File Selected! </span>";
				} else {
					//read file
					$handle = fopen($file, "r");
					// echo "Detected";

					//truncate the table before entering new data
					$wpdb->query('TRUNCATE TABLE '.$tablename);

					//skipping the first line that  has the header
					$i = 0;

					while(($filesop = fgetcsv($handle, 1000, ",")) !== false) {
						$student_id = $filesop[0];
						$student_name = $filesop[1];
						$student_class = $filesop[2];

						// echo "Student ID: ".$tablename."<br>";

						$data=array(
							'student_id' => $student_id, 
							'student_name' => $student_name,
							'student_class' => $student_class
						);
						
						if ($i > 0 ) { // this helps to skip the first part					
							$wpdb->insert( $tablename, $data);
						}

						$i = 1;
						
					}
					echo "<span style='color:green'>Record Inserted Successfully </span>";
				}

			}
			
			//read the student data from the db table
			$db_data = $wpdb->get_results( "SELECT * FROM $tablename" );
			$data = json_decode(json_encode($db_data), true);

			//Display Number of student records in the table
			$rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $tablename" );
			echo "<h2>Number of Student records in the database table is : <u>".$rowcount."</u></h2>";


			?>
			<!-- <table id="example" class="table table-striped table-bordered" style="width:100%">
				<tr>
					<th>Student ID</th>
					<th>Student Name</th>
					<th>Student Class</th>
				</tr> -->
				<?php
				// foreach ($data as $value) {
				// 	echo "<tr>";
				// 	echo "<td>".$value['student_id']."</td>";
				// 	echo "<td>".$value['student_name']."</td>";
				// 	echo "<td>".$value['student_class']."</td>";
				// 	echo "</tr>";
				// }
				?>
			<!-- </table> -->
			<?php 
		}

		//handle file upload
		function kkd_pff_rave_upload_student_data() {
			echo "We reached this page";
			// if ( isset( $_POST['action'] ) && 'upload_csv' == $_POST['action'] ) {

			// 	// // Open the .csv file and get it's contents
			// 	// $myCSV = $_POST['csv_file'];
			// 	// $path = parse_url($myCSV, PHP_URL_PATH);
			// 	// $myCSV = $_SERVER['DOCUMENT_ROOT'] . $path;

			// 	// if(( $fh = @fopen($myCSV, 'r')) !== false) {
			// 	// 	echo "<script>alert('Working Fine');</script>";
			// 	// } else {
			// 	// 	echo "<script>alert('Working Fine');</script>";
			// 	// }

			// } 
		}
		

		function kkd_pff_rave_setting_page() {
			?>
			<h1>Rave Forms API keys settings</h1>
			
        		
        	<h3>Get your Live and Test API keys <a href="https://rave.flutterwave.com" target="_blank">here</a></h3>

			<form method="post" action="options.php">
				<?php 
					settings_fields( 'kkd-pff-rave-settings-group' ); 
					do_settings_sections( 'kkd-pff-rave-settings-group' ); 
				?>
				<table class="form-table rave_setting_page">
					<tr valign="top">
						<th scope="row">Mode</th>
						<td>
							<select class="form-control" name="rave_mode" id="parent_id">
								<option value="sandbox" <?php echo kkd_pff_rave_check_selected('sandbox',esc_attr( get_option('rave_mode') )) ?>>Sandbox Mode</option>
								<option value="live" <?php echo kkd_pff_rave_check_selected('live',esc_attr( get_option('rave_mode') )) ?>>Live Mode</option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">Test Public Key</th>
						<td>
							<input type="text" name="rave_sandbox_public_key" value="<?php echo esc_attr( get_option('rave_sandbox_public_key') ); ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">Test Secret Key</th>
						<td><input type="text" name="rave_sandbox_secret_key" value="<?php echo esc_attr( get_option('rave_sandbox_secret_key') ); ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">Live Public Key</th>
						<td>
							<input type="text" name="rave_live_public_key" value="<?php echo esc_attr( get_option('rave_live_public_key') ); ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">Live Secret Key</th>
						<td><input type="text" name="rave_live_secret_key" value="<?php echo esc_attr( get_option('rave_live_secret_key') ); ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row" colspan="2" ><h3> Validation details </h4></th>
						
					</tr>
					<tr valign="">
						<th scope="row">Validation input field name</th>
						<td>
							<input type="text" name="rave_validation_param_name" value="<?php echo esc_attr( get_option('rave_validation_param_name') ); ?>" />
							
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">Validation value field name</th>
						<td>
							<input type="text" name="rave_validation_value_name" value="<?php echo esc_attr( get_option('rave_validation_value_name') ); ?>" />
							<?php	
								$show_validation_value =  esc_attr( get_option('rave_show_validation_value') );
								if ($show_validation_value == 1) {
									echo '<br><label><input name="rave_show_validation_value" type="checkbox" value="1" checked style="width: 5px !important;min-width: 15px;"> Show validation value </label>';
								}else{
									echo '<br><label><input name="rave_show_validation_value" type="checkbox" value="1" style="width: 5px !important;min-width: 15px;"> Show validation value </label>';
								}
							?>
						</td>
					</tr>
				</table>

			    <?php submit_button(); ?>

			</form>
			<?php
		}

		add_action( 'init', 'register_kkd_pff_rave' );
		function register_kkd_pff_rave() {

		    $labels = array(
		        'name' => _x( 'Rave Forms', 'rave_form' ),
		        'singular_name' => _x( 'Rave Form', 'rave_form' ),
		        'add_new' => _x( 'Add New', 'rave_form' ),
		        'add_new_item' => _x( 'Add Rave Form', 'rave_form' ),
		        'edit_item' => _x( 'Edit Rave Form', 'rave_form' ),
		        'new_item' => _x( 'Rave Form', 'rave_form' ),
		        'view_item' => _x( 'View Rave Form', 'rave_form' ),
		        'search_items' => _x( 'Search Rave Forms', 'rave_form' ),
		        'not_found' => _x( 'No Rave Forms found', 'rave_form' ),
		        'not_found_in_trash' => _x( 'No Rave Forms found in Trash', 'rave_form' ),
		        'parent_item_colon' => _x( 'Parent Rave Form:', 'rave_form' ),
		        'menu_name' => _x( 'Rave Forms', 'rave_form' ),
		    );

		    $args = array(
		        'labels' => $labels,
		        'hierarchical' => true,
		        'description' => 'Rave Forms filterable by genre',
		        'supports' => array( 'title', 'editor'),
		        'public' => true,
		        'show_ui' => true,
		        'show_in_menu' => true,
		        'menu_position' => 5,
		        'menu_icon' => plugins_url('../images/logo.png', __FILE__),
		        'show_in_nav_menus' => true,
		        'publicly_queryable' => true,
		        'exclude_from_search' => false,
		        'has_archive' => false,
		        'query_var' => true,
		        'can_export' => true,
		        'rewrite' => false,
		        'comments' => false,
		        'capability_type' => 'post'
		    );
		    register_post_type( 'rave_form', $args );
		}

		function kkd_pff_rave_add_view_payments($actions, $post){

		    if(get_post_type() === 'rave_form'){
				unset($actions['view']);
				unset($actions['quick edit']);
		        $url = add_query_arg(
		            array(
		              'post_id' => $post->ID,
		              'action' => 'submissions',
		            )
		        );
		    	$actions['export'] = '<a href="' . admin_url('admin.php?page=submissions&form='.$post->ID) . '" target="_blank" >View Payments</a>';
		    }
		    return $actions;
		}
		add_filter( 'page_row_actions', 'kkd_pff_rave_add_view_payments', 10, 2 );


		function kkd_pff_rave_remove_fullscreen( $qtInit ) {
			$qtInit['buttons'] = 'fullscreen';
			return $qtInit;
		}

		add_filter('user_can_richedit', 'kkd_pff_rave_disable_wyswyg');
		function kkd_pff_rave_disable_wyswyg( $default ){
		    global $post_type, $_wp_theme_features;

		    if ($post_type == 'rave_form') {
		        echo "<style>#edit-slug-box,#message p > a{display:none;}</style>";
		      	add_action("admin_print_footer_scripts", "kkd_pff_rave_shortcode_button_script");
		      	add_filter( 'user_can_richedit' , '__return_false', 50 );
		      	add_action( 'wp_dashboard_setup', 'kkd_pff_rave_remove_dashboard_widgets' );
				remove_action( 'media_buttons', 'media_buttons' );
				remove_meta_box( 'postimagediv','post','side' );
				add_filter('quicktags_settings', 'kkd_pff_rave_remove_fullscreen');
			}

		    return $default;
		}

		function kkd_pff_rave_remove_dashboard_widgets() {
			remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );   // Right Now
			remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' ); // Recent Comments
			remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );  // Incoming Links
			remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );   // Plugins
			remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );  // Quick Press
			remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );  // Recent Drafts
			remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );   // WordPress blog
			remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );   // Other WordPress News
			// use 'dashboard-network' as the second parameter to remove widgets from a network dashboard.
		}

		add_filter( 'manage_edit-rave_form_columns', 'kkd_pff_rave_edit_dashboard_header_columns' );
		function kkd_pff_rave_edit_dashboard_header_columns( $columns ) {

			$columns = array(
				'cb' => '<input type="checkbox" />',
				'title' => __( 'Name' ),
				'shortcode' => __( 'Shortcode' ),
				'payments' => __( 'Payments' ),
				'date' => __( 'Date' )
			);

			return $columns;
		}

		add_action( 'manage_rave_form_posts_custom_column', 'kkd_pff_rave_dashboard_table_data', 10, 2 );
		function kkd_pff_rave_dashboard_table_data( $column, $post_id ) {
			global $post,$wpdb;
			$table = $wpdb->prefix . KKD_PFF_RAVE_TABLE;

			switch( $column ) {
				case 'shortcode' :
					echo '<span class="shortcode">
					<input type="text" class="large-text code" value="[rave-form id=&quot;'.$post_id.'&quot;]"
					readonly="readonly" onfocus="this.select();"></span>';

					break;
				case 'payments':

					$count_query = 'select count(*) from '.$table.' WHERE post_id = "'.$post_id.'" AND paid = "1"';
					$num = $wpdb->get_var($count_query);

					echo '<u><a href="'.admin_url('admin.php?page=submissions&form='.$post_id) .'">'. $num.'</a></u>';
					break;
				default :
					break;
			}
		}

		add_filter( 'default_content', 'kkd_pff_rave_editor_content', 10, 2 );
		function kkd_pff_rave_editor_content( $content, $post ) {

		    switch( $post->post_type ) {
		        case 'rave_form':
		            $content = '[text name="Phone Number"]';
		        break;
		        default:
		            $content = '';
		        break;
		    }

		    return $content;
		}


		///// setting up the form for shortcode function
		function kkd_pff_rave_editor_help_metabox( $post ) {

		    do_meta_boxes( null, 'custom-metabox-holder', $post );
		}
		add_action( 'edit_form_after_title', 'kkd_pff_rave_editor_help_metabox' );
		
		function kkd_pff_rave_editor_help_metabox_details( $post ) {
			echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' .
	  			wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

			?>
			<div class="awesome-meta-admin">
				Email and Full Name field is added automatically, no need to include that.<br /><br />
				To make an input field compulsory add <code> required="required" </code> to the shortcode <br />
				To make the input field readonly, add <code> required="readonly" </code> to the shortcode. The <code>readonly</code> only applies to text fields.
				<br /><br />
				It should look like this <code> [text name="Full Name" required="required" ]</code><br /><br />

				<b style="color:red;">Warning:</b> Using the file input field may cause data overload on your server.
				 Be sure you have enough server space before using it. You also have the ability to set file upload limits.

			</div>

		<?php
		}

		function kkd_pff_rave_editor_shortcode_details( $post ) {
			?>
			<p class="description">
				<label for="wpcf7-shortcode">Copy this shortcode and paste it into your post, page, or text widget content:</label>
				<span class="shortcode wp-ui-highlight">
				<input type="text" id="wpcf7-shortcode" onfocus="this.select();" readonly="readonly" class="large-text code" 
				value="[rave-form id=&quot;<?php echo $post->ID; ?>&quot;]"></span>
			</p>

		<?php
		}

		add_action( 'add_meta_boxes', 'kkd_pff_rave_editor_add_extra_metaboxes' );
		function kkd_pff_rave_editor_add_extra_metaboxes() {

			if (isset($_GET['action']) && $_GET['action'] == 'edit') {
				add_meta_box( 'kkd_pff_rave_editor_help_shortcode', 'Paste shortcode on preferred page', 'kkd_pff_rave_editor_shortcode_details', 'rave_form', 'custom-metabox-holder');
			}

			add_meta_box( 'kkd_pff_rave_editor_help_data', 'Help Section', 'kkd_pff_rave_editor_help_metabox_details', 'rave_form', 'custom-metabox-holder');
			add_meta_box('kkd_pff_rave_editor_add_form_data', 'Extra Form Description', 'kkd_pff_rave_editor_add_form_data', 'rave_form', 'normal', 'default');
			add_meta_box('kkd_pff_rave_editor_add_recur_data', 'Recurring Payment', 'kkd_pff_rave_editor_add_recur_data', 'rave_form', 'side', 'default');
			add_meta_box('kkd_pff_rave_editor_add_email_data', 'Email Receipt Settings', 'kkd_pff_rave_editor_add_email_data', 'rave_form', 'normal', 'default');
			add_meta_box('kkd_pff_rave_editor_add_quantity_data', 'Quantity Payment', 'kkd_pff_rave_editor_add_quantity_data', 'rave_form', 'side', 'default');
			add_meta_box('kkd_pff_rave_editor_add_agreement_data', 'Agreement checkbox', 'kkd_pff_rave_editor_add_agreement_data', 'rave_form', 'side', 'default');
				
		}


	  	function kkd_pff_rave_editor_add_form_data() {
		  	global $post;

		  	// Noncename needed to verify where the data originated
		  	echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' .
		  	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

		  	// Get the location data if its already been entered
			$amount = get_post_meta($post->ID, '_amount', true);
			$paybtn = get_post_meta($post->ID, '_paybtn', true);
			$successmsg = get_post_meta($post->ID, '_successmsg', true);
			$txncharge = get_post_meta($post->ID, '_txncharge', true);
			$loggedin = get_post_meta($post->ID, '_loggedin', true);
			$currency = get_post_meta($post->ID, '_currency', true);
			$filelimit = get_post_meta($post->ID, '_filelimit', true);
			$redirect = get_post_meta($post->ID, '_redirect', true);
			$minimum = get_post_meta($post->ID, '_minimum', true);
			$usevariableamount = get_post_meta($post->ID, '_usevariableamount', true);
			$variableamount = get_post_meta($post->ID, '_variableamount', true);
			$hidetitle = get_post_meta($post->ID, '_hidetitle', true);
			$showvalidationsection = get_post_meta($post->ID, '_showvalidationsection', true);

			if ($amount == "") {$amount = 0;}
			if ($filelimit == "") {$filelimit = 2;}
			if ($paybtn == "") {$paybtn = 'Pay';}
			if ($successmsg == "") {$successmsg = 'Thank you for paying!';}
			if ($currency == "") {$currency = 'NGN';}
			if ($txncharge == "") {$txncharge = 'merchant';}
			if ($minimum == "") {$minimum = 0;}
			if ($usevariableamount == "") {$usevariableamount = 0;}
			if ($hidetitle == "") {$hidetitle = 0;}
			if ($showvalidationsection == "") {$showvalidationsection = 0;}
			if ($variableamount == "") {$variableamount = '';}
		  	// Echo out the field
			
				
			if ($hidetitle == 1) {
		  		echo '<label><input name="_hidetitle" type="checkbox" value="1" checked> Hide the form title </label>';
		  	}else{
		  		echo '<label><input name="_hidetitle" type="checkbox" value="1" > Hide the form title </label>';
			  }
			  
		  	echo "<br>";
		  	echo '<p>Currency:</p>';
		  	echo '<select class="form-control" name="_currency" style="width:100%;">
			  		<option value="NGN" '.kkd_pff_rave_check_selected('NGN', $currency).'>NGN</option>
					<option value="GHS" '.kkd_pff_rave_check_selected('GHS', $currency).'>GHS</option>
					<option value="KES" '.kkd_pff_rave_check_selected('KES', $currency).'>KES</option>
					<option value="UGX" '.kkd_pff_rave_check_selected('UGX',$currency).'>UGX</option>
					<option value="ZAR" '.kkd_pff_rave_check_selected('ZAR',$currency).'>ZAR</option>
					<option value="TZS" '.kkd_pff_rave_check_selected('TZS',$currency).'>TZS</option>
					<option value="ZMW" '.kkd_pff_rave_check_selected('ZMW',$currency).'>ZMW</option>
					<option value="RWF" '.kkd_pff_rave_check_selected('RWF',$currency).'>RWF</option>
					<option value="USD" '.kkd_pff_rave_check_selected('USD', $currency).'>USD</option>
					<option value="GBP" '.kkd_pff_rave_check_selected('GBP', $currency).'>GBP</option>
					<option value="EUR" '.kkd_pff_rave_check_selected('EUR', $currency).'>EUR</option>
					<option value="open" '.kkd_pff_rave_check_selected('open',$currency).'>All Currencies</option>
				</select>';

			echo '<p>Amount to be paid(Set 0 for customer input):</p>';
			echo '<input type="number" name="_amount" value="' . $amount  . '" class="widefat pf-number" />';
			  
		  	if ($minimum == 1) {
		  		echo '<br><label><input name="_minimum" type="checkbox" value="1" checked> Make amount minimum payable </label>';
		  	}else{
		  		echo '<br><label><input name="_minimum" type="checkbox" value="1"> Make amount minimum payable </label>';
		  	}
		  	echo '<p>Variable Dropdown Amount:<code><label>Format(option:amount):  Option 1:10000,Option 2:3000 Separate options with "," </code></label></p>';
		  	echo '<input type="text" name="_variableamount" value="' . $variableamount  . '" class="widefat " />';
		  	if ($usevariableamount == 1) {
		  		echo '<br><label><input name="_usevariableamount" type="checkbox" value="1" checked> Use dropdown amount option </label>';
		  	}else{
		  		echo '<br><label><input name="_usevariableamount" type="checkbox" value="1" > Use dropdown amount option </label>';
		  	}
		  	echo '<p>Pay button Description:</p>';
		  	echo '<input type="text" name="_paybtn" value="' . $paybtn  . '" class="widefat" />';
				
			echo '<p>User logged In:</p>';
			echo '<select class="form-control" name="_loggedin" id="parent_id" style="width:100%;">
					<option value="no" '.kkd_pff_rave_check_selected('no',$loggedin).'>User must not be logged in</option>
					<option value="yes"'.kkd_pff_rave_check_selected('yes',$loggedin).'>User must be logged In</option>
				</select>';

		  	echo '<p>Success Message after Payment</p>';
		    echo '<textarea rows="3"  name="_successmsg"  class="widefat" >'.$successmsg.'</textarea>';
			echo '<p>File Upload Limit(MB):</p>';
		  	echo '<input ttype="number" name="_filelimit" value="' . $filelimit  . '" class="widefat  pf-number" />';
		  	echo '<p>Redirect to page link after payment(keep blank to use normal success message):</p>';
			echo '<input ttype="text" name="_redirect" value="' . $redirect  . '" class="widefat" />';

			if ($showvalidationsection == 1) {
				echo '<label><input name="_showvalidationsection" type="checkbox" value="1" checked> Show validation section </label>';
			}else{
				echo '<label><input name="_showvalidationsection" type="checkbox" value="1" > Show validation section </label>';
			}

		}
		  
		function kkd_pff_rave_editor_add_email_data() {
	  		global $post;

			// Noncename needed to verify where the data originated
			echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' .
			wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

	  		// Get the location data if its already been entered
			$subject = get_post_meta($post->ID, '_subject', true);
	  		$heading = get_post_meta($post->ID, '_heading', true);
			$message = get_post_meta($post->ID, '_message', true);
			$sendreceipt = get_post_meta($post->ID, '_sendreceipt', true);
			$sendinvoice = get_post_meta($post->ID, '_sendinvoice', true);

			if ($subject == "") {$subject = 'Thank you for your payment';}
			if ($sendreceipt == "") {$sendreceipt = 'yes';}
			if ($sendinvoice == "") {$sendinvoice = 'yes';}
			if ($heading == "") {$heading = "We've received your payment";}
			if ($message == "") {$message = 'Your payment was received and we appreciate it.';}
	  		// Echo out the field
			echo '<p>Send an invoices when a payment is attempted:</p>';
			echo '<select class="form-control" name="_sendinvoice" id="parent_id" style="width:100%;">
			       <option value="no" '.kkd_pff_rave_check_selected('no',$sendinvoice).'>Don\'t send</option>
			       <option value="yes" '.kkd_pff_rave_check_selected('yes',$sendinvoice).'>Send</option>
			   	</select>';
			echo '<p>Send Email Receipt:</p>';
			echo '<select class="form-control" name="_sendreceipt" id="parent_id" style="width:100%;">
					<option value="no" '.kkd_pff_rave_check_selected('no',$sendreceipt).'>Don\'t send</option>
					<option value="yes" '.kkd_pff_rave_check_selected('yes',$sendreceipt).'>Send</option>
				</select>';
			echo '<p>Email Subject:</p>';
	  		echo '<input type="text" name="_subject" value="' . $subject  . '" class="widefat" />';
			echo '<p>Email Heading:</p>';
	  		echo '<input type="text" name="_heading" value="' . $heading  . '" class="widefat" />';
			echo '<p>Email Body/Message:</p>';
	    	echo '<textarea rows="6"  name="_message"  class="widefat" >'.$message.'</textarea>';

		}
		  
		function kkd_pff_rave_editor_add_recur_data() {
			global $post;

			// Noncename needed to verify where the data originated
			echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' .
			wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

			// Get the location data if its already been entered
			$recur = get_post_meta($post->ID, '_recur', true);
			$recurplan = get_post_meta($post->ID, '_recurplan', true);

			if ($recur == "") {$recur = 'no';}
			if ($recurplan == "") {$recurplan = '';}
			// Echo out the field
			echo '<p>Reccuring Payment:</p>';
			echo '<select class="form-control" name="_recur" style="width:100%;">
					<option value="no" '.kkd_pff_rave_check_selected('no',$recur).'>None</option>
					<option value="optional" '.kkd_pff_rave_check_selected('optional',$recur).'>Optional Recurring</option>
					<option value="fixed" '.kkd_pff_rave_check_selected('fixed',$recur).'>Fixed amount(with plan ID) </option>
				</select>';
			echo '<p>Rave Recur Plan Id:</p>';
			echo '<input type="text" name="_recurplan" value="' . $recurplan  . '" class="widefat" />
				  <small>Plan amount must match amount on extra form description.</small>';

		}

		function kkd_pff_rave_editor_add_quantity_data() {
			global $post;

			// Noncename needed to verify where the data originated
			echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' .
			wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

			// Get the location data if its already been entered
			$usequantity = get_post_meta($post->ID, '_usequantity', true);
			$quantity = get_post_meta($post->ID, '_quantity', true);

			if ($usequantity == "") {$usequantity = 'no';}
			if ($quantity == "") {$quantity = '10';}
			// Echo out the field
			echo '<small>Allow your users pay in multiple quantity</small><p>Quantified Payment:</p>';
			echo '<select class="form-control" name="_usequantity" style="width:100%;">
					<option value="no" '.kkd_pff_rave_check_selected('no',$usequantity).'>No</option>
					<option value="yes" '.kkd_pff_rave_check_selected('yes',$usequantity).'>Yes</option>
				</select>';
			echo '<p>Max payable quantity:</p>';
			echo '<input type="number" name="_quantity" value="' . $quantity  . '" class="widefat  pf-number" /> <small>Your users only get to pay in quantities if the from amount is not set to zero and recur is set to none.</small>';

		}

		function kkd_pff_rave_editor_add_agreement_data() {
			global $post;

			// Noncename needed to verify where the data originated
			echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' .
			wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

			// Get the location data if its already been entered
			$useagreement = get_post_meta($post->ID, '_useagreement', true);
			$agreementlink = get_post_meta($post->ID, '_agreementlink', true);

			if ($useagreement == "") {$useagreement = 'no';}
			if ($agreementlink  == "") {$agreementlink = '';}
			// Echo out the field
			echo '<p>Use agreement checkbox:</p>';
			echo '<select class="form-control" name="_useagreement" style="width:100%;">
					<option value="no" '.kkd_pff_rave_check_selected('no',$useagreement).'>No</option>
					<option value="yes" '.kkd_pff_rave_check_selected('yes',$useagreement).'>Yes</option>
				</select>';
			echo '<p>Agreement Page Link:</p>';
			echo '<input type="text" name="_agreementlink" value="' . $agreementlink  . '" class="widefat" />';

		}
	
		function kkd_pff_rave_save_data($post_id, $post) {

			if ( !wp_verify_nonce( @$_POST['eventmeta_noncename'], plugin_basename(__FILE__) )) {
			return $post->ID;
			}

			// Is the user allowed to edit the post or page?
			if ( !current_user_can( 'edit_post', $post->ID )){
				return $post->ID;
			}
			
		  	$form_meta['_amount'] = $_POST['_amount'];
		  	$form_meta['_hidetitle'] = (isset($_POST['_hidetitle'])) ? $_POST['_hidetitle'] : 0;
		  	$form_meta['_minimum'] = (isset($_POST['_minimum'])) ? $_POST['_minimum'] : 0;

			$form_meta['_variableamount'] = $_POST['_variableamount'];
			$form_meta['_usevariableamount'] = (isset($_POST['_usevariableamount'])) ? $_POST['_usevariableamount'] : 0;

			$form_meta['_paybtn'] = $_POST['_paybtn'];
			$form_meta['_currency'] = $_POST['_currency'];
			$form_meta['_successmsg'] = $_POST['_successmsg'];
			$form_meta['_txncharge'] = 'merchant';
			$form_meta['_loggedin'] = $_POST['_loggedin'];
			$form_meta['_filelimit'] = $_POST['_filelimit'];
			$form_meta['_redirect'] = $_POST['_redirect'];
			///
			$form_meta['_subject'] = $_POST['_subject'];
			$form_meta['_heading'] = $_POST['_heading'];
			$form_meta['_message'] = $_POST['_message'];
			$form_meta['_sendreceipt'] = $_POST['_sendreceipt'];
			///
			$form_meta['_recur'] = $_POST['_recur'];
			$form_meta['_recurplan'] = $_POST['_recurplan'];
			$form_meta['_usequantity'] = $_POST['_usequantity'];
			$form_meta['_quantity'] = $_POST['_quantity'];

			$form_meta['_useagreement'] = $_POST['_useagreement'];
			$form_meta['_showvalidationsection'] = $_POST['_showvalidationsection'];
			$form_meta['_agreementlink'] = $_POST['_agreementlink'];
			
			// Add values of $form_meta as custom fields

			
			foreach ($form_meta as $key => $value) { // Cycle through the $form_meta array!
				if( $post->post_type == 'revision' ) return; // Don't store custom data twice
				$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
				if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
					update_post_meta($post->ID, $key, $value);
				} else { // If the custom field doesn't have a value
					add_post_meta($post->ID, $key, $value);
				}
				if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
			}

		}
		add_action('save_post', 'kkd_pff_rave_save_data', 1, 2);

	}

	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/admin.css', array(), $this->version, 'all' );

	}

	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/rave-forms-admin.js', array( 'jquery' ), $this->version, false );

	}

}

add_action( 'admin_menu', 'kkd_pff_rave_register_newpage' );
function kkd_pff_rave_register_newpage(){
	add_menu_page('rave', 'rave', 'administrator','submissions', 'kkd_pff_rave_payment_submissions');
	remove_menu_page('submissions');
}

function kkd_pff_rave_payment_submissions(){
	$id = $_GET['form'];
	$obj = get_post($id);
	if ($obj->post_type == 'rave_form') {
		$amount = get_post_meta($id,'_amount',true);
		$thankyou = get_post_meta($id,'_successmsg',true);
		$paybtn = get_post_meta($id,'_paybtn',true);
		$loggedin = get_post_meta($id,'_loggedin',true);
		$txncharge = get_post_meta($id,'_txncharge',true);

		$exampleListTable = new Kkd_Pff_Rave_Payments_List_Table();
		$data = $exampleListTable->prepare_items();
		
		?>
		<div id="welcome-panel" class="welcome-panel">
			<div class="welcome-panel-content">
				<h1 style="margin: 0px;"><?php echo $obj->post_title; ?> Payments </h1>
				<p class="about-description">All payments made for this form</p>
				<?php if ($data > 0) { ?>
					
				<form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
				  <input type="hidden" name="action" value="kkd_pff_export_excel">
				  <input type="hidden" name="form_id" value="<?php echo $id; ?>">
				  <button type="submit"  class="button button-primary button-hero load-customize" >Export Data to Excel</button>
				</form>
				<?php } ?>
				
				<br><br>
			</div>
		</div>
		<div class="wrap">
			<div id="icon-users" class="icon32"></div>
			<?php $exampleListTable->display(); ?>
		</div>
		<?php

	}
}

add_action( 'admin_post_kkd_pff_export_excel', 'Kkd_pff_rave_export_excel' );
function Kkd_pff_rave_export_excel() {
	global $wpdb;
	
	$post_id = $_POST['form_id'];
	$obj = get_post($post_id);
	$csv_output = "";
	
	
  	$table = $wpdb->prefix.KKD_PFF_RAVE_TABLE;
	$data = array();
	$alldbdata = $wpdb->get_results("SELECT * FROM $table WHERE (post_id = '".$post_id."' AND paid = '1')  ORDER BY `id` ASC");
	$i = 0;
	
	if (count($alldbdata) > 0) {
		$header = $alldbdata[0];
		$csv_output .= "#,";
		$csv_output .= "Email,";
		$csv_output .= "Amount,";
		$csv_output .= "Mode,";
		$csv_output .= "Reference,";
		$csv_output .= "FLW Reference,";
		$new = json_decode($header->metadata);
		$text = '';

		if (array_key_exists("0", $new)) {
			foreach ($new as $key => $item) {
				$csv_output .= @$item->metaname.",";
			}
		}else{
			if (count($new) > 0) {
				foreach ($new as $key => $item) {
					$csv_output .= $key.",";
				}
			}
		}
		$csv_output .= "\n";
			
		foreach ($alldbdata as $key => $dbdata) {
			$newkey = $key+1;
			$csv_output .= $newkey.",";
			$csv_output .= $dbdata->email.",";
			$csv_output .= $dbdata->currency.' '.$dbdata->amount.",";
			$csv_output .= $dbdata->mode.",";
			$csv_output .= $dbdata->reference.",";
			$csv_output .= $dbdata->flw_reference.",";
			$new = json_decode($dbdata->metadata);
			$text = '';

			if (array_key_exists("0", $new)) {
				foreach ($new as $key => $item) {
					$csv_output .= @$item->metavalue.",";
				}
			}else{
				if (count($new) > 0) {
					foreach ($new as $key => $item) {
						$csv_output .= $item.",";
					}
				}
			}
			$csv_output .= "\n";
		}


		$filename = $obj->post_title."_payments_".date("Y-m-d_H-i",time());
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header( "Content-disposition: filename=".$filename.".csv");
		print $csv_output;
		exit;
	}	
	
    // Handle request then generate response using echo or leaving PHP and using HTML
}

class Kkd_Pff_Rave_Wp_List_Table{
    public function __construct(){
        add_action( 'admin_menu', array($this, 'add_menu_example_list_table_page' ));
	}
	
	public function add_menu_example_list_table_page(){
        add_menu_page( '', '', 'manage_options', 'example-list-table.php', array($this, 'list_table_page') );
	}
	
	public function list_table_page(){
        $exampleListTable = new Example_List_Table();
				$exampleListTable->prepare_items($data);
        ?>
		<div class="wrap">
            <div id="icon-users" class="icon32"></div>
            <?php $exampleListTable->display(); ?>
         </div>
        <?php
    }
}


if( ! class_exists( 'WP_List_Table' ) ) {
   require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
function format_data($data){
	$new = json_decode($data);
	$text = '';
	if (array_key_exists("0", $new)) {
		foreach ($new as $key => $item) {
			$text.= '<b>'.@$item->metaname.": </b> ".@$item->metavalue."<br />";
			// if ($item->type == 'text') {
			// 	$text.= '<b>'.@$item->metaname.": </b> ".@$item->metavalue."<br />";
			// }else{
			// 	$text.= '<b>'.@$item->metaname.": </b>  <a target='_blank' href='".@$item->metavalue."'>link</a><br />";
			// }

		}
	}else{
		$text = '';
		if (count($new) > 0) {
			foreach ($new as $key => $item) {
				$text.= '<b>'.$key.": </b> ".$item."<br />";
			}
		}
	}
	//
	return $text;
}

class Kkd_Pff_Rave_Payments_List_Table extends WP_List_Table{
   	public function prepare_items(){
		$post_id = $_GET['form'];
		$currency = get_post_meta($post_id,'_currency',true);

		global $wpdb;

		$table = $wpdb->prefix.KKD_PFF_RAVE_TABLE;
		$data = array();
		$alldbdata = $wpdb->get_results("SELECT * FROM $table WHERE (post_id = '".$post_id."' AND paid = '1')");

		foreach ($alldbdata as $key => $dbdata) {
			$newkey = $key+1;
			$data[] = array(
				'id'  => $newkey,
				'email' => '<a href="mailto:'.$dbdata->email.'">'.$dbdata->email.'</a>',
				'amount' => $dbdata->currency.'<b>'.number_format($dbdata->amount).'</b>',
				'mode' => $dbdata->mode,
				'reference' => $dbdata->reference,
				'flwReference' => $dbdata->flw_reference,
				'metadata' => format_data($dbdata->metadata),
				'date'  => $dbdata->created_at
			);
		}

		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();
		usort( $data, array( &$this, 'sort_data' ) );
		$perPage = 20;
		$currentPage = $this->get_pagenum();
		$totalItems = count($data);
		$this->set_pagination_args( array(
			'total_items' => $totalItems,
			'per_page'    => $perPage
		) );
		$data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = $data;

		$rows = count($alldbdata);
		return $rows;
	}
	   
	public function get_columns(){
       	$columns = array(
           'id'  => '#',
           'email' => 'Email',
           'amount' => 'Amount',
           'mode' => 'Mode',
           'reference' => 'Transaction Reference',
           'flwReference' => 'FLW Reference',
           'metadata' => 'Data',
           'date'  => 'Date'
       	);
       	return $columns;
	   }
	   
   	/**
    * Define which columns are hidden
    *
    * @return Array
    */
	public function get_hidden_columns(){
		return array();
	}

  	public function get_sortable_columns(){
       return array('email' => array('email', false),'date' => array('date', false),'amount' => array('amount', false));
	   }
	   
   	/**
    * Get the table data
    *
    * @return Array
    */
	private function table_data($data){

		return $data;
	}

	/**
	* Define what data to show on each column of the table
	*
	* @param  Array $item        Data
	* @param  String $column_name - Current column name
	*
	* @return Mixed
	*/
	public function column_default( $item, $column_name ){
		switch( $column_name ) {
			case 'id':
			case 'email':
			case 'amount':
			case 'mode':
			case 'reference':
			case 'flwReference':
			case 'metadata':
			case 'date':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ) ;
		}
	}

     /**
    * Allows you to sort the data by the variables set in the $_GET
    *
    * @return Mixed
    */
	private function sort_data( $a, $b ){
		$orderby = 'date';
		$order = 'desc';
		if(!empty($_GET['orderby'])){
			$orderby = $_GET['orderby'];
		}
		if(!empty($_GET['order'])){
			$order = $_GET['order'];
		}
		$result = strcmp( $a[$orderby], $b[$orderby] );
		if($order === 'asc'){
			return $result;
		}
		return -$result;
	}
}

?>
