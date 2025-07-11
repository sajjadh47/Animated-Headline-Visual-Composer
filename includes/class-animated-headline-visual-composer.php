<?php
/**
 * This file contains the definition of the Animated_Headline_Visual_Composer class, which
 * is used to begin the plugin's functionality.
 *
 * @package       Animated_Headline_Visual_Composer
 * @subpackage    Animated_Headline_Visual_Composer/includes
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

/**
 * The core plugin class.
 *
 * This is used to define admin-specific hooks and public-facing hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since    2.0.0
 */
class Animated_Headline_Visual_Composer {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since     2.0.0
	 * @access    protected
	 * @var       Animated_Headline_Visual_Composer_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since     2.0.0
	 * @access    protected
	 * @var       string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since     2.0.0
	 * @access    protected
	 * @var       string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function __construct() {
		$this->version     = defined( 'ANIMATED_HEADLINE_VISUAL_COMPOSER_PLUGIN_VERSION' ) ? ANIMATED_HEADLINE_VISUAL_COMPOSER_PLUGIN_VERSION : '1.0.0';
		$this->plugin_name = 'animated-headline-visual-composer';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Animated_Headline_Visual_Composer_Loader. Orchestrates the hooks of the plugin.
	 * - Animated_Headline_Visual_Composer_Admin.  Defines all hooks for the admin area.
	 * - Animated_Headline_Visual_Composer_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since     2.0.0
	 * @access    private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once ANIMATED_HEADLINE_VISUAL_COMPOSER_PLUGIN_PATH . 'includes/class-animated-headline-visual-composer-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once ANIMATED_HEADLINE_VISUAL_COMPOSER_PLUGIN_PATH . 'admin/class-animated-headline-visual-composer-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once ANIMATED_HEADLINE_VISUAL_COMPOSER_PLUGIN_PATH . 'public/class-animated-headline-visual-composer-public.php';

		$this->loader = new Animated_Headline_Visual_Composer_Loader();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since     2.0.0
	 * @access    private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Animated_Headline_Visual_Composer_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_notices', $plugin_admin, 'admin_notices' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since     2.0.0
	 * @access    private
	 */
	private function define_public_hooks() {
		$plugin_public = new Animated_Headline_Visual_Composer_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'vc_before_init', $plugin_public, 'vc_before_init' );

		$this->loader->add_filter( 'ahvc_animation_texts', $plugin_public, 'prepare_animated_texts' );

		add_shortcode( 'animated_headline_vc', array( $plugin_public, 'shortcode_callback' ) );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of WordPress.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @return    string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @return    Animated_Headline_Visual_Composer_Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @return    string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
