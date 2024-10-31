<?php

/*

Plugin Name: PlayerZBR

Description: PlayerZBR is the best HTML5 responsive player.

Version: 1.6

Author: Pedro Laxe

Author URI: http://www.phpsec.com.br/

License: GPLv2

*/

/*

 *      Copyright 2016 Pedro Laxe <pedro@phpsec.com.br>

 *

 *      This program is free software; you can redistribute it and/or modify

 *      it under the terms of the GNU General Public License as published by

 *      the Free Software Foundation; either version 3 of the License, or

 *      (at your option) any later version.

 *

 *      This program is distributed in the hope that it will be useful,

 *      but WITHOUT ANY WARRANTY; without even the implied warranty of

 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

 *      GNU General Public License for more details.

 *

 *      You should have received a copy of the GNU General Public License

 *      along with this program; if not, write to the Free Software

 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,

 *      MA 02110-1301, USA.

 */

add_action( 'init', 'Playerzbr_option' );

function Playerzbr_option(){

/**

 * Function Activate PlayerZBR

 *

 * @since 1.0

 *

 */

register_activation_hook( __FILE__, 'Playerzbr_install' );



/**

 * Function Compare Version of WP else Desactive Plugin

 *

 * @since 1.0

 *

 */

function Playerzbr_install() {

  if ( version_compare( PHP_VERSION, '5.2.1', '<' )

    or version_compare( get_bloginfo( 'version' ), '3.3', '<' ) ) {

      deactivate_plugins( basename( __FILE__ ) );

  }

  // Vamos criar um opção para ser guardada na base-de-dados

  // e incluir um valor por defeito.

  add_option( 'Playerzbr_option', 'Playerzbr_defeito' );

}



}

/**

 * Function to Create PlayerZBR

 *

 * @since 1.0

 *

 * Update 1.4 - Create multiples players on shortcode method
 * Update 1.5.1 - Added preload function to fix chrome bug

 */
 add_action( 'init', 'create_player_type' );

 /**
  * Esta é a função que é chamada pelo add_action()
  */
 function create_player_type() {

     /**
      * Labels customizados para o tipo de post
      *
      */
     $labels = array(
 	    'name' => _x('Playerzbr', 'Playerzbr'),
 	    'singular_name' => _x('Playerzbr', 'playerzbr'),
 	    'add_new' => _x('Add New', 'playerzbr'),
 	    'add_new_item' => __('Add New Player'),
 	    'edit_item' => __('Edit Player'),
 	    'new_item' => __('New Player'),
 	    'all_items' => __('All Players'),
 	    'view_item' => __('View Player'),
 	    'search_items' => __('Search Players'),
 	    'not_found' =>  __('No Players found'),
 	    'not_found_in_trash' => __('No Players found in Trash'),
 	    'parent_item_colon' => '',
 	    'menu_name' => 'PlayerZBR'
     );

     /**
      * Registamos o tipo de post film através desta função
      * passando-lhe os labels e parâmetros de controlo.
      */
     register_post_type( 'playerzbr', array(
 	    'labels' => $labels,
 	    'public' => true,
 	    'publicly_queryable' => true,
 	    'show_ui' => true,
 	    'show_in_menu' => true,
 	    'has_archive' => 'playerzbr',
 	    'rewrite' => array(
 		 'slug' => 'playerzbr',
 		 'with_front' => false,
 	    ),
 	    'capability_type' => 'post',
 	    'has_archive' => true,
 	    'hierarchical' => false,
 	    'menu_position' => null,
 	    'supports' => array('title')
 	    )
     );

}
//**

add_shortcode( 'playerzbr', 'display_playerzbr' );

   function display_playerzbr(){
       $args = array(
           'post_type'   => 'playerzbr',
           'post_status' => 'publish',
       );

       // The Query
       $query = new WP_Query( $args );

       // The Loop
       if ( $query->have_posts() ) {
       	while ( $query->have_posts() ) {
       		$query->the_post();
            $player = '
           <audio controls preload="auto">
            <source src="'.get_custom_field('urlmeta').'" type="audio/mpeg">
            <source src="'.get_custom_field('urlmeta').'" type="audio/ogg">
            Your browser does not support the audio element.
           </audio>
           ';
       	}
       }

       // Restore original Post Data
       wp_reset_postdata();
       return $player;
   }
   function get_custom_field($field_name){
     return get_post_meta(get_the_ID(),$field_name,true);
   }

//**

add_action( 'add_meta_boxes', 'playerzbr_add_meta_box' );

function playerzbr_add_meta_box() {
    add_meta_box(
        'playerzbr_metaid',
        'PlayerZBR Options',
        'playerzbr_inner_meta_box',
        'playerzbr',
        'normal',
        'default'
    );
}
function playerzbr_inner_meta_box( $post ) {
  $url_meta = get_post_meta( $post->ID, 'urlmeta', true );
?>
<p>
  <label for="urlmeta">URL:</label>
  <br />
  <input type="url" name="urlmeta" size="50" placeholder="YOUR FILE URL" value="<?php echo $url_meta; ?>" required>
</p>
<?php
}

add_action( 'save_post', 'playerzbr_save_post', 10, 2 );

function playerzbr_save_post( $post_id) {

   // Verificar se os dados foram enviados, neste caso se a metabox existe, garantindo-nos que estamos a guardar valores da página de filmes.
   if ( ! $_POST['urlmeta'] ) return;

   // Fazer a saneação dos inputs e guardá-los
   update_post_meta( $post_id, 'urlmeta', strip_tags( $_POST['urlmeta'] ) );

   return true;

}
/**
*
* Add Columns
*
* @since 1.6
*/

function playerzbr_columns_head($defaults){
    $defaults['shortcode'] = __('Shortcode');
    return $defaults;
}

//then you need to render the column
function playerzbr_columns_content($column_name, $post_id){
    if($column_name === 'shortcode'){
        echo '[playerzbr id="' . $post_id .'"]';
    }
}
add_filter('manage_posts_columns', 'playerzbr_columns_head');
add_action('manage_posts_custom_column', 'playerzbr_columns_content', 10, 2);

/**

 * Function add Page Options in WP Menu

 *

 * @since 1.0

 *

*/

/**
*
* Scripts on Footer
*
* @since 1.5
*/
function Activate_player() {
    echo '
<script type="text/javascript">
$( "audio" ).audioPlayer(
{
    classPrefix: "audioplayer",
    strPlay: "Play",
    strPause: "Pause",
    strVolume: "Volume"
});
</script>

	';
}
add_action('wp_footer', 'Activate_player');
/**

 * Function add Shortcode of PlayerZBR

 *

 * @since 1.0

 *

 */


/**
*
* Function Add Scripts
*
* @since 1.5
*/
function Playerzbr_scripts()
{
    // Deregister the included library
    wp_deregister_script( 'jquery' );

    // Register the library again from Google's CDN
    wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js', array(), null, false );

    // Register the script like this for a plugin:
    wp_register_script( 'playerzbr-script', plugins_url( '/js/playerzbr.js', __FILE__ ), array( 'jquery' ) );
    // or
    // Register the script like this for a theme:
    wp_register_script( 'playerzbr-scriptt', get_template_directory_uri() . '/js/playerzbr.js', array( 'jquery' ) );

	wp_register_style('playerzbr-script', plugins_url('css/playerzbr.css',__FILE__ ));
	wp_enqueue_style('playerzbr-script');
    // For either a plugin or a theme, you can then enqueue the script:
    wp_enqueue_script( 'playerzbr-script' );

}
add_action( 'wp_enqueue_scripts', 'Playerzbr_scripts' );
