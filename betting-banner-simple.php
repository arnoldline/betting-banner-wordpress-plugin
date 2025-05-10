<?php
/**
 * Plugin Name: Betting Banner Simple
 * Plugin URI: https://example.com/betting-banner
 * Description: Egy elegáns fogadási banner shortcode-dal [betting_banner] a WordPress oldalakhoz.
 * Version: 1.0.0
 * Author: Claude AI
 * Author URI: https://example.com
 * Text Domain: betting-banner
 */

// Biztonsági ellenőrzés
if (!defined('ABSPATH')) {
    exit; // Direct access not allowed
}

class Betting_Banner_Simple {
    
    /**
     * Konstruktor - Plugin inicializálása
     */
    public function __construct() {
        // Hook a shortcode regisztrálásához
        add_shortcode('betting_banner', array($this, 'render_betting_banner'));
        
        // Frontend stílusok és scriptek betöltése
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
    }
    
    /**
     * Frontend stílusok betöltése
     */
    public function enqueue_frontend_assets() {
        // Beágyazzuk a CSS-t inline, így nincs szükség külön fájlra
        wp_register_style('betting-banner-style', false);
        wp_enqueue_style('betting-banner-style');
        wp_add_inline_style('betting-banner-style', $this->get_banner_css());
    }
    
