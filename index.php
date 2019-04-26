<?php
 
/*

 Plugin Name: VW Travel Pro Posttype
 lugin URI: https://www.vwthemes.com/
 Description: Creating new post type for VW Travel Pro Theme.
 Author: VW Themes
 Version: 1.0
 Author URI: https://www.vwthemes.com/

*/

define( 'VW_TRAVEL_PRO_POSTTYPE_VERSION', '1.0' );
add_action( 'init', 'tourscategory');
add_action( 'init', 'vw_travel_pro_posttype_create_post_type' );

function vw_travel_pro_posttype_create_post_type() {

  register_post_type( 'destinations',
    array(
      'labels' => array(
          'name' => __( 'Destinations','vw-travel-pro-posttype' ),
          'singular_name' => __( 'Destinations','vw-travel-pro-posttype' )
      ),
        'capability_type' =>  'post',
        'menu_icon'  => 'dashicons-welcome-learn-more',
        'public' => true,
        'supports' => array(
        'title',
        'editor',
        'thumbnail',
        'page-attributes',
        'comments'
      )
    )
  );
  
  register_post_type( 'tours',
    array(
        'labels' => array(
            'name' => __( 'Tours','vw-travel-pro-posttype' ),
            'singular_name' => __( 'Tours','vw-travel-pro-posttype' )
        ),
        'capability_type' =>  'post',
        'menu_icon'  => 'dashicons-welcome-learn-more',
        'public' => true,
        'supports' => array(
        'title',
        'editor',
        'thumbnail',
        'page-attributes',
        'comments'
        )
    )
  );

  register_post_type( 'hotels',
    array(
        'labels' => array(
            'name' => __( 'Hotels','vw-travel-pro-posttype' ),
            'singular_name' => __( 'Hotels','vw-travel-pro-posttype' )
        ),
        'capability_type' =>  'post',
        'menu_icon'  => 'dashicons-welcome-learn-more',
        'public' => true,
        'supports' => array(
        'title',
        'editor',
        'thumbnail',
        'page-attributes',
        'comments'
        )
    )
  );
  
  register_post_type( 'testimonials',
    array(
      'labels' => array(
        'name' => __( 'Testimonial','vw-travel-pro-posttype' ),
        'singular_name' => __( 'Testimonial','vw-travel-pro-posttype' )
      ),
      'capability_type' => 'post',
      'menu_icon'  => 'dashicons-businessman',
      'public' => true,
      'supports' => array(
        'title',
        'editor',
        'thumbnail'
      )
    )
  );
}

// ---------------- Destinations ---------------

function vw_travel_pro_posttype_bn_custom_meta_destination() {

  add_meta_box( 'bn_meta', __( 'Destinations Meta', 'vw-travel-pro-posttype-pro' ), 'vw_travel_pro_posttype_bn_meta_callback_destination', 'destinations', 'normal', 'high' );
}
/* Hook things in for admin*/
if (is_admin()){
  add_action('admin_menu', 'vw_travel_pro_posttype_bn_custom_meta_destination');
}

