<?php
/**
 * Plugin Name: Betting Banner
 * Plugin URI: https://example.com/betting-banner
 * Description: Egy elegáns fogadási banner egy shortcode-dal [betting_banner] a WordPress oldalakhoz. Elementor-kompatibilis.
 * Version: 1.0.0
 * Author: Claude AI
 * Author URI: https://example.com
 * Text Domain: betting-banner
 */

// Biztonsági ellenőrzés
if (!defined('ABSPATH')) {
    exit; // Direct access not allowed
}

class Betting_Banner_Plugin {
    
    /**
     * Konstruktor - Plugin inicializálása
     */
    public function __construct() {
        // Hook a shortcode regisztrálásához
        add_shortcode('betting_banner', array($this, 'render_betting_banner'));
        
        // Admin menü regisztrálása
        add_action('admin_menu', array($this, 'register_admin_menu'));
        
        // Admin stílusok és scriptek betöltése
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // Frontend stílusok és scriptek betöltése
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        
        // Beállítások inicializálása
        register_activation_hook(__FILE__, array($this, 'plugin_activation'));
        
        // Elementor widget regisztrálása (ha az Elementor telepítve van)
        add_action('elementor/widgets/widgets_registered', array($this, 'register_elementor_widget'));
        
        // AJAX handler a banner szerkesztéséhez
        add_action('wp_ajax_update_betting_banner', array($this, 'ajax_update_banner'));
        
        // Init hook a plugin fordításához
        add_action('plugins_loaded', array($this, 'load_textdomain'));
    }
    
    /**
     * Plugin nyelvfájlok betöltése
     */
    public function load_textdomain() {
        load_plugin_textdomain('betting-banner', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Plugin aktiválása
     */
    public function plugin_activation() {
        // Alapértelmezett beállítások létrehozása
        $default_settings = array(
            'title' => 'Online Fogadási Kalauz',
            'subtitle' => 'A Világ Legszakértőbb Sportfogadási Közössége',
            'trust_text' => 'Több mint 2 millió felhasználó által megbízhatónak találva',
            'button_text' => 'Kezdd el a Fogadást Most',
            'button_url' => '#',
            'primary_color' => '#1e40af',
            'secondary_color' => '#f97316',
            'bg_gradient_start' => '#1e3a8a',
            'bg_gradient_middle' => '#1e40af',
            'bg_gradient_end' => '#1e3a8a',
            'features' => array(
                array(
                    'title' => 'Versenyek',
                    'value' => '£1.01m',
                    'description' => 'Összes nyeremény',
                    'icon' => 'trophy'
                ),
                array(
                    'title' => 'Szakértői Tippek',
                    'value' => '97%',
                    'description' => 'Sikerességi ráta',
                    'icon' => 'brain'
                ),
                array(
                    'title' => 'Fogadóirodák',
                    'value' => '146+',
                    'description' => 'Megbízható partner',
                    'icon' => 'file-text'
                ),
                array(
                    'title' => 'Ingyenes Fogadások',
                    'value' => '£2,710',
                    'description' => 'Elérhető bónuszok',
                    'icon' => 'gift'
                )
            )
        );
        
        // Beállítások mentése az adatbázisba
        update_option('betting_banner_settings', $default_settings);
    }
    
    /**
     * Admin menü regisztrálása
     */
    public function register_admin_menu() {
        add_menu_page(
            __('Fogadási Banner', 'betting-banner'),
            __('Fogadási Banner', 'betting-banner'),
            'manage_options',
            'betting-banner',
            array($this, 'render_admin_page'),
            'dashicons-awards',
            30
        );
    }
    
    /**
     * Admin stílusok és scriptek betöltése
     */
    public function enqueue_admin_assets($hook) {
        // Csak a plugin admin oldalán töltse be
        if ('toplevel_page_betting-banner' !== $hook) {
            return;
        }
        
        // Admin stílusok
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('betting-banner-admin', plugins_url('assets/css/admin.css', __FILE__), array(), '1.0.0');
        
        // Admin scriptek
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('betting-banner-admin', plugins_url('assets/js/admin.js', __FILE__), array('jquery', 'wp-color-picker'), '1.0.0', true);
        
        // AJAX adatok átadása
        wp_localize_script('betting-banner-admin', 'betting_banner', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('betting_banner_nonce')
        ));
    }
    
    /**
     * Frontend stílusok és scriptek betöltése
     */
    public function enqueue_frontend_assets() {
        // Banner stílusok
        wp_enqueue_style('betting-banner', plugins_url('assets/css/betting-banner.css', __FILE__), array(), '1.0.0');
    }
    
    /**
     * Admin oldal renderelése
     */
    public function render_admin_page() {
        // Beállítások lekérése
        $settings = get_option('betting_banner_settings');
        
        // Admin oldal HTML
        require_once plugin_dir_path(__FILE__) . 'templates/admin-page.php';
    }
    
    /**
     * AJAX handler a banner beállításainak frissítéséhez
     */
    public function ajax_update_banner() {
        // Biztonsági ellenőrzés
        check_ajax_referer('betting_banner_nonce', 'nonce');
        
        // Jogosultság ellenőrzése
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Nincs jogosultságod a beállítások módosításához.', 'betting-banner')));
            return;
        }
        
        // Adatok validálása és mentése
        if (isset($_POST['settings'])) {
            $settings = json_decode(stripslashes($_POST['settings']), true);
            
            // Egyszerű sanitizálás
            $sanitized_settings = array(
                'title' => sanitize_text_field($settings['title']),
                'subtitle' => sanitize_text_field($settings['subtitle']),
                'trust_text' => sanitize_text_field($settings['trust_text']),
                'button_text' => sanitize_text_field($settings['button_text']),
                'button_url' => esc_url_raw($settings['button_url']),
                'primary_color' => sanitize_hex_color($settings['primary_color']),
                'secondary_color' => sanitize_hex_color($settings['secondary_color']),
                'bg_gradient_start' => sanitize_hex_color($settings['bg_gradient_start']),
                'bg_gradient_middle' => sanitize_hex_color($settings['bg_gradient_middle']),
                'bg_gradient_end' => sanitize_hex_color($settings['bg_gradient_end']),
                'features' => array()
            );
            
            // Features kezelése
            foreach ($settings['features'] as $feature) {
                $sanitized_settings['features'][] = array(
                    'title' => sanitize_text_field($feature['title']),
                    'value' => sanitize_text_field($feature['value']),
                    'description' => sanitize_text_field($feature['description']),
                    'icon' => sanitize_key($feature['icon'])
                );
            }
            
            // Beállítások mentése
            update_option('betting_banner_settings', $sanitized_settings);
            
            wp_send_json_success(array('message' => __('Beállítások sikeresen mentve!', 'betting-banner')));
        } else {
            wp_send_json_error(array('message' => __('Hiányzó adatok.', 'betting-banner')));
        }
    }
    
