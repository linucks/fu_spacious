<?php
/**
 * farmurban functions
*/

/* For error logging */
if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}

function my_theme_enqueue_styles() {
    $parent_style = 'spacious_style';
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'farmurban_spacious_style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
    wp_enqueue_style( 'google-fonts-dosis', 'https://fonts.googleapis.com/css?family=Dosis' );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

/* Google Analytics */
function wpb_add_googleanalytics() {
?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-114920515-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-114920515-1');
</script>
<?php
}
add_action('wp_head', 'wpb_add_googleanalytics');

/* Display view cart url if not empty */
function get_fu_view_cart() {
    if ( WC()->cart->get_cart_contents_count() > 0 ) {
        return "<p>Your cart contains items and can be viewed <a href=\"" . home_url( '/cart/' ) . "\">here</a>.</p>";
    }
}
add_shortcode( 'fu_view_cart', 'get_fu_view_cart' );

/* Remove sidebar from woocommerce pages */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
