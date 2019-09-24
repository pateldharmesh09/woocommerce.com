<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'MailChimp' ) ) {
    require_once(SHOPMAGIC_BASE_DIR.'/includes/api/other/mailchimp/MailChimp.php');
}


/**
 * MailChimp Tools for ShopMagic
 *
 * @since   1.0.0
 */
class MailChimp_API_Tools extends MailChimp
{
	function __construct($api_key, $verify_ssl = false){
		// "verify_ssl" Will probably fire an SSL error if set to "true"
		parent::__construct($api_key, $verify_ssl);		

	}
	
	/*
	*
	*
	*/
	function add_member_from_order($order, $mailchimp_list_id, $mailchimp_doubleoptin){
		
            // Quit if API key or List ID are not set
            if( $this->getApiKey() == false || $mailchimp_list_id == '' ){
                return false;
            }

            if ( ! class_exists( 'MailChimp' ) ) {
                require_once(SHOPMAGIC_BASE_DIR.'/includes/api/other/mailchimp/MailChimp.php');
            }

            // Get further information settings
            $mailchimp_further_information = array(
                'LNAME' => get_option('wc_settings_tab_mailchimp_info_lname',false),
                'ADDRESS'   => get_option('wc_settings_tab_mailchimp_info_address',false),
                'CITY'      => get_option('wc_settings_tab_mailchimp_info_city',false),
                'STATE'     => get_option('wc_settings_tab_mailchimp_info_state',false),
                'COUNTRY'   => get_option('wc_settings_tab_mailchimp_info_country',false),
            );


            $billing_email = $order->billing_email;            
            $billing_fname = $order->billing_first_name;

            // Last name depends on the further information settings
            $billing_lname = ( $mailchimp_further_information['LNAME'] == 'yes'? $order->billing_last_name : '' );

            // Get further information ( Billing )
            $billing_address = $order->billing_address_1.' '.$order->billing_address_2;
            $billing_city = $order->billing_city;
            $billing_state = $order->billing_state;
            $billing_country = $order->billing_country;

            // Process
            if(!empty($billing_email) && !filter_var($billing_email, FILTER_VALIDATE_EMAIL) === false){
                // MailChimp API credentials
                $apiKey = $this->getApiKey();
                $listID = $mailchimp_list_id;
                
                $member_status = 'subscribed';

                // If Double-optin checked
                if( in_array(strtolower($mailchimp_doubleoptin), array('on','yes') ) ){
                    $member_status = 'pending';
                }

                // member information
                $mailchimp_add_member_params = array(
                    'email_address' => $billing_email,
                    'status'        => $member_status,
                    'merge_fields'  => array(
                        'FNAME'     => $billing_fname,
                        'LNAME'     => $billing_lname
                    )
                );

                // Look for new 'merge-fields' and add them if necessary
                    // Get new merge-fields 'TAG'=>'name'
                    $mailchimp_new_mergefields = array(
                        'ADDRESS' => 'Address',
                        'CITY' => 'City',
                        'STATE' => 'State',
                        'COUNTRY' => 'Country'
                    );
                    
                    foreach ($mailchimp_new_mergefields as $tag => $name) {

                        // If information checked checked on the settings
                        if( $mailchimp_further_information[$tag] == 'yes' ){
                            $mailchimp_add_mergefield_params = array(
                                'tag'=>$tag,
                                'name'=>$name,
                                'type'=>'text'
                            );

                            // MailChimp API Call for adding new merge-field
                            $this->add_merge_field_from_order($mailchimp_list_id, $mailchimp_add_mergefield_params);

                            if (!$this->success()) {
                                error_log($this->getLastError());
                            }
                            // Change params depending further information settings ( from WC settings ShopMagic )

                            switch ( $tag ) {
                                case 'ADDRESS':
                                    $mailchimp_add_member_params['merge_fields']['ADDRESS'] = $billing_address;
                                    break;

                                case 'CITY':
                                    $mailchimp_add_member_params['merge_fields']['CITY'] = $billing_city;
                                    break;

                                case 'STATE':
                                    $mailchimp_add_member_params['merge_fields']['STATE'] = $billing_state;
                                    break;

                                case 'COUNTRY':
                                    $mailchimp_add_member_params['merge_fields']['COUNTRY'] = $billing_country;
                                    break;
                            }
                        }

                    }

                // MailChimp API Call for adding new member
                $this->post(
                    "lists/". $mailchimp_list_id ."/members",
                    $mailchimp_add_member_params
                );

                if (!$this->success()) {
                    error_log($this->getLastError());
                }
            }
	}

	/*
	*
	*
	*/
	function add_merge_field_from_order($mailchimp_list_id, $params){

        // MailChimp API Call for adding new merge-field
        $this->post(
            "lists/". $mailchimp_list_id ."/merge-fields",
            $params                                
        );
	}

    /**
     * Extract the lists names and id to be used on options for the select element 'List name'
     *
     * @since   1.0.0
     */
    function get_all_lists_options(){
        // Get the list of lists
        $lists_options = array(
                                "0"=>__( 'Select a list', 'shopmagic' ),
                        );
        $lists = $this->get('lists');

        if( $this->success() ){
            if( count($lists['lists']) > 0 ){
            // If one list or more
                foreach ($lists['lists'] as $key => $list_obj) {
                    $lists_options[$list_obj['id']] = $list_obj['name'].' ['.$list_obj['id'].']';
                }
            }else{
            // If no lists yet or an error
                $lists_options = array(
                                    "0"=>__( 'No lists are set yet !', 'shopmagic' ),
                                );
            }
        }else{
        // If an error is there
            $lists_options = array(
                                "0"=>__( 'Please make sure about the MailChimp API key provided !', 'shopmagic' )
                            );
        }

        return $lists_options;
    }
}