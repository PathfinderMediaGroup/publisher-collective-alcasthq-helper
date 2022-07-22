<?php
/*
Plugin Name:  Network-N Advertisement Helper
Plugin URI:   https://www.network-n.com/
Description:  Network-N Ads scripts plugin for WordPress sites
Version:      20220302
Author:       NETWORK N
Author URI:   https://www.network-n.com/
Text Domain:  networkn
*/

class NetworkN_AdHelper
{
    private const MPU_CONTENT_SHORTCODE = 'nnmpu';
    private const DOMAIN = 'alcasthq.com';

    private string $domain;

    private array $configs = [
        self::DOMAIN => [
            'cmp' => [
                'template' => 'default.php',
                'logo' => 'https://' . self::DOMAIN . '/wp-content/uploads/2019/06/72xfavicon.png',
                'link' => 'https://' . self::DOMAIN . '/',
                'title' => 'Alcast HQ',
            ],
            'gtm_id' => 'GTM-T9VVBKT'
        ]
    ];

    private array $actions = [
        self::DOMAIN => [
            'wp_enqueue_scripts' => 'enqueue_custom_css',
            'wp_head' => 'insert_head_code',
            'wp_footer' => 'insert_rail_skins_and_bfa_containers',
            'avada_before_body_content' => 'insert_body_code',
            'avada_before_main_container' => 'insert_leaderboard_container',
        ]
    ];

    private array $filters = [
        self::DOMAIN => [
            // 'the_content' => 'inject_mpu_slots_into_post_content',
            // 'the_content' => 'inject_player_into_post_content',
            // 'attribute_escape' => 'alcast_append_celtra_sticky_header_class',
        ]
    ];

    private array $shortcodes = [
        self::DOMAIN => [
            // 'mpu_ad' => 'override_mpu_location'
        ]
    ];

    private bool $mpu_slot_at_bottom = false;

    public function __construct()
    {
        // We don't need to do anything in the admin area or post previews
        if (isset($_GET['preview']) || is_preview() || is_admin()) {
            return;
        }

        // Get the domain name
        $this->domain = self::getServerName();

        // Check if this is a local valet site
        if (stripos($this->domain, '.test') === strlen($this->domain) - 5) {
            $this->domain = substr($this->domain, 0, stripos($this->domain, '.test')) . '.com';
        }

        $this->add_actions();
        $this->add_filters();
        $this->add_shortcodes();
    }

    public function atlasEnabled(): bool
    {
        return $this->domain === self::DOMAIN;
    }

    public function add_actions(): void
    {
        // Add required scripts to all pages
        if (isset($this->actions[$this->domain])) {
            foreach ($this->actions[$this->domain] as $action_name => $function_name) {
                add_action($action_name, [$this, $function_name]);
            }
        }
    }

    public function add_filters(): void
    {
        // Add required scripts to all pages
        if (isset($this->filters[$this->domain])) {
            foreach ($this->filters[$this->domain] as $filter_name => $function_name) {
                add_filter($filter_name, [$this, $function_name], 20);
            }
        }
    }

    public function add_shortcodes(): void
    {
        // Add required scripts to all pages
        if (isset($this->shortcodes[$this->domain])) {
            foreach ($this->shortcodes[$this->domain] as $shortcode_name => $function_name) {
                add_shortcode($shortcode_name, [$this, $function_name], 20);
            }
        }
    }

    /**
     * Generic method for inserting header scripts
     */
    public function insert_head_code(): void
    {
        if ($this->userIsPatron(wp_get_current_user())) {
            return;
        }

        printf('<meta name="nn_scriptmode" content="%s">', $this->atlasEnabled() ? 'atlas' : 'sss');

        $this->insert_preconnect_code();
        if ($this->atlasEnabled()) {
            $this->insert_atlas_code();
        } else {
            $this->insert_cmp_head_code();
            $this->insert_sss_code();
        }
        $this->insert_placeholders();
    }

    /**
     * Queue custom CSS (not currently bundled with Atlas scripts)
     */
    public function enqueue_custom_css(): void
    {
        if ($this->atlasEnabled()) {
            $custom_css_path = sprintf('%scss/%s/custom.min.css', plugin_dir_url(__FILE__), $this->domain);
            wp_enqueue_style('nn-custom', $custom_css_path);

            wp_enqueue_script('alcast_main_js',
                plugins_url('/js/alcasthqmain.js', __FILE__),
                [], false, true);

            wp_localize_script("alcast_main_js", "autoplaceBodyAdsVars", [
                'isHomePage' => (is_home() || is_front_page())
            ]);
        }
    }

    /**
     * Insert Single Script Solution code
     */
    public function insert_sss_code(): void
    {
        include 'views/single-script-solution.php';
    }

    /**
     * Insert Atlas code
     */
    public function insert_atlas_code(): void
    {
        include 'views/atlas-script.php';
    }

    /**
     * Add Facebook Pixel script tag
     */
    public function insert_facebook_pixel_code(): void
    {
        include 'views/facebook-pixel.php';
    }

    /**
     * Insert body scripts
     */
    public function insert_body_code(): void
    {
        if (!$this->atlasEnabled()) {
            $this->insert_facebook_pixel_code();
        }
    }

    /**
     * Insert the Network-n CMP tool code
     */
    public function insert_cmp_head_code(): void
    {
        if (isset($this->configs[$this->domain]['cmp']['template'])) {
            include 'views/cmp/' . $this->configs[$this->domain]['cmp']['template'];
        }
    }

    public function insert_gtm_head_code(): void
    {
        include 'views/googletagmanager.php';
    }

    public function insert_placeholders(): void
    {
        include 'views/placeholders.php';
    }

