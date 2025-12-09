<?php
/**
 * This file contains the definition of the Animated_Headline_Visual_Composer_Public class, which
 * is used to load the plugin's public-facing functionality.
 *
 * @package       Animated_Headline_Visual_Composer
 * @subpackage    Animated_Headline_Visual_Composer/public
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version and other methods.
 *
 * @since    2.0.0
 */
class Animated_Headline_Visual_Composer_Public {
	/**
	 * The ID of this plugin.
	 *
	 * @since     2.0.0
	 * @access    private
	 * @var       string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since     2.0.0
	 * @access    private
	 * @var       string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     string $plugin_name The name of the plugin.
	 * @param     string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function enqueue_styles() {
		wp_register_style( $this->plugin_name, ANIMATED_HEADLINE_VISUAL_COMPOSER_PLUGIN_URL . 'public/css/public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function enqueue_scripts() {
		wp_register_script( $this->plugin_name, ANIMATED_HEADLINE_VISUAL_COMPOSER_PLUGIN_URL . 'public/js/public.js', array( 'jquery' ), $this->version, false );

		// Localize the script with animation_speed data.
		wp_localize_script(
			$this->plugin_name,
			'ANIMATED_HEADLINE_VISUAL_COMPOSER',
			array(
				'animation_speed' => intval( 2500 ),
			)
		);
	}

	/**
	 * Handles the shortcode callback for the animated headline.
	 *
	 * This function processes the animated headline shortcode, enqueues necessary CSS and JS files,
	 * extracts shortcode attributes, localizes the script with animation speed data, and generates
	 * the HTML output for the animated headline.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     array $atts Shortcode attributes.
	 * @return    string      The generated HTML for the animated headline.
	 */
	public function shortcode_callback( $atts ) {
		// Load on demand CSS & JS files.
		wp_enqueue_style( $this->plugin_name );
		wp_enqueue_script( $this->plugin_name );

		// Extract shortcode attributes with defaults.
		$atts = shortcode_atts(
			array(
				'title'           => __( 'Animated Heading Title', 'animated-headline-visual-composer' ),
				'animation_type'  => 'rotate-1',
				'animation_texts' => __( 'Hello,World,Animated', 'animated-headline-visual-composer' ),
				'animation_speed' => 2500,
			),
			$atts
		);

		// Localize the script with animation_speed data.
		wp_localize_script(
			$this->plugin_name,
			'ANIMATED_HEADLINE_VISUAL_COMPOSER',
			array(
				'animation_speed' => intval( $atts['animation_speed'] ),
			)
		);

		// Add 'letters' class to specific animation types.
		switch ( $atts['animation_type'] ) {
			case 'rotate-2':
			case 'rotate-3':
			case 'type':
			case 'scale':
				$atts['animation_type'] .= 'letters';
				break;
		}

		// Apply filter to animation texts and sanitize.
		$animation_texts = explode( ',', $atts['animation_texts'] );

		// Generate the HTML output.
		$html  = '<h1 class="cd-headline ' . esc_attr( $atts['animation_type'] ) . '"><span> ' . esc_html( $atts['title'] ) . ' </span>';
		$html .= '<span class="cd-words-wrapper">';
		$html .= apply_filters( 'ahvc_animation_texts', implode( ',', array_map( 'esc_html', $animation_texts ) ) );
		$html .= '</span></h1>';

		return $html;
	}

	/**
	 * Prepares the animated texts for display.
	 *
	 * This function takes a comma-separated string of animated texts, converts it into
	 * an array, and generates the HTML markup for each text, adding the 'is-visible' class
	 * to the first element.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     string $animation_texts A comma-separated string of animated texts.
	 * @return    string                  The HTML markup for the animated texts.
	 */
	public function prepare_animated_texts( $animation_texts ) {
		// First convert ',' separeted texts into array.
		$animation_texts = explode( ',', $animation_texts );

		// Second is-visible class to the first element.
		$html = '';

		$animation_texts_count = count( $animation_texts );

		for ( $x = 0; $x < $animation_texts_count; $x++ ) {
			$add_class = ( 0 === $x ) ? 'is-visible' : '';
			$html     .= '<b class="' . $add_class . '">' . $animation_texts[ $x ] . '</b>' . "\n";
		}

		return $html;
	}

