<?php

/**
 * Plugin Name: Columns for Bootstrap
 * Plugin URI: http://artsci.case.edu
 * Description: Use a [column] shortcode inside a [column-group] to create bootstrap magic.
 * Author: CWRU, CAS IT Group
 * Author URI: http://artsci.case.edu
 * License: GPLv2 or later
 * Version: 1.01
 */

class Columns_For_Bootstrap_Plugin {

    /**
     * Current Group Number
     * @var int 
     */
    public $current_group = 0;

    /**
     * Array containing total span width of each colgroup
     * @var array 
     */
    public $span = array();

    /**
     * Array containing width of spans indexed by group
     * $_spanwidths[0]=array(2,1,1);
     * @var array 
     */
    private $_spanwidths = array();

    /**
     * Array containing total number of columns in group
     * $_group_cols[0]=3
     * @var array 
     */
    private $_group_cols = array();

    function __construct() {
        if(is_network_admin() ) {
            register_activation_hook(__FILE__, array( $this, 'network_activation' ) );
            register_deactivation_hook(__FILE__, array( $this, 'network_deactivation' ) );
        } else {
            register_activation_hook(__FILE__, array( $this, 'activation' ) );
            register_deactivation_hook(__FILE__, array( $this, 'deactivation' ) );
        }

        add_action( 'init', array($this, 'init' ) );
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts' ) );
    }

    function init() {
        global $pagenow;
        if( ( get_option( 'bootstrap_columns_notice' ) == 'activated' || wp_get_theme()->get( 'Name' ) != get_option( 'bootstrap_columns_theme_name' ) || wp_get_theme()->get( 'Version' ) != get_option( 'bootstrap_columns_theme_version' ) ) && ( $pagenow == 'plugins.php' || $pagenow == 'themes.php' ) && (!is_network_admin() ) ) {
            $this->display_notice();
            update_option( 'bootstrap_columns_notice', 'shown', false );
            update_option( 'bootstrap_columns_theme_name', wp_get_theme()->name, false );
            update_option( 'bootstrap_columns_theme_version', wp_get_theme()->version, false );
        }      
        add_shortcode( 'column', array($this, 'column' ) );
        add_shortcode( 'column-group', array($this, 'group' ) );
    }
    
    function display_notice() {
        $found = false;
        $bootstrap = get_template_directory() . "/js/bootstrap.js";
        $class = "notice notice-warning is-dismissible";
	$message = "Bootstrap 3 was not found in the current theme. The Columns for Bootstrap plugin may not work correctly.";
        if( file_exists( $bootstrap ) ) {
            $file = file_get_contents( $bootstrap );
            if( strpos( $file, 'Bootstrap') !== false ) {
                if( strpos( $file, 'Bootstrap v3' ) !== false ) {
                    $found = true;
                } else {
                    $message = "A version of Bootstrap other than Bootstrap 3 was found. The Columns for Bootstrap plugin may not work correctly.";
                }
            }               
        }    
        if( !$found ) {
            echo"<div class=\"$class\"> <p>$message</p></div>";
        }
    }

    function column($attr, $content) {

        // get span value for this column, default to 1
        $attr = shortcode_atts(array('span' => 1,), $attr);

        $attr['span'] = absint($attr['span']);

        // Add this column to total span length
        $this->span[$this->current_group] += $attr['span'];

        // Add this columns to spanwidth array
        $this->_spanwidths[$this->current_group][] = $attr['span'];

        // Add column to total column count
        $this->_group_cols[$this->current_group] ++;

        $content = wpautop($content);

        // Allow other shortcodes inside the column content.
        if (false !== strpos($content, '[')) {
            $content = do_shortcode(shortcode_unautop($content));
        }

        // Get column index
        $i = $this->_group_cols[$this->current_group] - 1;
        return sprintf("<div class=\"col-xs-12 col-sm-12 col-md-##colwidth-span-%d## col-lg-##colwidth-span-%d##\">%s</div>", $i, $i, $content);
    }

    function group($attr, $content) {

        // Current Group
        $this->current_group++;

        // Current Span Count
        $this->span[$this->current_group] = 0;

        // Current Span Group Num Columns
        $this->_group_cols[$this->current_group] = 0;

        // Current Span Group Num Spans
        $this->_spanwidths[$this->current_group] = array();

        // Convent and count columns.
        $content = do_shortcode($content);

        $total_spans = $this->span[$this->current_group];

        $spanwidth = 12 / $total_spans;
        
        $i = 0;
        foreach ($this->_spanwidths[$this->current_group] as $column_span) {
            $needles = "##colwidth-span-$i##";
            $repls = round($this->_spanwidths[$this->current_group][$i] * $spanwidth);
            if ($repls % 1 != 0) {
                die($repls);
            }

            $content = str_replace($needles, $repls, $content);
            $i++;
        }

        return sprintf('<div class="row column-group-%d">%s</div>', $this->current_group, $content);
    }

    function enqueue_scripts() {
        wp_enqueue_style( 'columns', plugins_url('columns.css', __FILE__) );
    }

    function network_activation() {
        $blog_list = get_sites( );
        foreach ($blog_list AS $blog) {         
            add_blog_option($blog->blog_id, 'bootstrap_columns_notice', 'activated' );
            add_blog_option($blog->blog_id, 'bootstrap_columns_theme_name', wp_get_theme()->get( 'Name' ) );
            add_blog_option($blog->blog_id, 'bootstrap_columns_theme_version', wp_get_theme()->get( 'Version' ) );
        }
    }
    
    function network_deactivation() {
        $blog_list = get_sites( );
        foreach ($blog_list AS $blog) {         
            delete_blog_option($blog->blog_id, 'bootstrap_columns_notice', 'activated' );
            delete_blog_option($blog->blog_id, 'bootstrap_columns_theme_name', wp_get_theme()->get( 'Name' ) );
            delete_blog_option($blog->blog_id, 'bootstrap_columns_theme_version', wp_get_theme()->get( 'Version' ) );
        }
    }
        function activation() {       
            add_option( 'bootstrap_columns_notice', 'activated' );
            add_option( 'bootstrap_columns_theme_name', wp_get_theme()->get( 'Name' ) );
            add_option( 'bootstrap_columns_theme_version', wp_get_theme()->get( 'Version' ) );
    }
    
    function deactivation() {   
        delete_option( 'bootstrap_columns_notice', 'activated' );
        delete_option( 'bootstrap_columns_theme_name', wp_get_theme()->get( 'Name' ) );
        delete_option( 'bootstrap_columns_theme_version', wp_get_theme()->get( 'Version' ) );
    }
}
new Columns_For_Bootstrap_Plugin;