function vw_travel_pro_posttype_bn_meta_callback_destination( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );
    $dest_type = get_post_meta( $post->ID, 'meta-dest-type', true );
    $dest_city = get_post_meta( $post->ID, 'meta-dest-city', true );
    
    ?>
  <div id="property_stuff">
    <table id="list-table">     
      <tbody id="the-list" data-wp-lists="list:meta">
        <tr id="meta-1">
          <td class="left">
            <?php _e( 'Destinations Type', 'vw-travel-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="meta-dest-type" id="meta-dest-type" value="<?php echo esc_html($dest_type); ?>" />
          </td>
        </tr>
        <tr id="meta-2">
          <td class="left">
            <?php _e( 'City Name', 'vw-travel-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="meta-dest-city" id="meta-dest-city" value="<?php echo esc_html($dest_city); ?>" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <?php
}

function vw_travel_pro_posttype_bn_meta_save_dest( $post_id ) {

  if (!isset($_POST['bn_nonce']) || !wp_verify_nonce($_POST['bn_nonce'], basename(__FILE__))) {
    return;
  }

  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  if( isset( $_POST[ 'meta-dest-type' ] ) ) {
    update_post_meta( $post_id, 'meta-dest-type', sanitize_text_field($_POST[ 'meta-event-date' ]) );
  } 
  if( isset( $_POST[ 'meta-dest-city' ] ) ) {
    update_post_meta( $post_id, 'meta-dest-city', sanitize_text_field($_POST[ 'meta-dest-city' ]) );
  }
  
}
add_action( 'save_post', 'vw_travel_pro_posttype_bn_meta_save_dest' );

/* Event shortcode */
function vw_travel_pro_posttype_dest_func( $atts ) {
  $thumb_url="";
  $projects = '';
  $projects = '<div class="row top-destination" id="top-destination">';
  $query = new WP_Query( array( 'post_type' => 'destinations') );

    if ( $query->have_posts() ) :

  $k=1;
  $new = new WP_Query(array('post_type' => 'destinations','order' => 'ASC'));
  while ($new->have_posts()) : $new->the_post();

        $dest_col='';
        if($k==1)
        {
          $dest_col='col-lg-8 col-md-8';
        }else{
          $dest_col='col-lg-4 col-md-4';
        }

        $post_id = get_the_ID();
        $dest_type= get_post_meta($post_id,'meta-dest-type',true);
        $dest_city= get_post_meta($post_id,'meta-dest-city',true);
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
        if(has_post_thumbnail()) { $thumb_url = $thumb['0']; }
        $url = $thumb['0'];
        $custom_url ='';
        $excerpt = wp_trim_words(get_the_excerpt(),12);
        $custom_url = get_permalink();
        $projects .= '
            <div class="'.$dest_col.'">
              <div class="top-destination-box">
                <span class="destinations-type">
                  '.$dest_type.'
                </span>
                <img class="services-img" src="'.esc_url($thumb_url).'" />
                <div class="destinations-title">
                  <h5><a href="'.esc_url($custom_url).'">'.esc_html(get_the_title()) .'</a></h5>
                  <span class="destinations-city">
                    '.$dest_city.'
                  </span>
                </div>
              </div>
            </div>';
    if($k%2 == 0){
      $projects.= '<div class="clearfix"></div>';
    }
      $k++;
  endwhile;
  else :
    $projects = '<h2 class="center">'.esc_html__('Post Not Found','vw_travel_pro_posttype').'</h2>';
  endif;
  return $projects;
}

add_shortcode( 'vw-travel-pro-destinations', 'vw_travel_pro_posttype_dest_func' );

// ------------------ Tours --------------------

function tourscategory() {
  // Add new taxonomy, make it hierarchical (like categories)
  $labels = array(
    'name'              => __( 'Categories', 'vw-travel-pro-posttype' ),
    'singular_name'     => __( 'Categories', 'vw-travel-pro-posttype' ),
    'search_items'      => __( 'Search cats', 'vw-travel-pro-posttype' ),
    'all_items'         => __( 'All Categories', 'vw-travel-pro-posttype' ),
    'parent_item'       => __( 'Parent Categories', 'vw-travel-pro-posttype' ),
    'parent_item_colon' => __( 'Parent Categories:', 'vw-travel-pro-posttype' ),
    'edit_item'         => __( 'Edit Categories', 'vw-travel-pro-posttype' ),
    'update_item'       => __( 'Update Categories', 'vw-travel-pro-posttype' ),
    'add_new_item'      => __( 'Add New Categories', 'vw-travel-pro-posttype' ),
    'new_item_name'     => __( 'New Categories Name', 'vw-travel-pro-posttype' ),
    'menu_name'         => __( 'Categories', 'vw-travel-pro-posttype' ),
  );
  $args = array(
    'hierarchical'      => true,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'query_var'         => true,
    'rewrite'           => array( 'slug' => 'tourscategory' ),
  );
  register_taxonomy( 'tourscategory', array( 'tours' ), $args );
}


//  --------------- Tour  Meta ---------------

function vw_travel_pro_posttype_bn_custom_meta_tours() {

    add_meta_box( 'bn_meta', __( 'Tours Meta', 'vw-travel-pro-posttype-pro' ), 'vw_travel_pro_posttype_bn_meta_callback_tours', 'tours', 'normal', 'high' );
}
/* Hook things in for admin*/
if (is_admin()){
  add_action('admin_menu', 'vw_travel_pro_posttype_bn_custom_meta_tours');
}

function vw_travel_pro_posttype_bn_meta_callback_tours( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );
    $tour_price = get_post_meta( $post->ID, 'meta-tour-price', true );
    $tour_location = get_post_meta( $post->ID, 'meta-tour-location', true );
    $tour_days = get_post_meta( $post->ID, 'meta-tour-days', true );
    $tour_peaples = get_post_meta( $post->ID, 'meta-tour-peoples', true );
    ?>
  <div id="property_stuff">
    <table id="list-table">     
      <tbody id="the-list" data-wp-lists="list:meta">
        <tr id="meta-1">
          <p><b><?php _e( 'Do not add currency symbol with price here add it in customizer and add only numeric value in price textbox example (50,80,100 etc.)', 'vw-travel-pro-posttype' )?></b></p>
          <td class="left">
            <?php _e( 'Tour Price', 'vw-travel-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="meta-tour-price" id="meta-tour-price" value="<?php echo esc_html($tour_price); ?>" />
          </td>
        </tr>
        <tr id="meta-2">
          <td class="left">
            <?php _e( 'Tour Location', 'vw-travel-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="meta-tour-location" id="meta-tour-location" value="<?php echo esc_html($tour_location); ?>" />
          </td>
        </tr>
        <tr id="meta-3">
          <td class="left">
            <?php _e( 'Tour Days', 'vw-travel-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="meta-tour-days" id="meta-tour-days" value="<?php echo esc_html($tour_days); ?>" />
          </td>
        </tr>
        <tr id="meta-4">
          <td class="left">
            <?php _e( 'No Of Peoples', 'vw-travel-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="meta-tour-peoples" id="meta-tour-peoples" value="<?php echo esc_html($tour_peaples); ?>" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <?php
}

function vw_travel_pro_posttype_bn_meta_save_tours( $post_id ) {

  if (!isset($_POST['bn_nonce']) || !wp_verify_nonce($_POST['bn_nonce'], basename(__FILE__))) {
    return;
  }

  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }
  if( isset( $_POST[ 'meta-tour-price' ] ) ) {
    update_post_meta( $post_id, 'meta-tour-price', sanitize_text_field($_POST[ 'meta-tour-price' ]) );
  } 

  if( isset( $_POST[ 'meta-tour-location' ] ) ) {
    update_post_meta( $post_id, 'meta-tour-location', sanitize_text_field($_POST[ 'meta-tour-location' ]) );
  } 
  if( isset( $_POST[ 'meta-tour-days' ] ) ) {
    update_post_meta( $post_id, 'meta-tour-days', sanitize_text_field($_POST[ 'meta-tour-days' ]) );
  }
  if( isset( $_POST[ 'meta-tour-peoples' ] ) ) {
    update_post_meta( $post_id, 'meta-tour-peoples', sanitize_text_field($_POST[ 'meta-tour-peoples' ]) );
  }
}
add_action( 'save_post', 'vw_travel_pro_posttype_bn_meta_save_tours' );

/* tour shortcode */
function vw_travel_pro_posttype_tours_func( $atts ) {
  $projects = '';
  $projects = '<div class="row" id="tour-interest">';
  $query = new WP_Query( array( 'post_type' => 'tours') );

    if ( $query->have_posts() ) :

  $k=1;
  $new = new WP_Query('post_type=tours');
  while ($new->have_posts()) : $new->the_post();

        $post_id = get_the_ID();
        $tour_prices= get_post_meta($post_id,'meta-tour-price',true);
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
        if(has_post_thumbnail()) { $thumb_url = $thumb['0']; }
        $url = $thumb['0'];
        $custom_url ='';
        $excerpt = wp_trim_words(get_the_excerpt(),10);
        $custom_url = get_permalink();
        $tourcurrency=get_theme_mod('vw_travel_pro_tour_by_interest_currency');
        $projects .= '
            <div class="col-lg-3 col-md-4 col-sm-6 tour-interest-content">
              <div class="tour-interest-image">
                <img src="'.esc_url($thumb_url).'" />
                <h5><a href="'.esc_url($custom_url).'">'.esc_html(get_the_title()) .'</a></h5>
                <p>
                  '.$tourcurrency.'
                  '.$tour_prices.'
                </p>
              </div>
            </div>';
    if($k%2 == 0){
      $projects.= '<div class="clearfix"></div>';
    }
      $k++;
  endwhile;
  else :
    $projects = '<h2 class="center">'.esc_html__('Post Not Found','vw_travel_pro_posttype').'</h2>';
  endif;
  return $projects;
}

add_shortcode( 'vw-travel-pro-tours', 'vw_travel_pro_posttype_tours_func' );

// ---------------- Hotels ---------------------

function vw_travel_pro_posttype_bn_custom_meta_hotels() {

    add_meta_box( 'bn_meta', __( 'Hotels Meta', 'vw-travel-pro-posttype-pro' ), 'vw_travel_pro_posttype_bn_meta_callback_hotels', 'hotels', 'normal', 'high' );
}
/* Hook things in for admin*/
if (is_admin()){
  add_action('admin_menu', 'vw_travel_pro_posttype_bn_custom_meta_hotels');
}

function vw_travel_pro_posttype_bn_meta_callback_hotels( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );

    $hotel_rent = get_post_meta( $post->ID, 'meta-hotel-rent', true );
    $hotel_address = get_post_meta( $post->ID, 'meta-hotel-address', true );
    $hotel_people = get_post_meta( $post->ID, 'meta-hotel-people', true );
    $hotel_lat = get_post_meta( $post->ID, 'meta-hotel-lat', true );
    $hotel_long = get_post_meta( $post->ID, 'meta-hotel-long', true );
    ?>
  <div id="property_stuff hotels_meta_data">
    <table id="list-table">     
      <tbody id="the-list" data-wp-lists="list:meta">
        <p><b><?php _e( 'Do not add currency symbol with price here add it in customizer and add only numeric value in price textbox example (50,80,100 etc.)', 'vw-travel-pro-posttype' )?></b></p>
        <tr id="meta-1">
          <td class="left">
            <?php _e( 'Hotel Rent', 'vw-travel-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="meta-hotel-rent" id="meta-hotel-rent" value="<?php echo esc_html($hotel_rent); ?>" />
          </td>
        </tr>
        <tr id="meta-2">
          <td class="left">
            <?php _e( 'Hotel Address', 'vw-travel-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="meta-hotel-address" id="meta-hotel-address" value="<?php echo esc_html($hotel_address); ?>" />
          </td>
        </tr>
        <tr id="meta-3">
          <td class="left">
            <?php _e( 'No Of People', 'vw-travel-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="meta-hotel-people" id="meta-hotel-people" value="<?php echo esc_html($hotel_people); ?>" />
          </td>
        </tr>
        <tr>
          <td>
            <p class="meta-title"><b><?php _e( 'If you add latitude and longitude option of hotel location then View Map option will appear only in hotel search page', 'vw-travel-pro-posttype' )?></b></p>
          </td>
        </tr>
        <tr id="meta-4">
          <td class="left">
            <?php _e( 'Map Latitude', 'vw-travel-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="meta-hotel-lat" id="meta-hotel-lat" value="<?php echo esc_html($hotel_lat); ?>" />
          </td>
        </tr>
        <tr id="meta-4">
          <td class="left">
            <?php _e( 'Map Longitude', 'vw-travel-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="meta-hotel-long" id="meta-hotel-long" value="<?php echo esc_html($hotel_long); ?>" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <?php
}

function vw_travel_pro_posttype_bn_meta_save_hotel( $post_id ) {

  if (!isset($_POST['bn_nonce']) || !wp_verify_nonce($_POST['bn_nonce'], basename(__FILE__))) {
    return;
  }

  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  if( isset( $_POST[ 'meta-hotel-rent' ] ) ) {
    update_post_meta( $post_id, 'meta-hotel-rent', sanitize_text_field($_POST[ 'meta-hotel-rent' ]) );
  } 
  if( isset( $_POST[ 'meta-hotel-address' ] ) ) {
    update_post_meta( $post_id, 'meta-hotel-address', sanitize_text_field($_POST[ 'meta-hotel-address' ]) );
  }

  if( isset( $_POST[ 'meta-hotel-people' ] ) ) {
    update_post_meta( $post_id, 'meta-hotel-people', sanitize_text_field($_POST[ 'meta-hotel-people' ]) );
  }
  if( isset( $_POST[ 'meta-hotel-lat' ] ) ) {
    update_post_meta( $post_id, 'meta-hotel-lat', sanitize_text_field($_POST[ 'meta-hotel-lat' ]) );
  }
  
  if( isset( $_POST[ 'meta-hotel-long' ] ) ) {
    update_post_meta( $post_id, 'meta-hotel-long', sanitize_text_field($_POST[ 'meta-hotel-long' ]) );
  }
}
add_action( 'save_post', 'vw_travel_pro_posttype_bn_meta_save_hotel' );

/* Event shortcode */
function vw_travel_pro_posttype_hotel_func( $atts ) {
  $thumb_url="";
  $projects = '';
  $projects = '<div class="row" id="hotels">';
  $query = new WP_Query( array( 'post_type' => 'hotels') );

    if ( $query->have_posts() ) :

  $k=1;
  $new = new WP_Query('post_type=hotels');
  while ($new->have_posts()) : $new->the_post();


        $post_id = get_the_ID();
        $hotel_rent= get_post_meta($post_id,'meta-hotel-rent',true);
        $hotel_address= get_post_meta($post_id,'meta-hotel-address',true);
        $hotel_people= get_post_meta($post_id,'meta-hotel-people',true);
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
        if(has_post_thumbnail()) { $thumb_url = $thumb['0']; }
        $url = $thumb['0'];
        $custom_url ='';
        $excerpt = wp_trim_words(get_the_excerpt(),12);
        $custom_url = get_permalink();
        $hotelicon1=get_theme_mod('vw_travel_pro_hotels_address_icon');
        $hotelicon2=get_theme_mod('vw_travel_pro_hotels_people_icon');
        $hotel_currency=get_theme_mod('vw_travel_pro_hotels_currency');
        $projects .= '
            <div class="col-lg-6 col-md-6">
              <div class="hotels-box">
                <span class="hotel-rent">
                  '.$hotel_currency.'
                  '.$hotel_rent.'
                </span>
                <img src="'.esc_url($thumb_url).'" />
                <div class="hotels-title">
                  <h5><a href="'.esc_url($custom_url).'">'.esc_html(get_the_title()) .'</a></h5>
                  <span class="hotel-address">
                    <i class="'.$hotelicon1.'"></i>
                    '.$hotel_address.'
                  </span>
                  <span class="hotel-people">
                    <i class="'.$hotelicon2.'"></i>
                    '.$hotel_people.'
                  </span>
                </div>
              </div>
            </div>';
    if($k%2 == 0){
      $projects.= '<div class="clearfix"></div>';
    }
      $k++;
  endwhile;
  else :
    $projects = '<h2 class="center">'.esc_html__('Post Not Found','vw_travel_pro_posttype').'</h2>';
  endif;
  return $projects;
}

add_shortcode( 'vw-travel-pro-hotels', 'vw_travel_pro_posttype_hotel_func' );

/*---------------------------------- Testimonial section -------------------------------------*/

/* Adds a meta box to the Testimonial editing screen */
function vw_travel_pro_posttype_bn_testimonial_meta_box() {
  add_meta_box( 'vw-travel-pro-posttype-testimonial-meta', __( 'Enter Details', 'vw-travel-pro-posttype' ), 'vw_travel_pro_posttype_bn_testimonial_meta_callback', 'testimonials', 'normal', 'high' );
}
// Hook things in for admin
if (is_admin()){
    add_action('admin_menu', 'vw_travel_pro_posttype_bn_testimonial_meta_box');
}

/* Adds a meta box for custom post */
function vw_travel_pro_posttype_bn_testimonial_meta_callback( $post ) {
  wp_nonce_field( basename( __FILE__ ), 'vw_travel_pro_posttype_posttype_testimonial_meta_nonce' );
  $bn_stored_meta = get_post_meta( $post->ID );
  $desigstory = get_post_meta( $post->ID, 'vw_travel_pro_posttype_testimonial_desigstory', true );
  $test_facebook = get_post_meta( $post->ID, 'meta-tes-facebookurl', true );
  $test_linkedin = get_post_meta( $post->ID, 'meta-tes-linkdenurl', true );
  $test_twitter = get_post_meta( $post->ID, 'meta-tes-twitterurl', true );
  $test_gplus = get_post_meta( $post->ID, 'meta-tes-googleplusurl', true );
  $test_instagram = get_post_meta( $post->ID, 'meta-tes-instagram', true );
  $test_pinterest = get_post_meta( $post->ID, 'meta-tes-pinterest', true );
  ?>
  <div id="testimonials_custom_stuff">
    <table id="list">
      <tbody id="the-list" data-wp-lists="list:meta">
        <tr id="meta-1">
          <td class="left">
            <?php _e( 'Designation', 'vw-travel-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="vw_travel_pro_posttype_testimonial_desigstory" id="vw_travel_pro_posttype_testimonial_desigstory" value="<?php echo esc_attr( $desigstory ); ?>" />
          </td>
        </tr>
        <tr id="meta-2">
          <td class="left">
            <?php _e( 'Facebook Url', 'vw-travel-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="url" name="meta-tes-facebookurl" id="meta-tes-facebookurl" value="<?php echo esc_html($test_facebook); ?>" />
          </td>
        </tr>
        <tr id="meta-3">
          <td class="left">
            <?php _e( 'Linkedin Url', 'vw-travel-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="url" name="meta-tes-linkdenurl" id="meta-tes-linkdenurl" value="<?php echo esc_html($test_linkedin); ?>" />
          </td>
        </tr>
        <tr id="meta-4">
          <td class="left">
            <?php _e( 'Twitter Url', 'vw-travel-pro-posttype' ); ?>
          </td>
          <td class="left" >
            <input type="url" name="meta-tes-twitterurl" id="meta-tes-twitterurl" value="<?php echo esc_html($test_twitter); ?>" />
          </td>
        </tr>
        <tr id="meta-5">
          <td class="left">
            <?php _e( 'GooglePlus Url', 'vw-travel-pro-posttype' ); ?>
          </td>
          <td class="left" >
            <input type="url" name="meta-tes-googleplusurl" id="meta-tes-googleplusurl" value="<?php echo esc_html($test_gplus); ?>" />
          </td>
        </tr>
        <tr id="meta-6">
          <td class="left">
            <?php _e( 'Instagram Url', 'vw-travel-pro-posttype' ); ?>
          </td>
          <td class="left" >
            <input type="url" name="meta-tes-instagram" id="meta-tes-instagram" value="<?php echo esc_html($test_instagram); ?>" />
          </td>
        </tr>
        <tr id="meta-7">
          <td class="left">
            <?php _e( 'Pinterest Url', 'vw-travel-pro-posttype' ); ?>
          </td>
          <td class="left" >
            <input type="url" name="meta-tes-pinterest" id="meta-tes-pinterest" value="<?php echo esc_html($test_pinterest); ?>" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <?php
}

/* Saves the custom meta input */
function vw_travel_pro_posttype_bn_metadesig_save( $post_id ) {
  if (!isset($_POST['vw_travel_pro_posttype_posttype_testimonial_meta_nonce']) || !wp_verify_nonce($_POST['vw_travel_pro_posttype_posttype_testimonial_meta_nonce'], basename(__FILE__))) {
    return;
  }

  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  // Save desig.
  if( isset( $_POST[ 'vw_travel_pro_posttype_testimonial_desigstory' ] ) ) {
    update_post_meta( $post_id, 'vw_travel_pro_posttype_testimonial_desigstory', sanitize_text_field($_POST[ 'vw_travel_pro_posttype_testimonial_desigstory']) );
  }
  // Save facebookurl
  if( isset( $_POST[ 'meta-tes-facebookurl' ] ) ) {
      update_post_meta( $post_id, 'meta-tes-facebookurl', esc_url($_POST[ 'meta-tes-facebookurl' ]) );
  }
  // Save linkdenurl
  if( isset( $_POST[ 'meta-tes-linkdenurl' ] ) ) {
      update_post_meta( $post_id, 'meta-tes-linkdenurl', esc_url($_POST[ 'meta-tes-linkdenurl' ]) );
  }
  if( isset( $_POST[ 'meta-tes-twitterurl' ] ) ) {
      update_post_meta( $post_id, 'meta-tes-twitterurl', esc_url($_POST[ 'meta-tes-twitterurl' ]) );
  }
  // Save googleplusurl
  if( isset( $_POST[ 'meta-tes-googleplusurl' ] ) ) {
      update_post_meta( $post_id, 'meta-tes-googleplusurl', esc_url($_POST[ 'meta-tes-googleplusurl' ]) );
  }

  // Save Instagram
  if( isset( $_POST[ 'meta-tes-instagram' ] ) ) {
      update_post_meta( $post_id, 'meta-tes-instagram', esc_url($_POST[ 'meta-tes-instagram' ]) );
  }
  // Save Pinterest
  if( isset( $_POST[ 'meta-tes-pinterest' ] ) ) {
      update_post_meta( $post_id, 'meta-tes-pinterest', esc_url($_POST[ 'meta-tes-pinterest' ]) );
  }

}

add_action( 'save_post', 'vw_travel_pro_posttype_bn_metadesig_save' );

/*---------------------------------- testimonials shortcode --------------------------------------*/
function vw_travel_pro_posttype_testimonial_func( $atts ) {
  $testimonial = '';
  $testimonial = '<div class="row all-testimonial">';
  $query = new WP_Query( array( 'post_type' => 'testimonials') );

    if ( $query->have_posts() ) :

  $k=1;
  $new = new WP_Query('post_type=testimonials');
  while ($new->have_posts()) : $new->the_post();

        $post_id = get_the_ID();
         $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
        if(has_post_thumbnail()) { $thumb_url = $thumb['0']; }
        $url = $thumb['0'];
        $custom_url ='';
        
        $excerpt = wp_trim_words(get_the_excerpt(),15);
        $tdegignation= get_post_meta($post_id,'vw_travel_pro_posttype_testimonial_desigstory',true);
        if(get_post_meta($post_id,'meta-testimonial-url',true !='')){$custom_url =get_post_meta($post_id,'meta-testimonial-url',true); } else{ $custom_url = get_permalink(); }
        $testimonial .= '

            <div class="our_testimonial_outer col-lg-4 col-md-6 col-sm-6">
              <div class="testimonial_inner">
                <div class="row hover_border">
                  <div class="col-md-12">
                     <img class="classes-img" src="'.esc_url($thumb_url).'" alt="attorney-thumbnail" />
                    <h5><a href="'.esc_url($custom_url).'">'.esc_html(get_the_title()) .'</a></h5>
                    <div class="tdesig">'.$tdegignation.'</div>
                    <div class="short_text">'.$excerpt.'</div>
                  </div>
                </div>
              </div>
            </div>';
    if($k%2 == 0){
      $testimonial.= '<div class="clearfix"></div>';
    }
      $k++;
  endwhile;
  else :
    $testimonial = '<h2 class="center">'.esc_html__('Post Not Found','vw_travel_pro_posttype').'</h2>';
  endif;
  return $testimonial;
}

add_shortcode( 'vw-travel-pro-testimonials', 'vw_travel_pro_posttype_testimonial_func' );

