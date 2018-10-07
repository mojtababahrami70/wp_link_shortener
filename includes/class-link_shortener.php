<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://mojtababah.com
 * @since      1.0.0
 *
 * @package    Link_shortener
 * @subpackage Link_shortener/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Link_shortener
 * @subpackage Link_shortener/includes
 * @author     Mojtaba Bahrami <mojtababahrami70@gmail.com>
 */
class Link_shortener
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Link_shortener_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * @var string custom post type name
     */
    private $cpt_name = 'short_link';

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('LINK_SHORTENER_VERSION'))
        {
            $this->version = LINK_SHORTENER_VERSION;
        }
        else
        {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'link_shortener';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Link_shortener_Loader. Orchestrates the hooks of the plugin.
     * - Link_shortener_i18n. Defines internationalization functionality.
     * - Link_shortener_Admin. Defines all hooks for the admin area.
     * - Link_shortener_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-link_shortener-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-link_shortener-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-link_shortener-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-link_shortener-public.php';

        $this->loader = new Link_shortener_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Link_shortener_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Link_shortener_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Link_shortener_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('init', $this, 'init');
        $this->loader->add_action('add_meta_boxes', $this, 'add_meta_box');
        $this->loader->add_action('save_post', $this, 'save_url');
        $this->loader->add_action('get_header', $this, 'check_url');

        $this->loader->add_filter('manage_edit-' . $this->cpt_name . '_columns', $this, 'custom_column');
        $this->loader->add_action('manage_' . $this->cpt_name . '_posts_custom_column', $this, 'column_content', 10, 2);

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Link_shortener_Public($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Link_shortener_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }


    public function init()
    {
        $args = array(
            'labels'             => array(
                'name'               => __('Short Urls', $this->plugin_name),
                'singular_name'      => __('Short Url', $this->plugin_name),
                'add_new'            => __('Add Url', $this->plugin_name),
                'add_new_item'       => __('Add New Url', $this->plugin_name),
                'edit_item'          => __('Edit Url', $this->plugin_name),
                'new_item'           => __('New Url', $this->plugin_name),
                'all_items'          => __('All Urls', $this->plugin_name),
                'view_item'          => __('View Url', $this->plugin_name),
                'search_items'       => __('Search Urls', $this->plugin_name),
                'not_found'          => __('No url found', $this->plugin_name),
                'not_found_in_trash' => __('No urls found in Trash', $this->plugin_name),
                'parent_item_colon'  => '',
                'menu_name'          => __('Url Shortener', $this->plugin_name),
            ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => true,
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title')
        );
        register_post_type($this->cpt_name, $args);
    }

    public function add_meta_box()
    {
        add_meta_box("lsh-meta", __("Short URL", $this->plugin_name), array(
            $this,
            'meta_box_content'
        ), $this->cpt_name, "normal", "low");
    }

    public function meta_box_content()
    {
        global $post;
        wp_nonce_field('lsh_nonce', 'lsh_nonce');

        $short_id = get_post_meta($post->ID, 'short_id', true);

        if (!empty($_GET['lsh_message'])) :
            switch ((int)$_GET['lsh_message']) :
                case 1:
                    echo '<div class="updated"><p>' . __("URL is not valid", $this->plugin_name) . '</p></div>';
                    break;
                case 2:
                    echo '<div class="updated"><p>' . __("Your custom short id is already existed so we generated other short id", $this->plugin_name) . '</p></div>';
                    break;
            endswitch;
        endif
        ?>
        <p>
            <label><?php echo __('Shorten Url', $this->plugin_name) ?>:</label>
            <input name="short_id" type="text" value="<?php echo $short_id ?>"/>
            <?php echo __("You can put your custom shorten url here if wanted!", $this->plugin_name) ?>
        </p>
        <?php
        if (!empty($short_id)) :
            ?>
            <p>
                <label><?php echo __('Shorten url') ?>:</label>
                <input size="50" type="text" value="<?php echo trailingslashit(get_bloginfo('url')), $short_id ?>"/>
                <a target="_blank"
                   href="<?php echo trailingslashit(get_bloginfo('url')), $short_id ?>"><?php echo __('Try it', $this->plugin_name) ?></a>
            </p>
        <?php endif ?>

        <?php
    }

    public function save_url($post_id)
    {
        global $post;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        // if our nonce isn't there, or we can't verify it, bail
        if (!isset($_POST['lsh_nonce']) || !wp_verify_nonce($_POST['lsh_nonce'], 'lsh_nonce'))
            return;

        // if our current user can't edit this post, bail
        if (!current_user_can('edit_post'))
            return;

        //Also, if the url is invalid, add custom message
        if (!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $post->post_title))
        {
            add_filter('redirect_post_location', array($this, 'invalid_url'));
        }

        $short_id = empty($_POST['short_id']) ? base_convert($post_id, 10, 36) : preg_replace('/[^a-z0-9_]/i', '_', $_POST['short_id']);
        $old_short_id = get_post_meta($post_id, 'short_id', true);
        if ($short_id == $old_short_id)
        {
            //We are updating post, and the short_id is not changed so it's not necessary to save again
            return;
        }
        //If our short_id already exists! Let regenerate till we get a new short_id
        while ($this->_findUrlByShortId($short_id))
        {
            $short_id = base_convert(time() + rand(1, 10000), 10, 36);
            add_filter('redirect_post_location', array($this, 'invalid_short_id'));
        }
        update_post_meta($post_id, 'short_id', $short_id);
    }

    /**
     * Find the original url respond to this short_id
     * @global wpdb $wpdb
     * @param string $short_id
     * @return bool or string
     */
    private function _findUrlByShortId($short_id)
    {
        global $wpdb;
        $sql = "SELECT m.post_id, p.post_title as url
            FROM {$wpdb->prefix}postmeta as m 
            LEFT JOIN {$wpdb->prefix}posts as p ON m.post_id=p.id
            WHERE  m.meta_key='short_id' AND m.meta_value='%s'
        ";
        $result = $wpdb->get_row($wpdb->prepare($sql, $short_id));
        if (!$result)
        {
            return false;
        }
        'http://' != substr($result->url, 0, '7') && $result->url = 'http://' . $result->url;
        return $result->url;
    }

    public function invalid_short_id($location)
    {
        return $location . '&lsh_message=2';
    }

    public function invalid_url($location)
    {
        return $location . '&lsh_message=1';
    }

    public function check_url()
    {
        global $wp_query;
        if (!$wp_query->is_404)
        {
            //This is a valid URL of WordPress!
            return false;
        }
        $short_id = empty($wp_query->query['pagename']) ? false : $wp_query->query['pagename'];
        if ($short_id && $url = $this->_findUrlByShortId($short_id))
        {
            wp_redirect($url);
        }
    }

    /**
     * @param array of columns
     * @return mixed
     */
    public function custom_column($columns)
    {
        $columns['short_id'] = __('Shorten Url');
        return $columns;
    }

    public function column_content($column_name, $post_id)
    {
        switch ($column_name)
        {
            case 'short_id':
                $short_id = get_post_meta($post_id, 'short_id', true);
                if ($short_id)
                {
                    echo sprintf('<a target="_blank" href="%s" title="Original URL">%s</a>', trailingslashit(get_bloginfo('url')), $short_id, $short_id);
                }
                break;
        }
    }
}
