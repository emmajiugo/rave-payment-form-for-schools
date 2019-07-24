<?php

require_once(ABSPATH . "wp-admin" . '/includes/image.php');
require_once(ABSPATH . "wp-admin" . '/includes/file.php');
require_once(ABSPATH . "wp-admin" . '/includes/media.php');

class Kkd_Pff_Rave_Public {

	private $plugin_name;

	private $version;
	public $public_key;
	public $mode;
	public $secret_key;
	public $base_url;
	public $validation_param_name;
	public $show_validation_value;
	public $validation_value_name;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$mode =  esc_attr( get_option('rave_mode'));

		$this->base_url = 'https://api.ravepay.co';
		$this->mode = $mode;

		if ($mode == 'sandbox') {
			// $this->base_url = 'https://ravesandboxapi.flutterwave.com';
			$this->public_key = esc_attr( get_option('rave_sandbox_public_key') );
			$this->secret_key = esc_attr( get_option('rave_sandbox_secret_key') );

     	} else {

     		// $this->base_url = 'https://api.ravepay.co';
     		$this->public_key = esc_attr( get_option('rave_live_public_key') );
			$this->secret_key = esc_attr( get_option('rave_live_secret_key') );
		}
		 
		$this->validation_param_name = esc_attr( get_option('rave_validation_param_name') );
		$this->validation_value_name = esc_attr( get_option('rave_validation_value_name') );
		$this->show_validation_value = esc_attr( get_option('rave_show_validation_value') );
		
	}
	public function enqueue_styles() {

		 wp_enqueue_style( 'bootstrap', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/bootstrap.min.css', array(), $this->version, 'all' );
		 wp_enqueue_style( $this->plugin_name.'1', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/frontend.css', array(), $this->version, 'all' );
		 wp_enqueue_style( $this->plugin_name.'2', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/font-awesome.min.css', array(), $this->version, 'all' );

	}


	public function enqueue_scripts() {

		wp_register_script('Rave_inline', $this->base_url . '/flwv3-pug/getpaidx/api/flwpbf-inline.js', false, '1');
		wp_enqueue_script('Rave_inline');
		wp_enqueue_script( 'Rave_FJS', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/frontend.js', array( 'jquery' ), $this->version, true ,true  );
		
		wp_enqueue_script( 'rave_frontend', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/frontend_form.js', array( 'jquery' ), $this->version, true ,true  );
		wp_localize_script( 'rave_frontend', 'settings', array('key'=> $this->public_key,'validation_param_name'=> $this->validation_param_name), $this->version,true, true );

	}

}




add_filter ("wp_mail_content_type", "kkd_pff_rave_mail_content_type");
function kkd_pff_rave_mail_content_type() {
	return "text/html";
}
add_filter ("wp_mail_from_name", "kkd_pff_rave_mail_from_name");
function kkd_pff_rave_mail_from_name() {
	$name = get_option( 'blogname' );
	return $name;
}


function kkd_pff_rave_send_receipt($id,$currency,$amount,$name,$email,$code,$metadata){
	//  echo date('F j,Y');
	$user_email = stripslashes($email);
	$subject = get_post_meta($id, '_subject', true);
	$heading = get_post_meta($id, '_heading', true);
	$sitemessage = get_post_meta($id, '_message', true);

	$email_subject =$subject;

		ob_start();
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="format-detection" content="telephone=no">
	<meta name="format-detection" content="date=no">
	<meta name="format-detection" content="address=no">
	<meta name="format-detection" content="email=no">
	<title></title>
	<link href="http://fonts.googleapis.com/css?family=Noto+Sans:400,700" rel="stylesheet" type="text/css">
	<style type="text/css">body{Margin:0;padding:0;min-width:100%}a,#outlook a{display:inline-block}a,a span{text-decoration:none}img{line-height:1;outline:0;border:0;text-decoration:none;-ms-interpolation-mode:bicubic;mso-line-height-rule:exactly}table{border-spacing:0;mso-table-lspace:0;mso-table-rspace:0}td{padding:0}.email_summary{display:none;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden}.font_default,h1,h2,h3,h4,h5,h6,p,a{font-family:Helvetica,Arial,sans-serif}small{font-size:86%;font-weight:normal}.pricing_box_cell small{font-size:74%}.font_default,p{font-size:15px}p{line-height:23px;Margin-top:16px;Margin-bottom:24px}.lead{font-size:19px;line-height:27px;Margin-bottom:16px}.header_cell .column_cell{font-size:20px;font-weight:bold}.header_cell p{margin-bottom:0}h1,h2,h3,h4,h5,h6{Margin-left:0;Margin-right:0;Margin-top:16px;Margin-bottom:8px;padding:0}.line-through{text-decoration:line-through}h1,h2{font-size:26px;line-height:36px;font-weight:bold}.pricing_box h1,.pricing_box h2,.primary_pricing_box h1,.primary_pricing_box h2{line-height:20px;Margin-top:16px;Margin-bottom:0}h3,h4{font-size:22px;line-height:30px;font-weight:bold}h5{font-size:18px;line-height:26px;font-weight:bold}h6{font-size:16px;line-height:24px;font-weight:bold}.primary_btn td,.secondary_btn td{font-size:16px;mso-line-height-rule:exactly}.primary_btn a,.secondary_btn a{font-weight:bold}.email_body{padding:32px 6px;text-align:center}.email_container,.row,.col-1,.col-13,.col-2,.col-3{display:inline-block;width:100%;vertical-align:top;text-align:center}.email_container{width:100%;margin:0 auto}.email_container{max-width:588px}.row,.col-3{max-width:580px}.col-1{max-width:190px}.col-2{max-width:290px}.col-13{max-width:390px}.row{margin:0 auto}.column{width:100%;vertical-align:top}.column_cell{padding:16px;text-align:center;vertical-align:top}.col-bottom-0 .column_cell{padding-bottom:0}.col-top-0 .column_cell{padding-top:0}.email_container,.header_cell,.jumbotron_cell,.content_cell,.footer_cell,.image_responsive{font-size:0!important;text-align:center}.header_cell,.footer_cell{padding-bottom:16px}.header_cell .column_cell,.footer_cell .col-13 .column_cell,.footer_cell .col-1 .column_cell{text-align:left;padding-top:16px}.header_cell img{max-width:156px;height:auto}.footer_cell{text-align:center}.footer_cell p{Margin:16px 0}.invoice_cell .column_cell{text-align:left;padding-top:0;padding-bottom:0}.invoice_cell p{margin-top:8px;margin-bottom:16px}.pricing_box{border-collapse:separate;padding:10px 16px}.primary_pricing_box{border-collapse:separate;padding:18px 16px}.text_quote .column_cell{border-left:4px solid;text-align:left;padding-right:0;padding-top:0;padding-bottom:0}.primary_btn,.secondary_btn{clear:both;margin:0 auto}.primary_btn td,.secondary_btn td{text-align:center;vertical-align:middle;padding:12px 24px}.primary_btn a,.primary_btn span,.secondary_btn a,.secondary_btn span{text-align:center;display:block}.label .font_default{font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:2px;padding:3px 7px;white-space:nowrap}.icon_holder,.hruler{width:62px;margin-left:auto;margin-right:auto;clear:both}.icon_holder{width:48px}.hspace,.hruler_cell{font-size:0;height:8px;overflow:hidden}.hruler_cell{height:4px;line-height:4px}.icon_cell{font-size:0;line-height:1;padding:8px;height:48px}.product_row{padding:0 0 16px}.product_row .column_cell{padding:16px 16px 0}.product_row .col-13 .column_cell{text-align:left}.product_row h6{Margin-top:0}.product_row p{Margin-top:8px;Margin-bottom:8px}.order_total_right .column_cell{text-align:right}.order_total_left .column_cell{text-align:left}.order_total p{Margin:8px 0}.order_total h2{Margin:8px 0}.image_responsive img{display:block;width:100%;height:auto;max-width:580px;margin-left:auto;margin-right:auto}body,.email_body,.header_cell,.content_cell,.footer_cell{background-color:#fff}.secondary_btn td,.icon_primary .icon_cell,.primary_pricing_box{background-color:#2f68b4}.jumbotron_cell,.pricing_box{background-color:#f2f2f5}.primary_btn td,.label .font_default{background-color:#22aaa0}.icon_secondary .icon_cell{background-color:#e1e3e7}.label_1 .font_default{background-color:#62a9dd}.label_2 .font_default{background-color:#8965ad}.label_3 .font_default{background-color:#df6164}.primary_btn a,.primary_btn span,.secondary_btn a,.secondary_btn span,.label .font_default,.primary_pricing_box,.primary_pricing_box h1,.primary_pricing_box small{color:#fff}h2,h4,h5,h6{color:#383d42}.column_cell{color:#888}.header_cell .column_cell,.header_cell a,.header_cell a span,h1,h3,a,a span,.text-secondary,.column_cell .text-secondary,.content_cell h2 .text-secondary{color:#2f68b4}.footer_cell a,.footer_cell a span{color:#7a7a7a}.text-muted,.footer_cell .column_cell,.content h4 span,.content h3 span{color:#b3b3b5}.header_cell,.footer_cell{border-top:4px solid;border-bottom:4px solid}.header_cell,.footer_cell,.jumbotron_cell,.content_cell{border-left:4px solid;border-right:4px solid}.footer_cell,.product_row,.order_total{border-top:1px solid}.header_cell,.footer_cell,.jumbotron_cell,.content_cell,.product_row,.order_total,.icon_secondary .icon_cell,.footer_cell,.content .product_row,.content .order_total,.pricing_box,.text_quote .column_cell{border-color:#d8dde4}@media screen{h1,h2,h3,h4,h5,h6,p,a,.font_default{font-family:"Noto Sans",Helvetica,Arial,sans-serif!important}.primary_btn td,.secondary_btn td{padding:0!important}.primary_btn a,.secondary_btn a{padding:12px 24px!important}}@media screen and (min-width:631px) and (max-width:769px){.col-1,.col-2,.col-3,.col-13{float:left!important}.col-1{width:200px!important}.col-2{width:300px!important}}@media screen and (max-width:630px){.jumbotron_cell{background-size:cover!important}.row,.col-1,.col-13,.col-2,.col-3{max-width:100%!important}}</style>
	</head>
	<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="margin:0;padding:0;min-width:100%;background-color:#fff">
	<div class="email_body" style="padding:32px 6px;text-align:center;background-color:#fff">
	
	<div class="email_container" style="display:inline-block;width:100%;vertical-align:top;text-align:center;margin:0 auto;max-width:588px;font-size:0!important">
	<table class="header" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0">
	<tbody>
	<tr>
	<td class="header_cell col-bottom-0" align="center" valign="top" style="padding:0;text-align:center;padding-bottom:16px;border-top:4px solid;border-bottom:0 solid;background-color:#fff;border-left:4px solid;border-right:4px solid;border-color:#d8dde4;font-size:0!important">
	
	</td>
	</tr>
	</tbody>
	</table>
	<table class="content" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0">
	<tbody>
	<tr>
	<td class="content_cell" align="center" valign="top" style="padding:0;text-align:center;background-color:#fff;border-left:4px solid;border-right:4px solid;border-color:#d8dde4;font-size:0!important">
	
	<div class="row" style="display:inline-block;width:100%;vertical-align:top;text-align:center;max-width:580px;margin:0 auto">
	
	<div class="col-3" style="display:inline-block;width:100%;vertical-align:top;text-align:center;max-width:580px">
	<table class="column" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;width:100%;vertical-align:top">
	<tbody>
	<tr>
	<td class="column_cell font_default" align="center" valign="top" style="padding:16px;font-family:Helvetica,Arial,sans-serif;font-size:15px;text-align:center;vertical-align:top;color:#888">
	<p style="font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:23px;margin-top:16px;margin-bottom:24px">&nbsp; </p>
	<h5 style="font-family:Helvetica,Arial,sans-serif;margin-left:0;margin-right:0;margin-top:16px;margin-bottom:8px;padding:0;font-size:18px;line-height:26px;font-weight:bold;color:#383d42"><?php echo $heading; ?></h5>
	<p align="left" style="font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:23px;margin-top:16px;margin-bottom:24px">Hello <?php echo strstr($name." ", " ", true); ?>,</p>
	<p align="left" style="font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:23px;margin-top:16px;margin-bottom:24px"><?php echo $sitemessage; ?></p>
	<p style="font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:23px;margin-top:16px;margin-bottom:24px">&nbsp; </p>
	</td>
	</tr>
	</tbody>
	</table>
	</div>
	
	</div>
	
	</td>
	</tr>
	</tbody>
	</table>
	<table class="jumbotron" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0">
	<tbody>
	<tr>
	<td class="jumbotron_cell invoice_cell" align="center" valign="top" style="padding:0;text-align:center;background-color:#fafafa;font-size:0!important">
	
	<div class="row" style="display:inline-block;width:100%;vertical-align:top;text-align:center;max-width:580px;margin:0 auto">
	
	<div class="col-3" style="display:inline-block;width:100%;vertical-align:top;text-align:left">
	<table class="column" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;width:100%;vertical-align:top">
	<tbody>
	<tr>
	<td class="column_cell font_default" align="center" valign="top" style="padding:16px;font-family:Helvetica,Arial,sans-serif;font-size:15px;text-align:left;vertical-align:top;color:#888;padding-top:0;padding-bottom:0">
	<table class="label" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0">
	<tbody>
	<tr>
	<td class="hspace" style="padding:0;font-size:0;height:8px;overflow:hidden">&nbsp;</td>
	</tr>
	<tr>
	<td class="hspace" style="padding:0;font-size:0;height:8px;overflow:hidden">&nbsp;</td>
	</tr>
	<tr>
	<td class="font_default" style="padding:3px 7px;font-family:Helvetica,Arial,sans-serif;font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:2px;-webkit-border-radius:2px;border-radius:2px;white-space:nowrap;background-color:#666;color:#fff">Your Details</td>
	</tr>
	</tbody>
	</table>
	<p style="font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:23px;margin-top:8px;margin-bottom:16px">
		Amount <strong> : <?php echo $currency.' '.number_format($amount); ?></strong><br>
		Email <strong> :  <?php echo $user_email; ?></strong><br>
		<?php
		$new = json_decode($metadata);
		if (array_key_exists("0", $new)) {
			foreach ($new as $key => $item) {
				if ($item->type == 'text') {
					echo $item->display_name."<strong>  :".$item->value."</strong><br>";
				}else{
					echo $item->display_name."<strong>  : <a target='_blank' href='".$item->value."'>link</a></strong><br>";
				}

			}
		}else{
			$text = '';
			if (count($new) > 0) {
				foreach ($new as $key => $item) {
					echo $key."<strong>  :".$item."</strong><br />";
				}
			}
		}
		?>
		Transaction code: <strong> <?php echo $code; ?></strong><br>
	</p>
	</td>
	</tr>
	</tbody>
	</table>
	</div>
	</div>
	
	</td>
	</tr>
	</tbody>
	</table>
	<table class="jumbotron" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0">
	<tbody>
	<tr>
	<td class="jumbotron_cell product_row" align="center" valign="top" style="padding:0 0 16px;text-align:center;background-color:#f2f2f5;border-left:4px solid;border-right:4px solid;border-top:1px solid;border-color:#d8dde4;font-size:0!important">
	
	<div class="row" style="display:inline-block;width:100%;vertical-align:top;text-align:center;max-width:580px;margin:0 auto">
	
	<div class="col-3" style="display:inline-block;width:100%;vertical-align:top;text-align:center;max-width:580px">
	<table class="column" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;width:100%;vertical-align:top">
	<tbody>
	<tr>
	<td class="column_cell font_default" align="center" valign="top" style="padding:16px 16px 0;font-family:Helvetica,Arial,sans-serif;font-size:15px;text-align:center;vertical-align:top;color:#888">
	<small style="font-size:86%;font-weight:normal"><strong>Notice</strong><br>
	You're getting this email because you've made a payment of <?php $currency.' '.number_format($amount); ?> to <a href="<?php echo get_bloginfo('url') ?>" style="display:inline-block;text-decoration:none;font-family:Helvetica,Arial,sans-serif;color:#2f68b4"><?php echo get_option( 'blogname' );?></a>.</small>
	</td>
	</tr>
	</tbody>
	</table>
	</div>
	
	</div>
	</td>
	</tr>
	</tbody>
	</table>
	<table class="footer" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0">
	<tbody>
	<tr>
	<td class="footer_cell" align="center" valign="top" style="padding:0;text-align:center;padding-bottom:16px;border-top:1px solid;border-bottom:4px solid;background-color:#fff;border-left:4px solid;border-right:4px solid;border-color:#d8dde4;font-size:0!important">
	<div class="row" style="display:inline-block;width:100%;vertical-align:top;text-align:center;max-width:580px;margin:0 auto">
	<div class="col-13 col-bottom-0" style="display:inline-block;width:100%;vertical-align:top;text-align:center;max-width:390px">
	<table class="column" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;width:100%;vertical-align:top">
	<tbody>
	<tr>
	<td class="column_cell font_default" align="center" valign="top" style="padding:16px;font-family:Helvetica,Arial,sans-serif;font-size:15px;text-align:left;vertical-align:top;color:#b3b3b5;padding-bottom:0;padding-top:16px">
	<strong><?php echo get_option( 'blogname' );?></strong><br>
	</td>
	</tr>
	</tbody>
	</table>
	</div>
	<div class="col-1 col-bottom-0" style="display:inline-block;width:100%;vertical-align:top;text-align:center;max-width:190px">
	<table class="column" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;width:100%;vertical-align:top">
	<tbody>
	<tr>
	<td class="column_cell font_default" align="center" valign="top" style="padding:16px;font-family:Helvetica,Arial,sans-serif;font-size:15px;text-align:left;vertical-align:top;color:#b3b3b5;padding-bottom:0;padding-top:16px">
	</td>
	</tr>
	</tbody>
	</table>
	</div>
	</div>
	</td>
	</tr>
	</tbody>
	</table>
	</div>
	</div>
	</body>
	</html>

	<?php

	$message = ob_get_contents();
	ob_end_clean();
	$admin_email = get_option('admin_email');
	$website = get_option('blogname');
	$headers = array( 'Reply-To: ' . $admin_email,"From: $website <$admin_email>" . "\r\n");
	$headers = "From: ".$website."<$admin_email>" . "\r\n";
	wp_mail($user_email, $email_subject, $message,$headers);

}
function kkd_pff_rave_send_receipt_owner($id,$currency,$amount,$name,$email,$code,$metadata){
	//  echo date('F j,Y');
	$user_email = stripslashes($email);
	$subject = "You just received a payment";
	$heading = get_post_meta($id, '_heading', true);
	$sitemessage = get_post_meta($id, '_message', true);

	$email_subject =$subject;

		ob_start();
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="format-detection" content="telephone=no">
	<meta name="format-detection" content="date=no">
	<meta name="format-detection" content="address=no">
	<meta name="format-detection" content="email=no">
	<title></title>
	<link href="http://fonts.googleapis.com/css?family=Noto+Sans:400,700" rel="stylesheet" type="text/css">
	<style type="text/css">body{Margin:0;padding:0;min-width:100%}a,#outlook a{display:inline-block}a,a span{text-decoration:none}img{line-height:1;outline:0;border:0;text-decoration:none;-ms-interpolation-mode:bicubic;mso-line-height-rule:exactly}table{border-spacing:0;mso-table-lspace:0;mso-table-rspace:0}td{padding:0}.email_summary{display:none;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden}.font_default,h1,h2,h3,h4,h5,h6,p,a{font-family:Helvetica,Arial,sans-serif}small{font-size:86%;font-weight:normal}.pricing_box_cell small{font-size:74%}.font_default,p{font-size:15px}p{line-height:23px;Margin-top:16px;Margin-bottom:24px}.lead{font-size:19px;line-height:27px;Margin-bottom:16px}.header_cell .column_cell{font-size:20px;font-weight:bold}.header_cell p{margin-bottom:0}h1,h2,h3,h4,h5,h6{Margin-left:0;Margin-right:0;Margin-top:16px;Margin-bottom:8px;padding:0}.line-through{text-decoration:line-through}h1,h2{font-size:26px;line-height:36px;font-weight:bold}.pricing_box h1,.pricing_box h2,.primary_pricing_box h1,.primary_pricing_box h2{line-height:20px;Margin-top:16px;Margin-bottom:0}h3,h4{font-size:22px;line-height:30px;font-weight:bold}h5{font-size:18px;line-height:26px;font-weight:bold}h6{font-size:16px;line-height:24px;font-weight:bold}.primary_btn td,.secondary_btn td{font-size:16px;mso-line-height-rule:exactly}.primary_btn a,.secondary_btn a{font-weight:bold}.email_body{padding:32px 6px;text-align:center}.email_container,.row,.col-1,.col-13,.col-2,.col-3{display:inline-block;width:100%;vertical-align:top;text-align:center}.email_container{width:100%;margin:0 auto}.email_container{max-width:588px}.row,.col-3{max-width:580px}.col-1{max-width:190px}.col-2{max-width:290px}.col-13{max-width:390px}.row{margin:0 auto}.column{width:100%;vertical-align:top}.column_cell{padding:16px;text-align:center;vertical-align:top}.col-bottom-0 .column_cell{padding-bottom:0}.col-top-0 .column_cell{padding-top:0}.email_container,.header_cell,.jumbotron_cell,.content_cell,.footer_cell,.image_responsive{font-size:0!important;text-align:center}.header_cell,.footer_cell{padding-bottom:16px}.header_cell .column_cell,.footer_cell .col-13 .column_cell,.footer_cell .col-1 .column_cell{text-align:left;padding-top:16px}.header_cell img{max-width:156px;height:auto}.footer_cell{text-align:center}.footer_cell p{Margin:16px 0}.invoice_cell .column_cell{text-align:left;padding-top:0;padding-bottom:0}.invoice_cell p{margin-top:8px;margin-bottom:16px}.pricing_box{border-collapse:separate;padding:10px 16px}.primary_pricing_box{border-collapse:separate;padding:18px 16px}.text_quote .column_cell{border-left:4px solid;text-align:left;padding-right:0;padding-top:0;padding-bottom:0}.primary_btn,.secondary_btn{clear:both;margin:0 auto}.primary_btn td,.secondary_btn td{text-align:center;vertical-align:middle;padding:12px 24px}.primary_btn a,.primary_btn span,.secondary_btn a,.secondary_btn span{text-align:center;display:block}.label .font_default{font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:2px;padding:3px 7px;white-space:nowrap}.icon_holder,.hruler{width:62px;margin-left:auto;margin-right:auto;clear:both}.icon_holder{width:48px}.hspace,.hruler_cell{font-size:0;height:8px;overflow:hidden}.hruler_cell{height:4px;line-height:4px}.icon_cell{font-size:0;line-height:1;padding:8px;height:48px}.product_row{padding:0 0 16px}.product_row .column_cell{padding:16px 16px 0}.product_row .col-13 .column_cell{text-align:left}.product_row h6{Margin-top:0}.product_row p{Margin-top:8px;Margin-bottom:8px}.order_total_right .column_cell{text-align:right}.order_total_left .column_cell{text-align:left}.order_total p{Margin:8px 0}.order_total h2{Margin:8px 0}.image_responsive img{display:block;width:100%;height:auto;max-width:580px;margin-left:auto;margin-right:auto}body,.email_body,.header_cell,.content_cell,.footer_cell{background-color:#fff}.secondary_btn td,.icon_primary .icon_cell,.primary_pricing_box{background-color:#2f68b4}.jumbotron_cell,.pricing_box{background-color:#f2f2f5}.primary_btn td,.label .font_default{background-color:#22aaa0}.icon_secondary .icon_cell{background-color:#e1e3e7}.label_1 .font_default{background-color:#62a9dd}.label_2 .font_default{background-color:#8965ad}.label_3 .font_default{background-color:#df6164}.primary_btn a,.primary_btn span,.secondary_btn a,.secondary_btn span,.label .font_default,.primary_pricing_box,.primary_pricing_box h1,.primary_pricing_box small{color:#fff}h2,h4,h5,h6{color:#383d42}.column_cell{color:#888}.header_cell .column_cell,.header_cell a,.header_cell a span,h1,h3,a,a span,.text-secondary,.column_cell .text-secondary,.content_cell h2 .text-secondary{color:#2f68b4}.footer_cell a,.footer_cell a span{color:#7a7a7a}.text-muted,.footer_cell .column_cell,.content h4 span,.content h3 span{color:#b3b3b5}.header_cell,.footer_cell{border-top:4px solid;border-bottom:4px solid}.header_cell,.footer_cell,.jumbotron_cell,.content_cell{border-left:4px solid;border-right:4px solid}.footer_cell,.product_row,.order_total{border-top:1px solid}.header_cell,.footer_cell,.jumbotron_cell,.content_cell,.product_row,.order_total,.icon_secondary .icon_cell,.footer_cell,.content .product_row,.content .order_total,.pricing_box,.text_quote .column_cell{border-color:#d8dde4}@media screen{h1,h2,h3,h4,h5,h6,p,a,.font_default{font-family:"Noto Sans",Helvetica,Arial,sans-serif!important}.primary_btn td,.secondary_btn td{padding:0!important}.primary_btn a,.secondary_btn a{padding:12px 24px!important}}@media screen and (min-width:631px) and (max-width:769px){.col-1,.col-2,.col-3,.col-13{float:left!important}.col-1{width:200px!important}.col-2{width:300px!important}}@media screen and (max-width:630px){.jumbotron_cell{background-size:cover!important}.row,.col-1,.col-13,.col-2,.col-3{max-width:100%!important}}</style>
	</head>
	<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="margin:0;padding:0;min-width:100%;background-color:#fff">
	<div class="email_body" style="padding:32px 6px;text-align:center;background-color:#fff">
	
	<div class="email_container" style="display:inline-block;width:100%;vertical-align:top;text-align:center;margin:0 auto;max-width:588px;font-size:0!important">
	<table class="header" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0">
	<tbody>
	<tr>
	<td class="header_cell col-bottom-0" align="center" valign="top" style="padding:0;text-align:center;padding-bottom:16px;border-top:4px solid;border-bottom:0 solid;background-color:#fff;border-left:4px solid;border-right:4px solid;border-color:#d8dde4;font-size:0!important">
	
	</td>
	</tr>
	</tbody>
	</table>
	<table class="content" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0">
	<tbody>
	<tr>
	<td class="content_cell" align="center" valign="top" style="padding:0;text-align:center;background-color:#fff;border-left:4px solid;border-right:4px solid;border-color:#d8dde4;font-size:0!important">
	
	<div class="row" style="display:inline-block;width:100%;vertical-align:top;text-align:center;max-width:580px;margin:0 auto">
	
	<div class="col-3" style="display:inline-block;width:100%;vertical-align:top;text-align:center;max-width:580px">
	<table class="column" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;width:100%;vertical-align:top">
	<tbody>
	<tr>
	<td class="column_cell font_default" align="center" valign="top" style="padding:16px;font-family:Helvetica,Arial,sans-serif;font-size:15px;text-align:center;vertical-align:top;color:#888">
	<p style="font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:23px;margin-top:16px;margin-bottom:24px">&nbsp; </p>
	<h5 style="font-family:Helvetica,Arial,sans-serif;margin-left:0;margin-right:0;margin-top:16px;margin-bottom:8px;padding:0;font-size:18px;line-height:26px;font-weight:bold;color:#383d42">You just received a payment</h5>
	</td>
	</tr>
	</tbody>
	</table>
	</div>
	
	</div>
	
	</td>
	</tr>
	</tbody>
	</table>
	<table class="jumbotron" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0">
	<tbody>
	<tr>
	<td class="jumbotron_cell invoice_cell" align="center" valign="top" style="padding:0;text-align:center;background-color:#fafafa;font-size:0!important">
	
	<div class="row" style="display:inline-block;width:100%;vertical-align:top;text-align:center;max-width:580px;margin:0 auto">
	
	<div class="col-3" style="display:inline-block;width:100%;vertical-align:top;text-align:left">
	<table class="column" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;width:100%;vertical-align:top">
	<tbody>
	<tr>
	<td class="column_cell font_default" align="center" valign="top" style="padding:16px;font-family:Helvetica,Arial,sans-serif;font-size:15px;text-align:left;vertical-align:top;color:#888;padding-top:0;padding-bottom:0">
	<table class="label" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0">
	<tbody>
	<tr>
	<td class="hspace" style="padding:0;font-size:0;height:8px;overflow:hidden">&nbsp;</td>
	</tr>
	<tr>
	<td class="hspace" style="padding:0;font-size:0;height:8px;overflow:hidden">&nbsp;</td>
	</tr>
	<tr>
	<td class="font_default" style="padding:3px 7px;font-family:Helvetica,Arial,sans-serif;font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:2px;-webkit-border-radius:2px;border-radius:2px;white-space:nowrap;background-color:#666;color:#fff">Payment Details</td>
	</tr>
	</tbody>
	</table>
	<p style="font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:23px;margin-top:8px;margin-bottom:16px">
		Amount <strong> : <?php echo $currency.' '.number_format($amount); ?></strong><br>
		Email <strong> :  <?php echo $user_email; ?></strong><br>
		<?php
		$new = json_decode($metadata);
		if (array_key_exists("0", $new)) {
			foreach ($new as $key => $item) {
				if ($item->type == 'text') {
					echo $item->display_name."<strong>  :".$item->value."</strong><br>";
				}else{
					echo $item->display_name."<strong>  : <a target='_blank' href='".$item->value."'>link</a></strong><br>";
				}

			}
		}else{
			$text = '';
			if (count($new) > 0) {
				foreach ($new as $key => $item) {
					echo $key."<strong>  :".$item."</strong><br />";
				}
			}
		}
		?>
		Transaction code: <strong> <?php echo $code; ?></strong><br>
	</p>
	</td>
	</tr>
	</tbody>
	</table>
	</div>
	</div>
	
	</td>
	</tr>
	</tbody>
	</table>
	<table class="jumbotron" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0">
	<tbody>
	<tr>
	<td class="jumbotron_cell product_row" align="center" valign="top" style="padding:0 0 16px;text-align:center;background-color:#f2f2f5;border-left:4px solid;border-right:4px solid;border-top:1px solid;border-color:#d8dde4;font-size:0!important">
	
	<div class="row" style="display:inline-block;width:100%;vertical-align:top;text-align:center;max-width:580px;margin:0 auto">
	
	<div class="col-3" style="display:inline-block;width:100%;vertical-align:top;text-align:center;max-width:580px">
	<table class="column" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;width:100%;vertical-align:top">
	<tbody>
	<tr>
	<td class="column_cell font_default" align="center" valign="top" style="padding:16px 16px 0;font-family:Helvetica,Arial,sans-serif;font-size:15px;text-align:center;vertical-align:top;color:#888">
	<small style="font-size:86%;font-weight:normal"><strong>Notice</strong><br>
	You're getting this email because someone made a payment of <?php $currency.' '.number_format($amount); ?> to <a href="<?php echo get_bloginfo('url') ?>" style="display:inline-block;text-decoration:none;font-family:Helvetica,Arial,sans-serif;color:#2f68b4"><?php echo get_option( 'blogname' );?></a>.</small>
	</td>
	</tr>
	</tbody>
	</table>
	</div>
	
	</div>
	</td>
	</tr>
	</tbody>
	</table>
	<table class="footer" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0">
	<tbody>
	<tr>
	<td class="footer_cell" align="center" valign="top" style="padding:0;text-align:center;padding-bottom:16px;border-top:1px solid;border-bottom:4px solid;background-color:#fff;border-left:4px solid;border-right:4px solid;border-color:#d8dde4;font-size:0!important">
	<div class="row" style="display:inline-block;width:100%;vertical-align:top;text-align:center;max-width:580px;margin:0 auto">
	<div class="col-13 col-bottom-0" style="display:inline-block;width:100%;vertical-align:top;text-align:center;max-width:390px">
	<table class="column" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;width:100%;vertical-align:top">
	<tbody>
	<tr>
	<td class="column_cell font_default" align="center" valign="top" style="padding:16px;font-family:Helvetica,Arial,sans-serif;font-size:15px;text-align:left;vertical-align:top;color:#b3b3b5;padding-bottom:0;padding-top:16px">
	<strong><?php echo get_option( 'blogname' );?></strong><br>
	</td>
	</tr>
	</tbody>
	</table>
	</div>
	<div class="col-1 col-bottom-0" style="display:inline-block;width:100%;vertical-align:top;text-align:center;max-width:190px">
	<table class="column" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;width:100%;vertical-align:top">
	<tbody>
	<tr>
	<td class="column_cell font_default" align="center" valign="top" style="padding:16px;font-family:Helvetica,Arial,sans-serif;font-size:15px;text-align:left;vertical-align:top;color:#b3b3b5;padding-bottom:0;padding-top:16px">
	</td>
	</tr>
	</tbody>
	</table>
	</div>
	</div>
	</td>
	</tr>
	</tbody>
	</table>
	</div>
	</div>
	</body>
	</html>

	<?php

	$message = ob_get_contents();
	ob_end_clean();
	$admin_email = get_option('admin_email');
	$website = get_option('blogname');
	// $headers = array("From: $website <$admin_email>" . "\r\n");
	$headers = "From: ".$website."<$admin_email>" . "\r\n";
	wp_mail($admin_email, $email_subject, $message,$headers);

}
// function kkd_pff_rave_fetch_plan($code){
// 	$mode =  esc_attr( get_option('mode') );
// 	if ($mode == 'test') {
// 		$key = esc_attr( get_option('tsk') );
// 	}else{
// 		$key = esc_attr( get_option('lsk') );
// 	}
// 	$rave_url = 'https://api.rave.co/plan/' . $code;
// 	$headers = array(
// 		'Authorization' => 'Bearer ' . $key
// 	);
// 	$args = array(
// 		'headers'	=> $headers,
// 		'timeout'	=> 60
// 	);
// 	$request = wp_remote_get( $rave_url, $args );
// 	if( ! is_wp_error( $request )) {
// 		$rave_response = json_decode( wp_remote_retrieve_body( $request ) );

// 	}
// 	return $rave_response;
// }
function kkd_pff_rave_fetch_plan( $id) {
 
	$rave = new Kkd_Pff_Rave_Public('rave',KKD_PFF_RAVE_VERSION);
	
	$secret_key = $rave->secret_key;
	
  	$url = $rave->base_url . '/v2/gpx/paymentplans/query?seckey='.$secret_key.'&id='.$id;
  	$args = array(
	    'timeout'	=> 60
  	);

	$response = wp_remote_get( $url, $args );
	$result = wp_remote_retrieve_response_code( $response );

	if( $result === 200 ){
	    return json_decode( wp_remote_retrieve_body( $response ));
	}

  return $result;

}
function kkd_pff_rave_form_shortcode($atts) {
    ob_start();

		global $current_user;
		$user_id = $current_user->ID;
		$email = $current_user->user_email;
		$fname = $current_user->user_firstname;
		$lname = $current_user->user_lastname;
		if ($fname == '' && $lname == '') {
			$fullname = '';
		}else{
			$fullname = $fname.' '.$lname;
    }
		extract(shortcode_atts(array(
      'id' => 0,
   ), $atts));
    $rave = new Kkd_Pff_Rave_Public('rave',KKD_PFF_RAVE_VERSION);
	$pk = $rave->public_key;
	$validation_param_name = $rave->validation_param_name;
	$show_validation_value = $rave->show_validation_value;
	$validation_value_name = $rave->validation_value_name;
	
    if(!$pk){
		$settingslink = get_admin_url().'edit.php?post_type=rave_form&page=class-rave-forms-admin.php';
        echo "<h5>You must set your Rave API keys first <a href='".$settingslink."'>settings</a></h5>";
    }
    else if ($id != 0) {
     $obj = get_post($id);
		 if ($obj->post_type == 'rave_form') {
			$amount = get_post_meta($id,'_amount',true);
		    $thankyou = get_post_meta($id,'_successmsg',true);
			$paybtn = get_post_meta($id,'_paybtn',true);
			$loggedin = get_post_meta($id,'_loggedin',true);
			$txncharge = get_post_meta($id,'_txncharge',true);
			$currency = get_post_meta($id,'_currency',true);
			$recur = get_post_meta($id,'_recur',true);
			$recurplan = get_post_meta($id,'_recurplan',true);
			$usequantity = get_post_meta($id,'_usequantity',true);
			$quantity = get_post_meta($id,'_quantity',true);
			$useagreement = get_post_meta($id,'_useagreement',true);
			$agreementlink = get_post_meta($id,'_agreementlink',true);
			$minimum = get_post_meta($id,'_minimum',true);
			$variableamount = get_post_meta($id,'_variableamount',true);
			$usevariableamount = get_post_meta($id,'_usevariableamount',true);
			$hidetitle = get_post_meta($id,'_hidetitle',true);
			$showvalidationsection = get_post_meta($id, '_showvalidationsection', true);
			
		    if ($minimum == "") {$minimum = 0;}
		    if ($usevariableamount == "") {$usevariableamount = 0;}
			if ($showvalidationsection == "") {$showvalidationsection = 0;}

		    if ($usevariableamount == 1) {
		    	$paymentoptions = explode(',', $variableamount);
		    	// echo "<pre>";
		    	// print_r($paymentoptions);
		    	// echo "</pre>";
		    	// die();
		    }
		    $showbtn = true;
			$planerrorcode = 'Input Correct Recurring Plan Code';
			  if ($recur == 'fixed') {
					if ($recurplan == '' || $recurplan == null) {
						$showbtn = false;
					}else{
						$plan =	kkd_pff_rave_fetch_plan($recurplan);
						if ($plan->data->page_info->total == 1) {

							$planamount = $plan->data->paymentplans[0]->amount;
							
						}else{
							$showbtn = false;
						}
					}

			  }
			 if ((($user_id != 0) && ($loggedin == 'yes')) || $loggedin == 'no') {
			 	if ($hidetitle != 1) {
			 // echo "<h1 id='pf-form".$id."'>".$obj->post_title."</h1>";
			 		
			 	}
			 echo '<form version="'.KKD_PFF_RAVE_VERSION.'" enctype="multipart/form-data" action="' . admin_url('admin-ajax.php') . '" url="' . admin_url() . '" method="post" class="rave-form " novalidate>
				 <div class="form-heading text-center rave-form-header">
                    <div class="title">'.$obj->post_title.'</div>
                 </div>';
			 echo '<input type="hidden" name="action" value="kkd_pff_rave_submit_action">';
			 echo '<input type="hidden" name="rave-form-id" value="' . $id . '" />';
			 echo '<input type="hidden" name="rave-user-id" value="' . $user_id. '" />';
			 echo '<input type="hidden" name="rave-recur" value="' . $recur. '" />';

			 echo '<div class="row">
                    <div class="col-md-12">
                        <label>Full Name(Payer\'s Name)</label>
                        <input type="text" name="rave-fullname" value="' . $fullname. '" required /> 
                    </div>
                </div>';
			 echo '<div class="row">
                    <div class="col-md-12">
                        <label>Email</label>
                        <input type="email" name="rave-email"   id="rave-email" value="' . $email. '"
					 ';
					 if($loggedin == 'yes'){
						 echo 'readonly ';
					 }

			echo' required>
				 </div>
			 </div>';
			 echo '<div class="row">';

				if ($currency == 'open') {
					echo '<div class="col-md-12">
		 					 <label class="label">Currency</label>
		 				 	 <select class="form-control" name="rave-currency" required >
		 				 	 		<option value="NGN">NGN</option>
		 				 	 		<option value="KES">KES</option>
									<option value="GHS">GHS</option>
									<option value="UGX">UGX</option>
		 				 	 		<option value="ZAR">ZAR</option>
									<option value="TZS">TZS</option>
									<option value="ZMW">ZMW</option>
		 				 	 		<option value="RWF">RWF</option>
		 				 	 		<option value="USD">USD</option>
		 				 	 		<option value="GBP">GBP</option>
		 				 	 		<option value="EUR">EUR</option>
		 					 </select>
		 					 <br><br>
		 				 </div><br>';
				}
				 
				 
				echo '<div class="col-md-12"><label class="label">Amount ';

				if($currency != 'open'){
				echo '('.$currency;
					 if ($minimum == 0 && $amount != 0 && $usequantity == 'yes') {
					 	echo ' '.number_format($amount);
					 }
				 echo ')';
				}
				 if ($recur == 'fixed') {
				 	echo ' - Monthly Recurring Payment';
				 }
				echo '</label>';
				if ($usevariableamount == 0) {
					if ($minimum == 1) {
						 echo '<small> Minimum payable amount <b style="font-size:87% !important;">'.$currency.'  '.number_format($amount).'</b></small>';
					}
					if ($recur == 'fixed') {
						 if ($showbtn) {
							 echo '<input type="text" name="rave-amount" value="'.$planamount.'" id="rave-amount" readonly style="margin-bottom: 5px;" required/>';
							 

		 				 }else{
							 echo '<label class="label" style="font-size:18px;font-weight:600;line-height: 20px;">'.$planerrorcode.'</label>';
						 }
					}elseif($recur == 'optional'){
						 echo '<input type="text" name="rave-amount" class="rave-number" id="rave-amount" value="0" required/>';
					}else{
					 	if ($amount == 0) {
						 echo '<input type="text" name="rave-amount" class="rave-number" value="0" id="rave-amount" required/>';
					 	}elseif($amount != 0 && $minimum == 1){
							echo '<input type="text" name="rave-amount" value="'.$amount.'" id="rave-amount" required/>';
						}else{
							echo '<input type="text" name="rave-amount" value="'.$amount.'" id="rave-amount" readonly required/>';
						}
					}
				}else{
					if ($usevariableamount == "") {
						echo "Form Error, set variable amount string";
					}else{
						if (count($paymentoptions) > 0) {
								echo '<div class="select">
			 				 	 	<input type="hidden"  id="rave-vname" name="pf-vname" />
			 				 	 	<input type="hidden"  id="rave-amount" />
 									<select class="form-control" id="rave-vamount" name="rave-amount">';
			 					 	$max = $quantity+1;
			 					 	foreach ($paymentoptions as $key => $paymentoption) {
										list($a,$b) = explode(':', $paymentoption);
			 					 		echo '<option value="'.$b.'" data-name="'.$a.'">'.$a.'('.number_format($b).')</option>';

									}
			 					echo '</select> <i></i> </div>';
							
						}
					}
				}
			 	if ($txncharge != 'merchant' && $recur != 'plan') {
					echo '<small>Transaction Charge: <b class="rave-txncharge"></b>, Total:<b  class="rave-txntotal"></b></small>';
				}
				if($recur == 'fixed'){
					if ($showbtn) {
						
						echo '<input type="hidden" name="pf-plancode" value="' . $recurplan. '" />';
						echo '<label class="label" style="margin:0;font-size:13px;font-weight:600;line-height: 10px;">'.$plan->data->paymentplans[0]->name.' '.$plan->data->paymentplans[0]->interval. ' recuring payment - '.$plan->data->paymentplans[0]->currency.' '.number_format($planamount).'</label>
							';
					}else{
						echo '<div class="row">
									 <label class="label" style="font-size:18px;font-weight:600;line-height: 20px;">'.$planerrorcode.'</label>
								 </div>';
					}

				}
			echo '</div>
			 </div>';
			 if ($minimum == 0 && $recur == 'no' && $usequantity == 'yes' && ($usevariableamount == 1 || $amount != 0)) {
			 // if ($minimum == 0 && $recur == 'no' && $usequantity == 'yes' && $amount != 0) {
				echo '<div class="row">
	 				 <div class="col-md-12">
	 				 <label class="label">Quantity</label>
 				 
 				 	<input type="hidden" value="'.$amount.'" id="rave-quantityamount"/>
 					 <select class="form-control" id="rave-quantity" name="rave-quantity" >';
 					 $max = $quantity+1;
 					 for ($i=1; $i < $max; $i++) {
 					 	echo  ' <option value="'.$i.'">'.$i.'</ption>';
 					 }
 					echo  '</select>
 					 <i></i>
 				 </div>
 			 </div>';
			}

			if ($recur == 'optional') {
				echo '<div class="row">
	 				 <div class="col-md-12">
	 					 <label class="label">Recuring Payment</label>
	 				 	 <select class="form-control" name="rave-interval" >
	 						 <option value="no">None</option>
	 						 <option value="hourly">Hourly</option>
	 						 <option value="daily">Daily</option>
	 						 <option value="every 2 days">Every 2 days</option>
	 						 <option value="weekly">Weekly</option>
	 						 <option value="monthly">Monthly</option>
	 						 <option value="quarterly">Quarterly</option>
	 						 <option value="yearly">Yearly</option>
	 					 </select>
	 					 <i></i>
	 				 </div>
	 			 </div>';
			}
			if($showvalidationsection == 1){
			
				echo '<div class="row">
							<div class="col-md-12">
								<label>'.$validation_param_name.'</label>
								<input type="text" name="rave-validation"  id="rave-validation" style="margin-bottom: 0px;" required>';
								
								if($show_validation_value == 1){
									echo '<small style="margin-bottom: 20px;">'.$validation_value_name.': <img src="'. plugins_url( '../assets/loader.gif' , __FILE__ ) .'" alt="cardlogos"  id="validation_loader"  style="    max-width: 30px;display:none;" class=" size-full wp-image-1096" /><b id="rave-validation-name" style="    font-size: 15px;"></b></small>';
								}
					
					echo	'<br><br></div>
					</div>';
			}

		  echo(do_shortcode($obj->post_content));
		 
			if ($useagreement == 'yes'){
				echo '<div class="row">
						<div class="col-md-12">
							<input type="checkbox" name="agreement" id="rave-agreement" required value="yes">
							<label class="checkbox ">
							<i id="rave-agreementicon" ></i>
							Accept terms <a target="_blank" href="'.$agreementlink.'">Link </a></label>
						
					</div></div><br>';
			}
			echo '<div class="span12 unit">
					<br>
					<small><span style="color: red;">*</span> are compulsory</small><br />
					<img src="https://media.flutterwave.com/images/rave-payment-banner.png" alt="cardlogos"   style="    max-width: 250px;" class=" size-full wp-image-1096" />
					';
						
		echo '</div>';
			if ($showbtn){
				echo '
                    <div class="row ">
                        <div class="col-md-12">
                            <button type="submit" id="rave-submit"  class="adam-button2 adam-green">'.$paybtn.'</button>
                        </div>
                    </div>
                <div class="clearfix"></div>';
			}

			 echo '
			</form><div id="rave-form-loader" class="rave-form-spinner-container" style="display: none"><div class="rave-form-spinner"></div></div>';
		 }else{
			 echo "<h5>You must be logged in to make payment</h5>";
		 }

    }
   }



    return ob_get_clean();
}
add_shortcode( 'rave-form', 'kkd_pff_rave_form_shortcode' );

function kkd_pff_rave_datepicker_shortcode($atts) {
  	extract(shortcode_atts(array(
		'name' => 'Title',
    	'required' => '0',
		 ), $atts)
	);

  	$code = '<div class="row">
				<div class="col-md-12">
					<label class="label">'.$name;
					if ($required == 'required') {
						$code.= ' <span>*</span>';
					}
					$code.= '</label>
		
					<input type="text" class="date-picker" name="'.$name.'" ';
					if ($required == 'required') {
						$code.= ' required="required" ';
					}
					$code.= '" />
				</div>
			</div>';
  	return $code;
}
add_shortcode('datepicker', 'kkd_pff_rave_datepicker_shortcode');


function kkd_pff_rave_text_shortcode($atts) {
  	extract(shortcode_atts(array(
		'name' => 'Title',
    	'required' => '0',
	), $atts));
	
	if ($required == 'readonly') {
		$msg .= '<span style="color:green">(This field will appear when you enter the Student ID)</span>';
	}
  	$code = '<div class="row">
				<div class="col-md-12">
					<label class="label">'.$name .' '.$msg.'';
					if ($required == 'required') {
						$code.= ' <span>*</span>';
					}
					$code.= '</label>
					<input type="text" name="'.$name.'" id="'.str_replace(' ', '', $name).'" ';
					if ($required == 'required') {
						$code.= ' required="required" ';
					} else  if ($required == 'readonly') {
						$code.= 'readonly';
					}
					$code.= ' />
				</div>
			</div>';
  	return $code;
}
add_shortcode('text', 'kkd_pff_rave_text_shortcode');

function kkd_pff_rave_select_shortcode($atts) {
	extract(shortcode_atts(array(
		'name' => 'Title',
		'options' => '',
    'required' => '0',
 	), $atts));
	$code = '<div class="row">
						<div class="col-md-12">
		<label class="label">'.$name. '</label>
			<select class="form-control"  name="'.$name.'"';
	if ($required == 'required') {
		 $code.= ' required="required" ';
	}
	$code.=">";

	$soptions = explode(',', $options);
	if (count($soptions) > 0) {
		foreach ($soptions as $key => $option) {
			$code.= '<option  value="'.$option.'" >'.$option.'</option>';
		}
	}
	$code.= '" </select><div class="lineheight"></div> </div></div>';
  return $code;
}
add_shortcode('select', 'kkd_pff_rave_select_shortcode');
function kkd_pff_rave_radio_shortcode($atts) {
	extract(shortcode_atts(array(
		'name' => 'Title',
		'options' => '',
    'required' => '0',
 	), $atts));
	$code = '<div class="row">
						<div class="col-md-12">
		<label class="label">'.$name;
		if ($required == 'required') {
			 $code.= ' <span>*</span>';
		}
	$code.= '</label>
		';
	$soptions = explode(',', $options);
	if (count($soptions) > 0) {
		foreach ($soptions as $key => $option) {
			// $code.= '<option  value="'.$option.'" >'.$option.'</option>';
			$code.= '<label class="radio">
				<input type="radio" name="'.$name.'" value="'.$option.'"';
				if ($key == 0) {
					$code.= ' checked';
					if ($required == 'required') {
				$code.= ' required="required"';
			}
				}

			$code.= '/>
				<i></i>
				'.$option.'
			</label>';
		}
	}
	$code.= '</div></div>';
  return $code;
}
add_shortcode('radio', 'kkd_pff_rave_radio_shortcode');
function kkd_pff_rave_checkbox_shortcode($atts) {
	extract(shortcode_atts(array(
		'name' => 'Title',
		'options' => '',
    'required' => '0',
 	), $atts));
	$code = '<div class="row">
						<div class="col-md-12">
		<label class="label">'.$name;
		if ($required == 'required') {
			 $code.= ' <span>*</span>';
		}
	$code.= '</label>
		';

	$soptions = explode(',', $options);
	if (count($soptions) > 0) {
		foreach ($soptions as $key => $option) {
			$code.= '<label class="checkbox">
				<input type="checkbox" name="'.$name.'[]" value="'.$option.'"';
				if ($key == 0) {
					$code.= ' checked';
					if ($required == 'required') {
				$code.= ' required="required"';
			}
				}

			$code.= '/>
				<i></i>
				'.$option.'
			</label>';
		}
	}
	$code.= '</div></div>';
  return $code;
}
add_shortcode('checkbox', 'kkd_pff_rave_checkbox_shortcode');
function kkd_pff_rave_textarea_shortcode($atts) {
	extract(shortcode_atts(array(
      'name' => 'Title',
			'required' => '0',
	 ), $atts));
	$code = ' <div class="row">
                                <div class="col-md-12">
		<label class="label">'.$name;
		if ($required == 'required') {
			 $code.= ' <span>*</span>';
		}
	$code.= '</label>
			<textarea type="text" name="'.$name.'" rows="3" ';
	if ($required == 'required') {
		 $code.= ' required="required" ';
	}
	$code.= '" ></textarea></div></div>';
   return $code;
}
add_shortcode('textarea', 'kkd_pff_rave_textarea_shortcode');
function kkd_pff_rave_input_shortcode($atts) {
  extract(shortcode_atts(array(
		'name' => 'Title',
    'required' => '0',
 	), $atts));

	$code = ' <div class="row">
		<label class="label">'.$name;
		if ($required == 'required') {
			 $code.= ' <span>*</span>';
		}
	$code.= '</label>
		<div class="input  append-small-btn">
		<div class="file-button">
			Browse
			<input type="file" name="'.$name.'" onchange="document.getElementById(\'append-small-btn\').value = this.value;"';
	if ($required == 'required') {
		 $code.= ' required="required" ';
	}
	$code.= '" /></div>
		<input type="text" id="append-small-btn" readonly="" placeholder="no file selected">
	</div></div>';
  return $code;
}
add_shortcode('input', 'kkd_pff_rave_input_shortcode');

// Save the Metabox Data
function kkd_pff_rave_generate_new_code($length = 10){
  $characters = '06EFGHI9KL'.time().'MNOPJRSUVW01YZ923234'.time().'ABCD5678QXT';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return "RAVEWP".$randomString;
}
function kkd_pff_rave_check_code($code){
	global $wpdb;
	$table = $wpdb->prefix.KKD_PFF_RAVE_TABLE;
	$o_exist = $wpdb->get_results("SELECT * FROM $table WHERE reference = '".$code."'");

	  if (count($o_exist) > 0) {
	      $result = true;
	  } else {
	      $result = false;
	  }

  return $result;
}
function kkd_pff_rave_generate_code(){
  $code = 0;
  $check = true;
  while ($check) {
      $code = kkd_pff_rave_generate_new_code();
      $check = kkd_pff_rave_check_code($code);
  }

  return $code;
}
function kkd_pff_rave_get_the_user_ip() {
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

add_action( 'wp_ajax_kkd_pff_rave_submit_action', 'kkd_pff_rave_submit_action' );
add_action( 'wp_ajax_nopriv_kkd_pff_rave_submit_action', 'kkd_pff_rave_submit_action' );
function kkd_pff_rave_submit_action() {
	if (trim($_POST['rave-email']) == '') {
	    $response['result'] = 'failed';
	  	$response['message'] = 'Email is required';

	  	exit(json_encode($response));
	}

  	do_action( 'kkd_pff_rave_before_save' );

  	global $wpdb;
	$code = kkd_pff_rave_generate_code();

  	$table = $wpdb->prefix.KKD_PFF_RAVE_TABLE;
	$metadata = $_POST;
	$fullname = $_POST['rave-fullname'];
	$recur = $_POST['rave-recur'];
	$rave_validation = $_POST['rave-validation'];
	
	unset($metadata['action']);
	unset($metadata['rave-recur']);
	unset($metadata['rave-form-id']);
	unset($metadata['rave-email']);
	unset($metadata['rave-amount']);
	unset($metadata['rave-user-id']);
	unset($metadata['rave-interval']);
	unset($metadata['rave-validation']);



	$fixedmetadata = kkd_pff_rave_meta_as_custom_fields($metadata);
	
	$filelimit = get_post_meta($_POST["rave-form-id"],'_filelimit',true);
	$currency = get_post_meta($_POST["rave-form-id"],'_currency',true);

	if($currency == 'open'){
		$currency = $_POST['rave-currency'];
	}
	$formamount = get_post_meta($_POST["rave-form-id"],'_amount',true);/// From form
	$recur = get_post_meta($_POST["rave-form-id"],'_recur',true);
	$transaction_charge = get_post_meta($_POST["rave-form-id"],'_merchantamount',true);
	$transaction_charge = $transaction_charge*100;
		
	$txncharge = get_post_meta($_POST["rave-form-id"],'_txncharge',true);
	$minimum = get_post_meta($_POST["rave-form-id"],'_minimum',true);
	$variableamount = get_post_meta($_POST["rave-form-id"],'_variableamount',true);
	$usevariableamount = get_post_meta($_POST["rave-form-id"],'_usevariableamount',true);
	$amount = (int)str_replace(' ', '', $_POST["rave-amount"]);
	if($rave_validation){
		$validation_table = $wpdb->prefix.KKD_PFF_RAVE_TABLE_VALIDATION;
		$rave = new Kkd_Pff_Rave_Public('rave',KKD_PFF_RAVE_VERSION);
	
		$validation_param_name = $rave->validation_param_name;
		$record = $wpdb->get_results("SELECT * FROM $validation_table WHERE (param = '".$rave_validation."')");
		if (array_key_exists("0", $record)) {

			$validation_array =  array(
				'metaname' => $validation_param_name,
				'metavalue' => $rave_validation
			);
			array_unshift($fixedmetadata, $validation_array);
		}else{
			$response = [];
			$response['result'] = 'failed';
	  		$response['message'] = $validation_param_name. ' is invalid';

	  		exit(json_encode($response));
		}
	

	}
	$variablename = $_POST["rave-vname"];
	// pf-vname
	$originalamount = $amount;
	$quantity = 1;
	$usequantity = get_post_meta($_POST["rave-form-id"],'_usequantity',true);

	if(($recur == 'no') && ($formamount != 0)){
		$amount = (int)str_replace(' ', '', $formamount);
	}
	if ($minimum == 1 && $formamount != 0) {
		if ($originalamount < $formamount) {
			$amount = $formamount;
		}else{
			$amount = $originalamount;
		}
	}
	if ($usevariableamount == 1) {
		$paymentoptions = explode(',', $variableamount);
		if (count($paymentoptions) > 0) {
			foreach ($paymentoptions as $key => $paymentoption) {
				list($a,$b) = explode(':', $paymentoption);
				if ($variablename == $a) {
					$amount = $b;
				}
		 	}
		}
	}
	$fixedmetadata[] =  array(
		'display_name' => 'Unit Price',
		'variable_name' => 'Unit_Price',
		'type' => 'text',
		'value' => $currency.number_format($amount)
	);
		if ($usequantity != 'no') {
			$quantity = $_POST["rave-quantity"];
			$unitamount = (int)str_replace(' ', '', $amount);
			$amount = $quantity*$unitamount;
		}
		
	
	
	$maxFileSize = $filelimit * 1024 * 1024;

	if(!empty($_FILES)){
		foreach ($_FILES as $keyname => $value) {
			if ($value['size'] > 0) {
				if ($value['size'] > $maxFileSize) {
					$response['result'] = 'failed';
			  	$response['message'] = 'Max upload size is '.$filelimit."MB";
					exit(json_encode($response));
				}else{
					$attachment_id = media_handle_upload($keyname, $_POST["rave-form-id"]);
					$url = wp_get_attachment_url( $attachment_id);
					$fixedmetadata[] =  array(
						'display_name' => ucwords(str_replace("_", " ", $keyname)),
						'variable_name' => $keyname,
					      'type' => 'link',
					      'value' => $url
					);
				}
			}else{
				$fixedmetadata[] =  array(
					'display_name' => ucwords(str_replace("_", " ", $keyname)),
					'variable_name' => $keyname,
				      'type' => 'text',
				      'value' => 'No file Uploaded'
				);
			}

		}
	}
	$plancode = 'none';
	$interval = $_POST['rave-interval'];
	if($recur != 'no'){
		if ($recur == 'optional') {
			$interval = $_POST['rave-interval'];
			if ($interval != 'no') {
				unset($metadata['rave-interval']);
					
					
					$rave = new Kkd_Pff_Rave_Public('rave',KKD_PFF_RAVE_VERSION);
	
					$secret_key = $rave->secret_key;

					$url = $rave->base_url . '/v2/gpx/paymentplans/create';
					$body = array(
						'name' => $currency.number_format($originalamount).' ['.$currency.number_format($amount).'] - '.$interval,
						'amount'=> $amount,
						'interval'	=> $interval,
						'seckey' => $secret_key
					);
					$headers = array(
						'Content-Type'	=> 'application/json',
					);
					$args = array(
						'headers'	=> $headers,
						'body'		=> json_encode( $body ),
						'timeout'	=> 60
					);

					$request = wp_remote_post( $url, $args );
					if( ! is_wp_error( $request )) {
						$rave_response = json_decode(wp_remote_retrieve_body($request));
						$plancode	= $rave_response->data->id;
						$fixedmetadata[] =  array(
							'display_name' => 'Plan Interval',
							'variable_name' => 'Plan Interval',
							'type' => 'text',
							'value' => $rave_response->data->interval
						);

					}
						// }

					// }

				}
		}else{
			//Use Plan Code
			$plancode = $_POST['pf-plancode'];
			unset($metadata['pf-plancode']);
		}
	}
	

	$is_recurring = 0;
	if(($recur == 'optional' && $interval != 'no') || ($recur == 'fixed')){
		$is_recurring = 1;
	}
	$rave = new Kkd_Pff_Rave_Public('rave',KKD_PFF_RAVE_VERSION);
	$public_key = $rave->public_key;

	

	$insert =  array(
    	'post_id' => strip_tags($_POST["rave-form-id"], ""),
		'email' => strip_tags($_POST["rave-email"], ""),
    	'user_id' => strip_tags($_POST["rave-user-id"], ""),
		'amount' => strip_tags($amount, ""),
	  	'plan' => strip_tags($plancode, ""),
		'ip' => kkd_pff_rave_get_the_user_ip(),
		'mode' => $rave->mode,
		'reference' => $code,
		'currency' => $currency,
		'recur' => $is_recurring,
		'metadata' => json_encode($fixedmetadata)
  	);
	$exist = $wpdb->get_results("SELECT * FROM $table WHERE (post_id = '".$insert['post_id']."'
			AND email = '".$insert['email']."'
			AND user_id = '".$insert['user_id']."'
			AND amount = '".$insert['amount']."'
			AND plan = '".$insert['plan']."'
			AND recur = '".$insert['recur']."'
			AND mode = '".$insert['mode']."'
			AND ip = '".$insert['ip']."'
			AND paid = '0'
			AND metadata = '". $insert['metadata'] ."')");
	if (count($exist) > 0) {
		 $wpdb->update( $table, array( 'reference' => $code,'plan' =>$insert['plan']),array('id'=>$exist[0]->id));
							
   	}else{
		 $wpdb->insert(
	        $table,
	        $insert
	    );
	}
	$amount = floatval($insert['amount'])*100;
	switch ($currency) {
		case 'GHS':
			$country = 'GH';
			break;
		case 'KES':
			$country = 'KE';
			break;
		case 'TZS':
			$country = 'TZ';
			break;
		case 'ZAR':
			$country = 'ZA';
			break;
		
		default:
			$country = 'NG';
			break;
	}
	 $response = array(
     	'result' => 'success',
		'reference' => $insert['reference'],
     	'plan' => $insert['plan'],
     	'quantity' => $quantity,
		'email' => $insert['email'],
     	'name' => $fullname,
   	 	'total' => round($amount),
		'meta' => $fixedmetadata,
		'country' => $country,
		'key' => $public_key,
		'currency' => $currency,
   );
	 // print_r($response);
  echo json_encode($response,JSON_NUMERIC_CHECK);

  die();
}

function kkd_pff_rave_meta_as_custom_fields($metadata){
	$custom_fields = array();
	foreach ($metadata as $key => $value) {
		if (is_array($value)) {
			$value = implode(', ',$value);
		}
		if ($key == 'rave-fullname') {
			$custom_fields[] =  array(
				'metaname' => 'Full Name',
				'metavalue' => $value
			);
		}elseif ($key == 'pf-plancode') {
			$custom_fields[] =  array(
				'metaname' => 'Plan',
				'metavalue' => $value
			);
		}elseif ($key == 'pf-vname') {
			$custom_fields[] =  array(
				'metaname' => 'Payment Option',
				 'metavalue' => $value
			);
		}elseif ($key == 'rave-interval') {
			$custom_fields[] =  array(
				'metaname' => 'Plan Interval',
				 'metavalue' => $value
			);
		}elseif ($key == 'rave-quantity') {
			$custom_fields[] =  array(
				'metaname' => 'Quantity',
				'metavalue' => $value
			);
		}else{
			$custom_fields[] =  array(
			'metaname' =>  ucwords(str_replace("_", " ", $key)),
	     'metavalue' => $value
		
			);
		}

	}
	return $custom_fields;
}
add_action( 'wp_ajax_kkd_pff_rave_validate_parameter', 'kkd_pff_rave_validate_parameter' );
add_action( 'wp_ajax_nopriv_kkd_pff_rave_validate_parameter', 'kkd_pff_rave_validate_parameter' );


function kkd_pff_rave_validate_parameter() {

  if (trim($_POST['param']) == '' || trim($_POST['param']) == '') {
    $response['error'] = true;
  	$response['error_message'] = "Did you make a payment?";

  	exit(json_encode($response));
  }
	 global $wpdb;
	 $rave = new Kkd_Pff_Rave_Public('rave',KKD_PFF_RAVE_VERSION);
	
	$validation_param_name = $rave->validation_param_name;
	$table = $wpdb->prefix.KKD_PFF_RAVE_TABLE_VALIDATION;
	$param = $_POST['param'];
	$data = [];
	$record = $wpdb->get_results("SELECT * FROM $table WHERE (param = '".$param."')");
	if (array_key_exists("0", $record)) {

		$payment_array = $record[0];
		$result = 'success';

		$data = [
			'param' => $payment_array->param,
			'param_name' => $$validation_param_name,
			'value' => $payment_array->value
		];
		
	}else{
		$message = "Payment Verification Failed.";
		$result = "failed";
		$data = [
			'param' => $param,
			'param_name' => $validation_param_name,
			'value' => null
		];

	}
	
	$response = array(
	 'result' => $result,
	 
     'data' => $data,
   );
	
	 
  echo json_encode($response);

  die();
}

add_action( 'wp_ajax_kkd_pff_rave_confirm_payment', 'kkd_pff_rave_confirm_payment' );
add_action( 'wp_ajax_nopriv_kkd_pff_rave_confirm_payment', 'kkd_pff_rave_confirm_payment' );
function getTransactionDetails( $flwReference) {
 
	$rave = new Kkd_Pff_Rave_Public('rave',KKD_PFF_RAVE_VERSION);
	
	$secret_key = $rave->secret_key;
	
  	$url = $rave->base_url . '/flwv3-pug/getpaidx/api/verify';
  	$args = array(
	    'body' => array(
	      	'flw_ref' => $flwReference,
	      	'SECKEY' => $secret_key 
	    ),
	    'sslverify' => false
  	);

	$response = wp_remote_post( $url, $args );
	$result = wp_remote_retrieve_response_code( $response );

	if( $result === 200 ){
	    return wp_remote_retrieve_body( $response );
	}

  return $result;

}

function kkd_pff_rave_confirm_payment() {
  if (trim($_POST['reference']) == '' || trim($_POST['flwReference']) == '') {
    $response['error'] = true;
  	$response['error_message'] = "Did you make a payment?";

  	exit(json_encode($response));
  }
 	global $wpdb;
	$table = $wpdb->prefix.KKD_PFF_RAVE_TABLE;
	$code = $_POST['reference'];
	$flwReference = $_POST['flwReference'];
	$record = $wpdb->get_results("SELECT * FROM $table WHERE (reference = '".$code."')");
	if (array_key_exists("0", $record)) {

		$payment_array = $record[0];
		$amount = get_post_meta($payment_array->post_id,'_amount',true);
		$recur = get_post_meta($payment_array->post_id,'_recur',true);
		$currency = get_post_meta($payment_array->post_id,'_currency',true);
		if($currency == 'open'){
			$currency = $payment_array->currency;
		}
		$txncharge = get_post_meta($payment_array->post_id,'_txncharge',true);
		$redirect = get_post_meta($payment_array->post_id,'_redirect',true);
		$minimum = get_post_meta($payment_array->post_id,'_minimum',true);
		$usevariableamount = get_post_meta($payment_array->post_id,'_usevariableamount',true);
		$variableamount = get_post_meta($payment_array->post_id,'_variableamount',true);

		if ($minimum == 1 && $amount != 0) {
			if ($payment_array->amount < $formamount) {
				$amount = $formamount;
			}else{
				$amount = $payment_array->amount;
			}
		}
		$oamount = (int)$amount;
		$rave_response = json_decode(getTransactionDetails($flwReference,$payment_array->recur));
		if ($rave_response->data->flwMeta->chargeResponse === '00' || $rave_response->data->flwMeta->chargeResponse === '0') {
			$emailP = 'customer.email';
			$rave_email = strtolower($rave_response->data->$emailP);
			$amount_paid = $rave_response->data->amount;
			$currency_paid = $rave_response->data->currency ? $rave_response->data->currency : $rave_response->data->transaction_currency;
			$rave_ref 	= $rave_response->data->tx_ref;
			if ($recur == 'optional' || $recur == 'plan') {
				$wpdb->update( $table, array( 'paid' => 1,'amount' =>$amount_paid,'flw_reference' =>$flwReference),array('reference'=>$rave_ref));
							$thankyou = get_post_meta($payment_array->post_id,'_successmsg',true);
				$message = $thankyou;
				$result = "success";
			}else{

				if ($amount == 0 || $usevariableamount == 1) {
					$wpdb->update( $table, array( 'paid' => 1,'amount' =>$amount_paid,'flw_reference' =>$flwReference),array('reference'=>$rave_ref));
					$thankyou = get_post_meta($payment_array->post_id,'_successmsg',true);
					$message = $thankyou;
					$result = "success";
				}else{
					$usequantity = get_post_meta($payment_array->post_id,'_usequantity',true);
					if ($usequantity == 'no') {
						$oamount = (int)str_replace(' ', '', $amount);
					}else{
						$quantity = $_POST["quantity"];
						$unitamount = (int)str_replace(' ', '', $amount);
						$oamount = $quantity*$unitamount;
					}
					

					if( $oamount !=  $amount_paid ) {
						$message = "Invalid amount Paid. Amount required is ".$currency."<b>".number_format($oamount)."</b>";
						$result = "failed";
					}else{
						if($currency_paid == $currency){
							$wpdb->update( $table, array( 'paid' => 1,'flw_reference' =>$flwReference),array('reference'=>$rave_ref));
							$thankyou = get_post_meta($payment_array->post_id,'_successmsg',true);
							$message = $thankyou;
							$result = "success";
						}else{
							$message = "Payment currency mismatch. Currency required is ".$currency;
							$result = "failed";
						}
						
					}
				}
			}

		}else {
			$message = "Transaction Failed/Invalid Code";
			$result = "failed";
		}
	}else{
		$message = "Payment Verification Failed.";
		$result = "failed";

	}

	/** Still review this */
	if ($result == 'success') {
		///
		//Create Plan
		$enabled_custom_plan = get_post_meta($payment_array->post_id, '_startdate_enabled', true);
		if ($enabled_custom_plan == 1) {
			$mode =  esc_attr( get_option('mode') );
			if ($mode == 'test') {
				$key = esc_attr( get_option('tsk') );
			}else{
				$key = esc_attr( get_option('lsk') );
			}
			//Create Plan
			$rave_url = 'https://api.ravepay.co/subscription';
			$headers = array(
				'Content-Type'	=> 'application/json',
				'Authorization' => 'Bearer ' . $key
			);
			$custom_plan = get_post_meta($payment_array->post_id, '_startdate_plan_code', true);
			$days = get_post_meta($payment_array->post_id, '_startdate_days', true);

			$start_date = date("c", strtotime("+".$days." days"));
			$body = array(
				'start_date'	=> $start_date,
				'plan'			=> $custom_plan,
				'customer'		=> $customer_code
			);
			$args = array(
				'body'		=> json_encode( $body ),
				'headers'	=> $headers,
				'timeout'	=> 60
			);

			$request = wp_remote_post( $rave_url, $args );
			if( ! is_wp_error( $request )) {
				$rave_response = json_decode(wp_remote_retrieve_body($request));
				$plancode	= $rave_response->data->subscription_code;
				// $message.= $message.'Subscribed<br>'.$plancode.'sssss';


			}
		}
		
		$sendreceipt = get_post_meta($payment_array->post_id, '_sendreceipt', true);
		if($sendreceipt == 'yes'){
			$decoded = json_decode($payment_array->metadata);
			$fullname = $decoded[0]->value;
			// kkd_pff_rave_send_receipt($payment_array->post_id,$currency,$amount_paid,$fullname,$payment_array->email,$rave_ref,$payment_array->metadata);
			kkd_pff_rave_send_receipt_owner($payment_array->post_id,$currency,$amount_paid,$fullname,$payment_array->email,$rave_ref,$payment_array->metadata);

		}

	}
	/** end of review */

	$response = array(
     'result' => $result,
     'message' => $message,
   );
	if ($result == 'success' && $redirect != '') {
	 $response['result'] = 'success2';
	 $response['link'] = $redirect;
	}

	 
  echo json_encode($response);

  die();
}


add_action( 'wp_ajax_kkd_pff_rave_retry_action', 'kkd_pff_rave_retry_action' );
add_action( 'wp_ajax_nopriv_kkd_pff_rave_retry_action', 'kkd_pff_rave_retry_action' );
function kkd_pff_rave_retry_action() {
  if (trim($_POST['code']) == '') {
    $response['result'] = 'failed';
  	$response['message'] = 'Cde is required';

  	// Exit here, for not processing further because of the error
  	exit(json_encode($response));
  }
  do_action( 'kkd_pff_rave_before_save' );

  global $wpdb;
  	$code = $_POST['code'];
	$newcode = kkd_pff_rave_generate_code();
	$newcode = $newcode.'_2';
	$insert = array();
  	$table = $wpdb->prefix.KKD_PFF_RAVE_TABLE;
	$record = $wpdb->get_results("SELECT * FROM $table WHERE (reference = '".$code."')");
	if (array_key_exists("0", $record)) {
		$dbdata = $record[0];
		$plan = $dbdata->plan;
		$quantity = 1;
		$wpdb->update( $table, array( 'reference_2' => $newcode),array('reference' => $code));
								
		$currency = get_post_meta($dbdata->post_id,'_currency',true);
		$subaccount = get_post_meta($dbdata->post_id,'_subaccount',true);
		$txnbearer = get_post_meta($dbdata->post_id,'_txnbearer',true);
		$transaction_charge = get_post_meta($dbdata->post_id,'_merchantamount',true);
		$transaction_charge = $transaction_charge*100;
		$fixedmetadata = kkd_pff_rave_meta_as_custom_fields($dbdata->metadata);
		$nmeta = json_decode($dbdata->metadata);
		foreach ($nmeta as $nkey => $nvalue) {
			if ($nvalue->variable_name == 'Quantity') {
				$quantity = $nvalue->value;
			}
			if ($nvalue->variable_name == 'Full_Name') {
				$fullname = $nvalue->value;
			}
		}

	}
	if ($subaccount == "" || !isset($subaccount)) {
		$subaccount = NULL;
		$txnbearer = NULL;
		$transaction_charge = NULL;
	}
	if ($transaction_charge == "" || $transaction_charge == 0 || $transaction_charge == NULL || !isset($transaction_charge)) {
		$transaction_charge = NULL;
	}
	 $response = array(
     	'result' => 'success',
		'code' => $newcode,
		'plan' => $plan,
		'quantity' => $quantity,
		'email' => $dbdata->email,
		'name' => $fullname,
		'total' => $dbdata->amount*100,
		'custom_fields' => $fixedmetadata,
		'subaccount' => $subaccount,
		'txnbearer' => $txnbearer,
		'transaction_charge' => $transaction_charge
   );
  echo json_encode($response);

  die();
}
add_action( 'wp_ajax_kkd_pff_rave_rconfirm_payment', 'kkd_pff_rave_rconfirm_payment' );
add_action( 'wp_ajax_nopriv_kkd_pff_rave_rconfirm_payment', 'kkd_pff_rave_rconfirm_payment' );

function kkd_pff_rave_rconfirm_payment() {
  if (trim($_POST['code']) == '') {
    $response['error'] = true;
  	$response['error_message'] = "Did you make a payment?";

  	exit(json_encode($response));
  }
 	global $wpdb;
	$table = $wpdb->prefix.KKD_PFF_RAVE_TABLE;
	$code = $_POST['code'];
	$record = $wpdb->get_results("SELECT * FROM $table WHERE (reference_2 = '".$code."')");
	if (array_key_exists("0", $record)) {

		$payment_array = $record[0];
		$amount = get_post_meta($payment_array->post_id,'_amount',true);
		$recur = get_post_meta($payment_array->post_id,'_recur',true);
		$currency = get_post_meta($payment_array->post_id,'_currency',true);
		$txncharge = get_post_meta($payment_array->post_id,'_txncharge',true);
		$redirect = get_post_meta($payment_array->post_id,'_redirect',true);


		$mode =  esc_attr( get_option('mode') );
		if ($mode == 'test') {
			$key = esc_attr( get_option('tsk') );
		}else{
			$key = esc_attr( get_option('lsk') );
		}
		$rave_url = 'https://api.ravepay.co/transaction/verify/' . $code;
		$headers = array(
			'Authorization' => 'Bearer ' . $key
		);
		$args = array(
			'headers'	=> $headers,
			'timeout'	=> 60
		);
		$request = wp_remote_get( $rave_url, $args );
		if( ! is_wp_error( $request ) && 200 == wp_remote_retrieve_response_code( $request ) ) {
			$rave_response = json_decode( wp_remote_retrieve_body( $request ) );
			if ( 'success' == $rave_response->data->status ) {
						$amount_paid	= $rave_response->data->amount / 100;
						$rave_ref 	= $rave_response->data->reference;
						if ($recur == 'optional' || $recur == 'plan') {
							$wpdb->update( $table, array( 'paid' => 1,'amount' =>$amount_paid),array('reference_2'=>$rave_ref));
							$thankyou = get_post_meta($payment_array->post_id,'_successmsg',true);
							$message = $thankyou;
							$result = "success";
						}else{

							if ($amount == 0) {
								$wpdb->update( $table, array( 'paid' => 1,'amount' =>$amount_paid),array('reference_2'=>$rave_ref));
								$thankyou = get_post_meta($payment_array->post_id,'_successmsg',true);
								$message = $thankyou;
								$result = "success";
								// kkd_pff_rave_send_receipt($currency,$amount,$name,$payment_array->email,$code,$metadata)
							}else{
								$usequantity = get_post_meta($payment_array->post_id,'_usequantity',true);
								if ($usequantity == 'no') {
									$amount = (int)str_replace(' ', '', $amount);
								}else{
									$quantity = $_POST["quantity"];
									$unitamount = (int)str_replace(' ', '', $amount);
									$amount = $quantity*$unitamount;
								}


								if( $amount !=  $amount_paid ) {
									$message = "Invalid amount Paid. Amount required is ".$currency."<b>".number_format($amount)."</b>";
									$result = "failed";
								}else{

									$wpdb->update( $table, array( 'paid' => 1),array('reference_2'=>$rave_ref));
									$thankyou = get_post_meta($payment_array->post_id,'_successmsg',true);
									$message = $thankyou;
									$result = "success";
								}
							}
						}

			}else {
				$message = "Transaction Failed/Invalid Code";
				$result = "failed";
			}

		}
	}else{
		$message = "Payment Verification Failed.";
		$result = "failed";

	}

	if ($result == 'success') {
		$sendreceipt = get_post_meta($payment_array->post_id, '_sendreceipt', true);
		if($sendreceipt == 'yes'){
			$decoded = json_decode($payment_array->metadata);
			$fullname = $decoded[0]->value;
			kkd_pff_rave_send_receipt($payment_array->post_id,$currency,$amount_paid,$fullname,$payment_array->email,$rave_ref,$payment_array->metadata);
			kkd_pff_rave_send_receipt_owner($payment_array->post_id,$currency,$amount_paid,$fullname,$payment_array->email,$rave_ref,$payment_array->metadata);
			
		}

	}
	$response = array(
     'result' => $result,
     'message' => $message,
   );
	if ($result == 'success' && $redirect != '') {
	 $response['result'] = 'success2';
	 $response['link'] = $redirect;
	}

	 
  echo json_encode($response);

  die();
}

//getting the students details from the db
add_action( 'wp_ajax_kkd_pff_rave_select_student', 'kkd_pff_rave_select_student' );
add_action( 'wp_ajax_nopriv_kkd_pff_rave_select_student', 'kkd_pff_rave_select_student' );

function kkd_pff_rave_select_student() {

	$student_id = $_POST['stdid'];
 	global $wpdb;
	$table = $wpdb->prefix.KKD_PFF_RAVE_STUDENT_TABLE;
	$record = $wpdb->get_results("SELECT * FROM $table WHERE (student_id = '".$student_id."')");
	echo wp_send_json($record);
}