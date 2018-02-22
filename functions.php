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
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

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

function user_is_teacher() {
    $group_id = groups_get_id( 'teachers' );
    $group = groups_get_group( array( 'group_id' => $group_id ) );
    return bp_group_is_member( $group );
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