    /**
     * Shortcode renderelése
     */
    public function render_betting_banner($atts = array()) {
        // Shortcode attribútumok feldolgozása
        $atts = shortcode_atts(array(
            'color' => '', // Egyéni szín felülírás
            'button_text' => '', // Egyéni gomb szöveg
            'button_url' => '', // Egyéni gomb URL
        ), $atts, 'betting_banner');
        
        // Beállítások lekérése
        $settings = get_option('betting_banner_settings');
        
        // Shortcode attribútumok alkalmazása, ha meg vannak adva
        if (!empty($atts['color'])) {
            $settings['secondary_color'] = $atts['color'];
        }
        
        if (!empty($atts['button_text'])) {
            $settings['button_text'] = $atts['button_text'];
        }
        
        if (!empty($atts['button_url'])) {
            $settings['button_url'] = $atts['button_url'];
        }
        
        // Output buffer indítása
        ob_start();
        
        // Banner HTML betöltése
        require_once plugin_dir_path(__FILE__) . 'templates/banner.php';
        
        // Output buffer visszaadása
        return ob_get_clean();
    }
    
    /**
     * Elementor widget regisztrálása
     */
    public function register_elementor_widget() {
        // Ellenőrizzük, hogy az Elementor be van-e töltve
        if (!did_action('elementor/loaded')) {
            return;
        }
        
        // Elementor widget regisztrálása
        require_once plugin_dir_path(__FILE__) . 'elementor/betting-banner-widget.php';
        
        // Widget regisztrálása
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Betting_Banner_Elementor_Widget());
    }
    
    /**
     * SVG ikonok lekérése
     */
    public function get_svg_icon($icon_name) {
        $icons = array(
            'trophy' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path></svg>',
            
            'brain' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 9L12 5 2 9l10 4 10-4v6"></path><path d="M6 10.6V16c0 2 3 3 6 3s6-1 6-3v-5.4"></path></svg>',
            
            'file-text' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg>',
            
            'gift' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V8H6a2 2 0 0 1 0-4h12v4"></path><path d="M4 6v12c0 1.1.9 2 2 2h14v-4"></path><path d="M18 12a2 2 0 0 0-2 2c0 1.1.9 2 2 2h4v-4h-4z"></path></svg>',
            
            'chevron-right' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>'
        );
        
        return isset($icons[$icon_name]) ? $icons[$icon_name] : '';
    }
}

// Plugin inicializálása
$betting_banner_plugin = new Betting_Banner_Plugin();
