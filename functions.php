<?php
/**
 * farmurban functions
*/

$FF_STAFF_PAGES = array( '/the-staff-room/', '/zoom-room/', '/team-leader-instructions/', '/resources/' );
$FF_STUDENT_PAGES = array( '/forums/forum/future-food-challenge-2018/', '/shop/', '/12-week-content/', '/future-food-challenge-2018/' );
$FF_PAGES = array_merge($FF_STAFF_PAGES, $FF_STUDENT_PAGES);
$FF_STAFF_MENUS = array( 'The Staff Room', 'Teachers Forum' );
$FF_STUDENT_MENUS = array( 'Future Food Challenge 2018 Forum', 'Future Food Challenge 2018' );
$FF_MENUS = array_merge($FF_STAFF_MENUS, $FF_STUDENT_MENUS);

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

function fu_login_redirect($redirect_to_calculated, $redirect_url_specified, $user) {
    $redirect_to = $redirect_to_calculated;
    if ( ! is_wp_error( $user ) ) {
        if ( in_array('administrator',  $user->roles) ) {
            $redirect_to = $redirect_to_calculated;
        } elseif ( user_is_teacher($user->ID) ) {
            $redirect_to = home_url( '/the-staff-room/' );
        } else {
            $redirect_to = home_url( '/future-food-challenge-2018/' );
        }
    }
    return $redirect_to;
}
add_filter('login_redirect','fu_login_redirect', 10, 3);

function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo home_url( '/wp-content/uploads/2016/06/FarmUrbanLogoNew100x106.png' ); ?>);
		height:106px;
		width:100px;
		background-size: 100px 106px;
		background-repeat: no-repeat;
        	padding-bottom: 30px;
        }
        #wp-submit {
            color:  #F4BA55;
            background-color: #0D96A5;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

function my_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return 'Farm Urban';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );


/* Buddypress stuff below */

function page_in_array($page_array){
    //$path = parse_url(wp_get_referer(), PHP_URL_PATH);
    $path = $_SERVER['REQUEST_URI'];
    return in_array( $path, $page_array );
}

function is_protected_page() {
    global $FF_PAGES;
    return ( ! is_user_logged_in() && ( bp_requires_login() || page_in_array( $FF_PAGES ) ) );
}

function bp_requires_login() {
    return ( ( ! bp_is_blog_page() && ! bp_is_activation_page() && ! bp_is_register_page() ) || is_bbpress() );
}

function page_only_for_teachers() {
    global $FF_STAFF_PAGES;
    return ( ! user_is_teacher() && page_in_array( $FF_STAFF_PAGES ) );
}

function user_is_teacher( $user_id = null ) {
    if ($user_id === null) {
        $user_id = bp_loggedin_user_id();
    }
    $group_id = groups_get_id( 'teachers' );
    return groups_is_user_member( $user_id, $group_id );
}

function my_page_template_redirect()
{
    if ( page_only_for_teachers() || is_protected_page() )
    {
        wp_redirect( home_url( '/wp-login.php' ) );
        exit();
    }
}

add_action( 'template_redirect', 'my_page_template_redirect' );

function filter_nav_menu_items($menu){
    // https://wordpress.stackexchange.com/questions/233667/how-to-hide-an-item-from-a-menu-to-logged-out-users-without-a-plugin
    global $FF_MENUS;
    global $FF_STAFF_MENUS;
    if ( in_array( $menu->title, $FF_STAFF_MENUS ) && ! user_is_teacher() ) {
        $menu->_invalid = True;
    } elseif ( in_array( $menu->title, $FF_MENUS ) && ! is_user_logged_in() ) {
        $menu->_invalid = True;
    }
    return $menu; //return the filtered object
}
add_filter( 'wp_setup_nav_menu_item', 'filter_nav_menu_items', 1 );