    /**
     * Segédfüggvény a szín világosításához/sötétítéséhez
     */
    public function adjust_brightness($hex, $steps) {
        // Eltávolítjuk a # karaktert, ha van
        $hex = ltrim($hex, '#');
        
        // Konvertáljuk a hexet RGB-be
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Világosítjuk/sötétítjük a színkomponenseket
        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));
        
        // Visszaalakítjuk hex-re
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
    
    /**
     * Shortcode renderelése
     */
    public function render_betting_banner($atts = array()) {
        // Shortcode attribútumok feldolgozása
        $atts = shortcode_atts(array(
            'color' => '#f97316', // Alapértelmezett narancs szín
            'button_text' => 'Kezdd el a Fogadást Most',
            'button_url' => '#',
            'title' => 'Online Fogadási Kalauz',
            'subtitle' => 'A Világ Legszakértőbb Sportfogadási Közössége',
        ), $atts, 'betting_banner');
        
        // Alap beállítások
        $primary_color = '#1e40af'; // Kék
        $secondary_color = $atts['color'];
        $bg_gradient_start = '#1e3a8a';
        $bg_gradient_middle = '#1e40af';
        $bg_gradient_end = '#1e3a8a';
        
        // Funkciókártyák
        $features = array(
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
        );
        
        // Egyedi ID generálása a bannerhez
        $banner_id = 'betting-banner-' . uniqid();
        
        // Dinamikus CSS generálása a beállított színekkel
        $dynamic_css = "
        <style>
        #$banner_id {
            background: linear-gradient(to right, $bg_gradient_start, $bg_gradient_middle, $bg_gradient_end);
        }
        #$banner_id .card-icon {
            background: linear-gradient(to bottom right, " . $this->adjust_brightness($primary_color, 40) . ", $primary_color);
        }
        #$banner_id .feature-card:hover .card-icon {
            background: linear-gradient(to bottom right, " . $this->adjust_brightness($secondary_color, 20) . ", $secondary_color);
        }
        #$banner_id .badge {
            background: linear-gradient(to bottom right, " . $this->adjust_brightness($primary_color, 20) . ", $primary_color);
        }
        #$banner_id .badge-container:hover .badge {
            background: linear-gradient(to bottom right, " . $this->adjust_brightness($primary_color, 40) . ", " . $this->adjust_brightness($primary_color, 20) . ");
        }
        #$banner_id .banner-title:hover,
        #$banner_id .feature-card:hover .card-title,
        #$banner_id .card-value {
            color: $secondary_color;
        }
        #$banner_id .banner-title::after {
            background-color: $secondary_color;
        }
        #$banner_id .cta-button {
            background: linear-gradient(to right, $secondary_color, " . $this->adjust_brightness($secondary_color, -20) . ");
        }
        #$banner_id .cta-button::before {
            background: linear-gradient(to right, " . $this->adjust_brightness($secondary_color, -20) . ", $secondary_color);
        }
        #$banner_id .cta-button::after {
            background-color: $secondary_color;
        }
        </style>
        ";
        
        // Kezdjük a kimenetet
        $output = $dynamic_css;
        
        // HTML összeállítása
        $output .= '<div id="' . esc_attr($banner_id) . '" class="betting-banner">';
        
        // Háttér effektek
        $output .= '<div class="banner-bg-effects">
                       <div class="bg-effect-1"></div>
                       <div class="bg-effect-2"></div>
                       <div class="bg-effect-3"></div>
                    </div>';
        
        // Banner tartalom
        $output .= '<div class="banner-content">';
        
        // Fejléc
        $output .= '<div class="banner-header">
                      <div class="header-text">
                        <h1 class="banner-title">' . esc_html($atts['title']) . '</h1>
                        <p class="banner-subtitle">' . esc_html($atts['subtitle']) . '</p>
                      </div>
                      
                      <div class="trust-badges">
                        <div class="badge-container">
                          <div class="badge">
                            <span>18+</span>
                          </div>
                        </div>
                        <div class="badge-container">
                          <div class="badge">
                            <span>SSL</span>
                          </div>
                        </div>
                        <div class="badge-container">
                          <div class="badge">
                            <span>✓</span>
                          </div>
                        </div>
                      </div>
                    </div>';
        
        // Bizalmi szöveg
        $output .= '<div class="trust-text">
                     <span>Több mint 2 millió felhasználó által megbízhatónak találva</span>
                   </div>';
        
        // Kártyák
        $output .= '<div class="feature-cards">';
        
        foreach ($features as $feature) {
            $output .= '<div class="feature-card">
                          <div class="card-header">
                            <div class="card-icon">
                              ' . $this->get_svg_icon($feature['icon']) . '
                            </div>
                            <h3 class="card-title">' . esc_html($feature['title']) . '</h3>
                          </div>
                          <div class="card-value">' . esc_html($feature['value']) . '</div>
                          <p class="card-description">' . esc_html($feature['description']) . '</p>
                        </div>';
        }
        
        $output .= '</div>'; // .feature-cards vége
        
        // CTA gomb
        $output .= '<div class="cta-container">
                     <a href="' . esc_url($atts['button_url']) . '" class="cta-button">
                       <span>' . esc_html($atts['button_text']) . '</span>
                       ' . $this->get_svg_icon('chevron-right') . '
                     </a>
                   </div>';
        
        $output .= '</div>'; // .banner-content vége
        $output .= '</div>'; // .betting-banner vége
        
        return $output;
    }
    
    /**
     * SVG ikonok lekérése
     */
    public function get_svg_icon($icon_name) {
        $icons = array(
            'trophy' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path></svg>',
            
            'brain' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 9L12 5 2 9l10 4 10-4v6"></path><path d="M6 10.6V16c0 2 3 3 6 3s6-1 6-3v-5.4"></path></svg>',
            
            'file-text' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>',
            
            'gift' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V8H6a2 2 0 0 1 0-4h12v4"></path><path d="M4 6v12c0 1.1.9 2 2 2h14v-4"></path><path d="M18 12a2 2 0 0 0-2 2c0 1.1.9 2 2 2h4v-4h-4z"></path></svg>',
            
            'chevron-right' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>'
        );
        
        return isset($icons[$icon_name]) ? $icons[$icon_name] : '';
    }
    
    /**
     * Banner CSS kód
     */
    public function get_banner_css() {
        return '
        /* Betting Banner CSS */
        .betting-banner {
          position: relative;
          width: 100%;
          padding: 40px 30px;
          border-radius: 12px;
          overflow: hidden;
          background: linear-gradient(to right, #1e3a8a, #1e40af, #1e3a8a);
          box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
          color: #fff;
          font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
          box-sizing: border-box;
        }
        
        .betting-banner * {
          box-sizing: border-box;
          margin: 0;
          padding: 0;
        }
        
        .banner-bg-effects {
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          opacity: 0.1;
          z-index: 0;
        }
        
        .bg-effect-1 {
          position: absolute;
          top: 25%;
          left: 25%;
          width: 160px;
          height: 160px;
          border-radius: 50%;
          background-color: white;
          filter: blur(40px);
        }
        
        .bg-effect-2 {
          position: absolute;
          bottom: 25%;
          right: 25%;
          width: 240px;
          height: 240px;
          border-radius: 50%;
          background-color: #60a5fa;
          filter: blur(40px);
        }
        
        .bg-effect-3 {
          position: absolute;
          top: 50%;
          right: 33%;
          width: 80px;
          height: 80px;
          border-radius: 50%;
          background-color: #fb923c;
          filter: blur(20px);
        }
        
        .banner-content {
          position: relative;
          z-index: 1;
        }
        
        .banner-header {
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: space-between;
          margin-bottom: 30px;
        }
        
        .header-text {
          text-align: center;
          margin-bottom: 20px;
        }
        
        .banner-title {
          font-size: 32px;
          font-weight: 700;
          color: white;
          margin: 0 0 10px 0;
          position: relative;
          display: inline-block;
          transition: color 0.3s;
          line-height: 1.2;
        }
        
        .banner-title:hover {
          color: #fb923c;
        }
        
        .banner-title::after {
          content: "";
          position: absolute;
          left: 0;
          bottom: -2px;
          width: 0;
          height: 2px;
          background-color: #fb923c;
          transition: width 0.5s;
        }
        
        .banner-title:hover::after {
          width: 100%;
        }
        
        .banner-subtitle {
          font-size: 18px;
          color: #93c5fd;
          margin: 0;
          font-weight: 500;
          transition: color 0.3s;
          line-height: 1.4;
        }
        
        .banner-subtitle:hover {
          color: #bfdbfe;
        }
        
        .trust-badges {
          display: flex;
          gap: 16px;
        }
        
        .badge-container {
          background-color: rgba(255, 255, 255, 0.1);
          padding: 8px;
          border-radius: 50%;
          box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
          transition: all 0.3s;
          cursor: pointer;
        }
        
        .badge-container:hover {
          background-color: rgba(255, 255, 255, 0.2);
          box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
          transform: scale(1.1);
        }
        
        .badge {
          width: 32px;
          height: 32px;
          border-radius: 50%;
          background: linear-gradient(to bottom right, #2563eb, #1e40af);
          display: flex;
          align-items: center;
          justify-content: center;
          transition: background 0.3s;
        }
        
        .badge-container:hover .badge {
          background: linear-gradient(to bottom right, #3b82f6, #2563eb);
        }
        
        .badge span {
          font-size: 14px;
          font-weight: 700;
          color: white;
        }
        
        .trust-text {
          text-align: center;
          background-color: rgba(255, 255, 255, 0.1);
          border-radius: 50px;
          padding: 8px 16px;
          margin: 0 auto 30px;
          display: inline-block;
          transition: all 0.3s;
          cursor: default;
        }
        
        .trust-text:hover {
          background-color: rgba(255, 255, 255, 0.15);
          transform: scale(1.05);
        }
        
        .trust-text span {
          font-size: 16px;
          font-weight: 500;
          color: #dbeafe;
        }
        
        .feature-cards {
          display: grid;
          grid-template-columns: repeat(4, 1fr);
          gap: 16px;
          margin-bottom: 30px;
        }
        
        .feature-card {
          background-color: rgba(255, 255, 255, 0.05);
          border-radius: 12px;
          padding: 20px;
          border: 1px solid rgba(59, 130, 246, 0.2);
          transition: all 0.3s;
          transform: translateY(0);
        }
        
        .feature-card:hover {
          background-color: rgba(255, 255, 255, 0.1);
          border-color: rgba(251, 146, 60, 0.5);
          box-shadow: 0 10px 15px -3px rgba(30, 64, 175, 0.2);
          transform: translateY(-4px);
        }
        
        .card-header {
          display: flex;
          align-items: center;
          margin-bottom: 16px;
        }
        
        .card-icon {
          background: linear-gradient(to bottom right, #60a5fa, #2563eb);
          padding: 12px;
          border-radius: 8px;
          margin-right: 12px;
          box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
          transition: all 0.3s;
        }
        
        .feature-card:hover .card-icon {
          background: linear-gradient(to bottom right, #fb923c, #ea580c);
          transform: rotate(3deg) scale(1.1);
        }
        
        .card-icon svg {
          width: 24px;
          height: 24px;
          stroke: white;
          stroke-width: 2;
          stroke-linecap: round;
          stroke-linejoin: round;
          fill: none;
        }
        
        .card-title {
          font-size: 16px;
          font-weight: 700;
          color: white;
          margin: 0;
          transition: color 0.3s;
        }
        
        .feature-card:hover .card-title {
          color: #fb923c;
        }
        
        .card-value {
          font-size: 24px;
          font-weight: 700;
          color: #fb923c;
          margin-bottom: 8px;
          transition: all 0.3s;
        }
        
        .feature-card:hover .card-value {
          color: #fdba74;
          font-size: 26px;
          transform: translateX(4px);
        }
        
        .card-description {
          font-size: 14px;
          color: #93c5fd;
          margin: 0;
          transition: color 0.3s;
        }
        
        .feature-card:hover .card-description {
          color: #dbeafe;
        }
        
        .cta-container {
          text-align: center;
        }
        
        .cta-button {
          background: linear-gradient(to right, #f97316, #ea580c);
          color: white;
          font-size: 18px;
          font-weight: 700;
          padding: 14px 32px;
          border-radius: 12px;
          border: none;
          display: inline-flex;
          align-items: center;
          cursor: pointer;
          box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
          transition: all 0.3s;
          position: relative;
          overflow: hidden;
          text-decoration: none;
        }
        
        .cta-button::before {
          content: "";
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: linear-gradient(to right, #ea580c, #f97316);
          opacity: 0;
          transition: opacity 0.3s;
          z-index: 0;
        }
        
        .cta-button::after {
          content: "";
          position: absolute;
          inset: -4px;
          background-color: #f97316;
          border-radius: 50%;
          filter: blur(10px);
          opacity: 0;
          transition: opacity 0.3s;
          z-index: -1;
        }
        
        .cta-button:hover {
          transform: translateY(-4px) scale(1.05);
          box-shadow: 0 20px 25px -5px rgba(249, 115, 22, 0.4);
          color: white;
          text-decoration: none;
        }
        
        .cta-button:hover::before {
          opacity: 1;
        }
        
        .cta-button:hover::after {
          opacity: 0.3;
          animation: pulse 2s infinite;
        }
        
        .cta-button span {
          position: relative;
          z-index: 1;
        }
        
        .cta-button svg {
          width: 24px;
          height: 24px;
          margin-left: 8px;
          position: relative;
          z-index: 1;
          transition: margin-left 0.3s;
          stroke: white;
          stroke-width: 2;
          stroke-linecap: round;
          stroke-linejoin: round;
          fill: none;
        }
        
        .cta-button:hover svg {
          margin-left: 12px;
        }
        
        @keyframes pulse {
          0% {
            opacity: 0.3;
          }
          50% {
            opacity: 0.5;
          }
          100% {
            opacity: 0.3;
          }
        }
        
        /* Reszponzív beállítások */
        @media (max-width: 1200px) {
          .feature-cards {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
          }
          
          .banner-title {
              font-size: 28px;
          }
          
          .banner-subtitle {
              font-size: 16px;
          }
        }
        
        @media (max-width: 767px) {
          .betting-banner {
            padding: 30px 20px;
          }
          
          .banner-header {
            flex-direction: column;
          }
          
          .banner-title {
            font-size: 24px;
          }
          
          .banner-subtitle {
            font-size: 14px;
          }
          
          .trust-badges {
            margin-top: 15px;
          }
          
          .feature-cards {
            grid-template-columns: 1fr;
          }
          
          .cta-button {
            width: 100%;
            justify-content: center;
            padding: 12px 20px;
            font-size: 16px;
          }
        }
        
        /* WordPress kompatibilitási fix */
        .betting-banner h1, 
        .betting-banner h2, 
        .betting-banner h3, 
        .betting-banner p, 
        .betting-banner a {
          text-shadow: none;
          letter-spacing: normal;
        }
        
        .betting-banner a:hover,
        .betting-banner a:focus {
          outline: none;
          box-shadow: none;
        }
        ';
    }
}

// Plugin inicializálása
$betting_banner_simple = new Betting_Banner_Simple();
