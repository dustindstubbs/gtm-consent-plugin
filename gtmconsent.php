<?php

/**
 * @package GTMConsent
 */

/*
Plugin Name: GTM Consent
Description: A simple solution for managing user consent for a GTM container.
Version: 0.1.3
Author: Dustin Stubbs
License GPLv2 or later
*/

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class GTMConsent
{

	public $plugin;

	//Passing variable to __construct for classes
	function __construct() {
		$this->plugin = plugin_basename( __FILE__ );
	}

	function register() {
		// frontend css
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

		// Add plugin page links
		add_filter( "plugin_action_links_$this->plugin", array( $this, 'settingsLink') );

		add_shortcode( 'consent_popup', array( $this, 'consentPopup' ) );

		add_shortcode( 'consent_buttons', array( $this, 'consentButtons' ) );

	}

	public function editVerse() {
		$data = $_POST;
		if ($data['usage'] == 'accept') {
			echo get_option('gtm_consent_option_name')['container_0'];
		}
		die;
	}

	public function consentPopup() {
		$consentContainer = get_option('gtm_consent_option_name')['container_0'];
		$consentDisclaimer = get_option('gtm_consent_option_name')['disclaimer_1'];
		$consentBackground = get_option('gtm_consent_option_name')['background_2'];
		if ($consentBackground == 'light') {
			$consentTheme = "bg-white text-dark";
		}else{
			$consentTheme = "bg-dark text-white";
		}
		 

		// Generate GTM consent popup
		echo "
		<div id='gc-popup' class='d-none card gc-card position-fixed $consentTheme p-3 rounded start-0 bottom-0 m-sm-2'>
			<div class='card-body'>
				$consentDisclaimer
			</div>
			<div class='border-0 d-flex'>
				<button id='reject' class='gc-btn-reject flex-fill btn btn-$consentBackground' onclick='scriptReject()'>Only Essential</button>
				<button id='accept' class='gc-btn-accept flex-fill btn btn-primary ms-2' onclick='scriptAccept()'>Accept All</button>
			</div>
		</div>

		<!-- Google tag (gtag.js) -->
		<script async src='https://www.googletagmanager.com/gtag/js?id=$consentContainer'></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}

			gtag('consent', 'default', {
				'ad_storage': 'denied',
				'ad_personalization': 'denied',
				'ad_user_data': 'denied',
				'analytics_storage': 'denied'
			});

			gtag('js', new Date());
			gtag('config', '$consentContainer');
		</script>
		";
	}

	public function consentButtons() {

		// Place consent buttons
		echo "
		<button id='reject' class='gc-btn-reject flex-fill btn btn-dark' onclick='scriptReject()'>Only Essential</button>
		<button id='accept' class='gc-btn-accept flex-fill btn btn-primary ms-2' onclick='scriptAccept()'>Accept All</button>
		";
	}

	public function settingsLink( $links ) {
		$settingsLink = '<a href="tools.php?page=gtm-consent">Settings</a>';
		array_push( $links, $settingsLink );
		return $links;
	}

	function activate() {
		flush_rewrite_rules();
	}

	function deactivate() {
		flush_rewrite_rules();
	}


	function enqueue() {
		//enqueue all of our scripts
		wp_enqueue_style( 'GTMConsentstyle', plugins_url( '/assets/style.css', __FILE__ ) );
		wp_enqueue_script( 'GTMConsentscript', plugins_url( '/assets/main.js', __FILE__ ) );
	}

}

if ( class_exists( 'GTMConsent' ) ) {
	$GTMConsent = new GTMConsent();
	$GTMConsent->register();
}

require_once plugin_dir_path( __FILE__ ) . 'templates/settings.php';

// activation
register_activation_hook( __FILE__, array( $GTMConsent, 'activate' ) );

// deactivation
register_deactivation_hook( __FILE__, array( $GTMConsent, 'deactivate' ) );



