<?php
/**
 * Plugin Name: Agri Weather Map
 * Description: Displays an agriculture-friendly weather overlay map using Leaflet + OpenStreetMap.
 * Version: 1.0
 * Author: 
 * Text Domain: agri-weather-map
 */

if (!defined('ABSPATH')) {
    exit;
}

define('AGRI_WEATHER_MAP_VERSION', '1.0');
define('AGRI_WEATHER_MAP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AGRI_WEATHER_MAP_PLUGIN_URL', plugin_dir_url(__FILE__));

class AgriWeatherMap {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_shortcode('agri_weather_map', array($this, 'render_shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    public function add_admin_menu() {
        add_options_page(
            'Agri Weather Map Settings',
            'Agri Weather Map',
            'manage_options',
            'agri-weather-map',
            array($this, 'render_admin_page')
        );
    }
    
    public function register_settings() {
        register_setting('agri_weather_map_settings', 'agri_weather_map_options', array($this, 'sanitize_settings'));
        
        add_settings_section(
            'agri_weather_map_main',
            'Map Settings',
            null,
            'agri-weather-map'
        );
        
        add_settings_field(
            'default_zoom',
            'Default Zoom Level',
            array($this, 'render_zoom_field'),
            'agri-weather-map',
            'agri_weather_map_main'
        );
        
        add_settings_field(
            'default_lat',
            'Default Center Latitude',
            array($this, 'render_lat_field'),
            'agri-weather-map',
            'agri_weather_map_main'
        );
        
        add_settings_field(
            'default_lng',
            'Default Center Longitude',
            array($this, 'render_lng_field'),
            'agri-weather-map',
            'agri_weather_map_main'
        );
        
        add_settings_field(
            'farmer_friendly',
            'Enable Farmer Friendly Mode',
            array($this, 'render_farmer_friendly_field'),
            'agri-weather-map',
            'agri_weather_map_main'
        );
    }
    
    public function sanitize_settings($input) {
        $sanitized = array();
        
        if (isset($input['default_zoom'])) {
            $sanitized['default_zoom'] = absint($input['default_zoom']);
            if ($sanitized['default_zoom'] < 1) $sanitized['default_zoom'] = 6;
            if ($sanitized['default_zoom'] > 18) $sanitized['default_zoom'] = 18;
        }
        
        if (isset($input['default_lat'])) {
            $sanitized['default_lat'] = floatval($input['default_lat']);
        }
        
        if (isset($input['default_lng'])) {
            $sanitized['default_lng'] = floatval($input['default_lng']);
        }
        
        $sanitized['farmer_friendly'] = isset($input['farmer_friendly']) ? 1 : 0;
        
        return $sanitized;
    }
    
    public function render_zoom_field() {
        $options = $this->get_options();
        echo '<input type="number" name="agri_weather_map_options[default_zoom]" value="' . esc_attr($options['default_zoom']) . '" min="1" max="18" />';
        echo '<p class="description">Zoom level between 1-18 (default: 6)</p>';
    }
    
    public function render_lat_field() {
        $options = $this->get_options();
        echo '<input type="text" name="agri_weather_map_options[default_lat]" value="' . esc_attr($options['default_lat']) . '" />';
        echo '<p class="description">Default latitude (e.g., 39.8283)</p>';
    }
    
    public function render_lng_field() {
        $options = $this->get_options();
        echo '<input type="text" name="agri_weather_map_options[default_lng]" value="' . esc_attr($options['default_lng']) . '" />';
        echo '<p class="description">Default longitude (e.g., -98.5795)</p>';
    }
    
    public function render_farmer_friendly_field() {
        $options = $this->get_options();
        echo '<input type="checkbox" name="agri_weather_map_options[farmer_friendly]" value="1" ' . checked($options['farmer_friendly'], 1, false) . ' />';
        echo '<p class="description">Enable larger icons and helpful weather tips for farmers</p>';
    }
    
    public function render_admin_page() {
        require_once AGRI_WEATHER_MAP_PLUGIN_DIR . 'admin/settings-page.php';
    }
    
    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'width' => '100%',
            'height' => '500px'
        ), $atts);
        
        $options = $this->get_options();
        
        ob_start();
        require AGRI_WEATHER_MAP_PLUGIN_DIR . 'frontend/shortcode-output.php';
        return ob_get_clean();
    }
    
    public function enqueue_scripts() {
        if (is_admin()) {
            return;
        }
        
        wp_enqueue_style(
            'leaflet-css',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
            array(),
            '1.9.4'
        );
        
        wp_enqueue_style(
            'agri-weather-map-css',
            AGRI_WEATHER_MAP_PLUGIN_URL . 'assets/css/style.css',
            array('leaflet-css'),
            AGRI_WEATHER_MAP_VERSION
        );
        
        wp_enqueue_script(
            'leaflet-js',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
            array(),
            '1.9.4',
            true
        );
        
        wp_enqueue_script(
            'agri-weather-map-js',
            AGRI_WEATHER_MAP_PLUGIN_URL . 'assets/js/map.js',
            array('leaflet-js'),
            AGRI_WEATHER_MAP_VERSION,
            true
        );
        
        $options = $this->get_options();
        
        wp_localize_script('agri-weather-map-js', 'agriWeatherMapSettings', array(
            'defaultZoom' => $options['default_zoom'],
            'defaultLat' => $options['default_lat'],
            'defaultLng' => $options['default_lng'],
            'farmerFriendly' => $options['farmer_friendly']
        ));
    }
    
    private function get_options() {
        $defaults = array(
            'default_zoom' => 6,
            'default_lat' => 39.8283,
            'default_lng' => -98.5795,
            'farmer_friendly' => 0
        );
        
        $options = get_option('agri_weather_map_options', array());
        return wp_parse_args($options, $defaults);
    }
}

function agri_weather_map_init() {
    AgriWeatherMap::get_instance();
}
add_action('plugins_loaded', 'agri_weather_map_init');
