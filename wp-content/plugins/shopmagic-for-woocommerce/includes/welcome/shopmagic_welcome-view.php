<?php
/**
 * Welcome Page View
 *
 * Welcome page content i.e. HTML/CSS/PHP.
 *
 * @since 	1.0.0
 * @package SHOPMAGIC
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//Image folder location
$image_loc = SHOPMAGIC_PLUGIN_URL . 'assets/images/';

// Logo image
$logo_img = SHOPMAGIC_PLUGIN_URL . 'assets/images/ShopMagic_logo2-hd.png';

//Ribbon html
$proButton = '<a href="http://shopmagic.app/" target="blank" class="proButton">Get ShopMagic Pro</a>';

?>
<!-- HTML Started! -->
<div class="wrap about-wrap">

	<h1><?php printf( __( 'ShopMagic &nbsp;%s' ), SHOPMAGIC_VERSION ); ?></h1>

	<div class="about-text">
		<?php printf( __( "WooCommerce Marketing Automation to Make You More Money" ) ); ?>
	</div>

	<div class="wp-badge welcome__logo"></div>

	<div class="SM_instructions_section">
		<div class="SM_title"><h2>Getting Started</h2></div>
		<ul>
			<li><strong>Step #1:</strong> Start by creating a <a href='/wp-admin/post-new.php?post_type=shopmagic_automation' target='blank'>new Automation</a></li>
			<li><strong>Step #2:</strong> Choose an Event to trigger your automation. <i>Example: Order Completed</i> </li>
			<li><strong>Step #3:</strong> Choose an Action you'd like to happen. <i>Example: Send Email. Now write a followup email to their order</i> </li>
			<li><strong>You Got It!:</strong> <i>So now when a customer makes a purchase, ShopMagic will send them an automatic email congratulating them and thanking them for their purchase</i> </li>
		</ul>

		<div class="col1">
			<div class="SM_title"><h2>Setting Up Your 1st Automation</h2></div>
					<iframe width="400" height="225" src="https://www.youtube.com/embed/pSgCunkz02Q" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
		</div>

		<div class="col2">
			<div class="SM_title"><h2>Other Help Videos</h2></div>
					<iframe width="250" height="140" src="https://www.youtube.com/embed/-vImrWtAEZ0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
					<h3>How to Design Customized <br />Order Confirmation Emails</h3>

					<iframe width="250" height="140" src="https://www.youtube.com/embed/7rPE32DUq1o" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
					<h3>Automation Emails Not Sending?</h3>
		</div>
	</div>

	<div class="SM_feature_section">

			<div class="SM_title"><h2>Do More with ShopMagic Pro</h2></div>

			<table id="sm_feature_table">
				<tr>
			    <th class="sm_table_col_1"><img src="<?php _e( $image_loc ); ?>/delayedemails.png" /></th>
			    <th class="sm_table_col_2"><h3><?php _e( 'Delay Actions by Hours/Days/Months' ); ?></h3></th>
			    <th class="sm_table_col_3"><?php _e( 'Delay custom emails after purchase for a specified amount of time. (eg. 3 days after purchase)' ); ?></th>
					<th class="sm_table_col_4"><p><?php _e( '<span style="color:red">Pro</span><br />only' ); ?></th>
			  </tr>
				<tr>
			    <th class="sm_table_col_1"><img src="<?php _e( $image_loc ); ?>/review.png" width="128px" /></th>
			    <th class="sm_table_col_2"><h3><?php _e( 'Review Requests' ); ?></h3></th>
			    <th class="sm_table_col_3"><?php _e( 'Request a review from customers with an automated personalized email including direct links to leave reviews products purchased' ); ?></th>
					<th class="sm_table_col_4"><p><?php _e( '<span style="color:red">Pro</span><br />only' ); ?></th>
				</tr>
				<tr>
			    <th class="sm_table_col_1"><img src="<?php _e( $image_loc ); ?>/personalized-discounts.png" /></th>
			    <th class="sm_table_col_2"><h3><?php _e( 'Unique Customer Coupons' ); ?></h3></th>
			    <th class="sm_table_col_3"><?php _e( 'Give customers a unique, 1 time-only coupon code automatically after purchase' ); ?></th>
					<th class="sm_table_col_4"><p><?php _e( '<span style="color:red">Pro</span><br />only' ); ?></th>
			  </tr>
				<tr>
			    <th class="sm_table_col_1"><img src="<?php _e( $image_loc ); ?>/aweber.png" /></th>
			    <th class="sm_table_col_2"><h3><?php _e( 'Add Customers to Mailing List' ); ?></h3></th>
			    <th class="sm_table_col_3"><p><?php _e( 'Automatically subscribe new customers to various Mailing List providers after purchase. Currently supports: Mailchimp, Aweber, Active Campaign' ); ?></th>
					<th class="sm_table_col_4"><p><?php _e( '<span style="color:red">Pro</span><br />only' ); ?></th>
			  </tr>
				<tr>
			    <th class="sm_table_col_1"><img src="<?php _e( $image_loc ); ?>/upsell.png" /></th>
			    <th class="sm_table_col_2"><h3><?php _e( 'After Purchase Upsells' ); ?></h3></th>
			    <th class="sm_table_col_3"><p><?php _e( 'Immediately send customers to a different page after purchase to show them a one-time offer based on products purchased' ); ?></th>
					<th class="sm_table_col_4"><p><?php _e( '<span style="color:red">Pro</span><br />only' ); ?></th>
			  </tr>
				<tr>
			    <th class="sm_table_col_1"><img src="<?php _e( $image_loc ); ?>/abandon-cart.png" /></th>
			    <th class="sm_table_col_2"><h3><?php _e( 'Abandoned Cart' ); ?></h3></th>
			    <th class="sm_table_col_3"><p><?php _e( 'Sending an email (or take any action) after a potential customer abandons the checkout form to encourage them to finish their checkout' ); ?></th>
					<th class="sm_table_col_4"><p><?php _e( '<a href="#updates">Coming Soon!</a>' ); ?></th>
			  </tr>
				<tr>
			    <th class="sm_table_col_1"><img src="<?php _e( $image_loc ); ?>/sale-popups.png" /></th>
			    <th class="sm_table_col_2"><h3><?php _e( 'Recent Order Popups' ); ?></h3></th>
			    <th class="sm_table_col_3"><p><?php _e( 'Display popups on the frontend of your store each time a sale is made to encourage trust. Display popups for any products or only specific products or categories' ); ?></th>
			    <th class="sm_table_col_4"><p><?php _e( '<a href="#updates">Coming Soon!</a>' ); ?></th>
			  </tr>
			</table>

			<div class="SM_pro_offer">
				<?php echo $proButton; ?>
			</div>

	</div>

	<a name="subscribe">
		<div class="emailsignup-section">
			<a name="updates"><div class="SM_title"><h2>Signup to Get Updates about ShopMagic!</h2></div>
			<div class="SM_slogan"><h4>We'll email you periodically when we release new addons and add new features!</h4></div>
			<?php  _e(file_get_contents(SHOPMAGIC_PLUGIN_URL."/assets/activecampaign.txt")); ?>
		</div>
	</a>
</div>
<!-- HTML Ended! -->