	/**
	 * Initializes the Visual Composer shortcode and parameters.
	 *
	 * This function registers a custom parameter type ('raw_html'), maps the animated headline
	 * shortcode to Visual Composer, and defines the shortcode's parameters.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function vc_before_init() {
		$plugins_url = ANIMATED_HEADLINE_VISUAL_COMPOSER_PLUGIN_URL;

		vc_add_shortcode_param( 'raw_html', array( $this, 'add_custom_param_type' ) );

		vc_map(
			array(
				'name'              => __( 'Animated Headline', 'animated-headline-visual-composer' ),
				'base'              => 'animated_headline_vc',
				'description'       => __( '10+ animation texts effects', 'animated-headline-visual-composer' ),
				'class'             => 'animated_headline_vc',
				'icon'              => $plugins_url . '/admin/images/animation.svg',
				'category'          => __( 'Content', 'animated-headline-visual-composer' ),
				'front_enqueue_js'  => array(
					$plugins_url . '/admin/js/jquery.tagsinput-revisited.js',
					$plugins_url . '/admin/js/animated_heading_vc.js',
					$plugins_url . '/admin/js/admin.js',
				),
				'front_enqueue_css' => array(
					$plugins_url . '/admin/css/jquery.tagsinput-revisited.css',
					$plugins_url . '/admin/css/heading_animations.css',
					$plugins_url . '/admin/css/admin.css',
				),
				'admin_enqueue_js'  => array(
					$plugins_url . '/admin/js/jquery.tagsinput-revisited.js',
					$plugins_url . '/admin/js/animated_heading_vc.js',
					$plugins_url . '/admin/js/admin.js',
				),
				'admin_enqueue_css' => array(
					$plugins_url . '/admin/css/jquery.tagsinput-revisited.css',
					$plugins_url . '/admin/css/heading_animations.css',
					$plugins_url . '/admin/css/admin.css',
				),
				'params'            => array(
					array(
						'type'        => 'textfield',
						'holder'      => 'div',
						'class'       => 'animation_texts_title',
						'heading'     => __( 'Title', 'animated-headline-visual-composer' ),
						'param_name'  => 'title',
						'value'       => '',
						'description' => __( 'Enter title (Goes Before Animated Texts)', 'animated-headline-visual-composer' ),
					),
					array(
						'type'        => 'textfield',
						'holder'      => 'div',
						'class'       => 'animation_texts_options',
						'heading'     => __( 'Animated Texts', 'animated-headline-visual-composer' ),
						'param_name'  => 'animation_texts',
						'value'       => '',
						'description' => __( 'Enter Texts You Want to Animate', 'animated-headline-visual-composer' ),
					),
					array(
						'type'        => 'dropdown',
						'holder'      => 'div',
						'class'       => 'animation_type',
						'heading'     => __( 'Animation Type', 'animated-headline-visual-composer' ),
						'param_name'  => 'animation_type',
						'value'       => array(
							__( 'Choose Type', 'animated-headline-visual-composer' ) => '',
							__( 'Rotate 1', 'animated-headline-visual-composer' )    => 'rotate-1',
							__( 'Rotate 2', 'animated-headline-visual-composer' )    => 'rotate-2',
							__( 'Rotate 3', 'animated-headline-visual-composer' )    => 'rotate-3',
							__( 'Type', 'animated-headline-visual-composer' )        => 'type',
							__( 'Loading Bar', 'animated-headline-visual-composer' ) => 'loading-bar',
							__( 'Slide', 'animated-headline-visual-composer' )       => 'slide',
							__( 'Clip', 'animated-headline-visual-composer' )        => 'clip',
							__( 'Zoom', 'animated-headline-visual-composer' )        => 'zoom',
							__( 'Scale', 'animated-headline-visual-composer' )       => 'scale',
							__( 'Push', 'animated-headline-visual-composer' )        => 'push',
						),
						'description' => __( 'Select Animation Type', 'animated-headline-visual-composer' ),
					),
					array(
						'type'        => 'textfield',
						'holder'      => 'div',
						'class'       => 'animation_texts_speed',
						'heading'     => __( 'Animation Speed', 'animated-headline-visual-composer' ),
						'param_name'  => 'animation_speed',
						'value'       => '',
						'description' => __( 'Enter Animation Speed (Default 2500ms). Note : [1000ms = 1 second]. Enter only number without ms text.', 'animated-headline-visual-composer' ),
					),
					array(
						'type'        => 'raw_html',
						'holder'      => 'div',
						'class'       => 'animation_preview',
						'heading'     => __( 'Animation Preview', 'animated-headline-visual-composer' ),
						'param_name'  => 'animation_preview',
						'value'       => '',
						'description' => __( 'Choose Different Animation Type to Preview it here', 'animated-headline-visual-composer' ),
					),
				),
			)
		);
	}

	/**
	 * Adds a custom parameter type for raw HTML in Visual Composer.
	 *
	 * This function creates a container for raw HTML content within the Visual Composer editor.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     array  $settings An array of parameter settings.
	 * @param     string $value    The current value of the parameter.
	 * @return    string           The HTML markup for the raw HTML container.
	 */
	public function add_custom_param_type( $settings, $value ) {
		$default_settings = $settings;
		$default_value    = $value;
		return '<div class="raw_html_container"></div>';
	}
}