    public function insert_preconnect_code(): void
    {
        include 'views/preconnect.php';
    }

    public function insert_gtm_body_code(): void
    {
        include 'views/googletagmanager-body.php';
    }


    /**
     * Add leaderboard container <div>
     */
    public function insert_leaderboard_container(): void
    {
        if (is_front_page() || is_search() || is_archive() || is_single() || is_page()) {
            include 'views/leaderboard.php';
        }
    }

    public function insert_rail_skins_and_bfa_containers(): void
    {
        $this->insert_rail_skins_container();
        // $this->insert_bfa_container();
    }

    /**
     * Add rail skin container <div>'s
     */
    public function insert_rail_skins_container(): void
    {
        if (is_front_page() || is_search() || is_archive() || is_single() || is_page()) {
            include 'views/railskins.php';
        }
    }

    public function insert_bfa_container(): void
    {
        if (is_front_page() || is_search() || is_archive() || is_single() || is_page()) {
            include 'views/bfa.php';
        }
    }

    public function override_mpu_location(array $atts): string
    {
        $atts = shortcode_atts(['id' => '', 'class' => 'nn-mpu--mobile'], $atts);
        return '<div id="' . $atts['id'] . '" class="' . $atts['class'] . '"></div>';
    }

    // Filter the_content to ensure that mpu ads are injected into a post
    public function inject_mpu_slots_into_post_content(string $content): string
    {
        if (is_singular('post')) {
            if (!str_contains($content, 'nn_mobile_mpu1')) {
                $content = $this->dom_insert_adslot_after($content, 'nn_mobile_mpu1', 'nn-mpu--mobile', 'h2[2]', 4);
            }

            if (!str_contains($content, 'nn_mobile_mpu2') && str_contains($content, 'nn_mobile_mpu1')) {
                $content = $this->dom_insert_adslot_after($content, 'nn_mobile_mpu2', 'nn-mpu--mobile', 'h2[4]', 10);
            }
            return $content;
        }

        return $content;
    }

    // Filter the_content and add an nn_player unit
    public function inject_player_into_post_content(string $content): string
    {
        if (is_singular('post')) {
            if (!str_contains($content, 'nn_player')) {
                $content = $this->dom_insert_adslot_after($content, 'nn_player', '', '', 3);
            }
            return $content;
        }

        return $content;
    }

    public function alcast_append_celtra_sticky_header_class(string $safe_text = '', string $text = ''): string
    {
        if ($safe_text === 'fusion-header-wrapper') {
            $safe_text = 'fusion-header-wrapper celtra-reveal-header-sticky';
        }
        return $safe_text;
    }

    public function dom_insert_adslot_after(string $content, $adslot_id, string $class = '', string $hPos = 'h2[2]', int $pPos = 4): string
    {
        libxml_use_internal_errors(true);
        $dom = new domDocument;
        $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new DomXPath($dom);

        if (!empty($hPos)) {
            $domPosition = $xpath->query('//' . $hPos);
            if ($domPosition->length < 1) {
                // No heading found... reverting to Nth paragraph
                $domPosition = $xpath->query('//p');
                if (!$this->mpu_slot_at_bottom && $domPosition->length <= $pPos) {
                    $domPosition = $domPosition[$domPosition->length - 1];
                    $this->mpu_slot_at_bottom = true;
                } else {
                    $domPosition = $domPosition[$pPos];
                }
            } else {
                $domPosition = $domPosition[0];
            }
        } else {
            $domPosition = $xpath->query('//p');

            // $pPos should not be higher than no. of paragraphs
            $pPos = ($pPos >= $domPosition->length) ? $domPosition->length - 1 : $pPos;

            $domPosition = $domPosition[$pPos];
        }
        $element = $dom->createElement('div');
        $element->setAttribute('id', $adslot_id);
        $element->setAttribute('class', $class);
        $element->setAttribute('style', 'text-align:center; display:block; width:100%; clear:both; padding:15px 0;');

        // $placeholder = $dom->createElement('img');
        // $placeholder->setAttribute('src', 'https://placehold.it/320x250/?text='.$adslot_id);
        // $element->appendChild( $placeholder );

        if ($domPosition->parentNode !== null && $domPosition->nextSibling !== null) {
            $domPosition->parentNode->insertBefore($element, $domPosition->nextSibling);

            // Get all the inner body elements
            $content = '';
            $body = $dom->getElementsByTagName('body')->item(0);
            foreach ($body->childNodes as $childNode) {
                $content .= $dom->saveHTML($childNode);
            }
        }
        return $content;
    }

    private function userIsPatron($user): bool
    {
        if ($user && class_exists('Patreon_Wordpress') && method_exists('Patreon_Wordpress', 'getPatreonUser')) {
            $patreonUser = Patreon_Wordpress::getPatreonUser($user);
            if (isset($patreonUser['included'])) {
                foreach ($patreonUser['included'] as $pledge) {
                    if (
                        isset($pledge['relationships']['currently_entitled_tiers']['data'])
                        && count($pledge['relationships']['currently_entitled_tiers']['data']) > 0
                    ) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private static function getServerName(): string
    {
        if (!empty(get_home_url())) {
            return rtrim(str_replace(['https://', 'http://', 'www.'], '', get_home_url()), '/');
        }
        if (!empty($_SERVER['SERVER_NAME'])) {
            return $_SERVER['SERVER_NAME'];
        }
        if (!empty($_SERVER['HTTP_HOST'])) {
            return $_SERVER['HTTP_HOST'];
        }

        return self::DOMAIN;
    }
}

// No action required on admin area
if (!is_admin()) {
    new NetworkN_AdHelper;
}
