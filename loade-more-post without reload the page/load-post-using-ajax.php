// add this code in index.php or wher you want to show the post without reload 
// <!-- auto loaded post section  -->
<section class="products-section py-2 ">
       <div class="container">

           <?php
           $argument = array(
               'post_type'      => 'post',
               'post_status'    => 'publish',
               'posts_per_page' => 4,
               'paged'          => 1,
           );
           // Query blog post arguments
           $blog_posts = new WP_Query($argument);

           if ($blog_posts->have_posts()) :
           ?>
               <div class="blog-posts row g-4">
                   <?php
                   while ($blog_posts->have_posts()) : $blog_posts->the_post();
                       $product_price = get_post_meta(get_the_ID(), '_product_price', true); // Optional
                   ?>
                       <div class="post_item_hover col-lg-3 col-md-3">
                           <div class="post-item">
                               <a href="<?php the_permalink(); ?>" class="post-thumbnail">
                                   <?php
                                   if (has_post_thumbnail()) {
                                       the_post_thumbnail('full', array(
                                           'class' => 'img-fluid',
                                           'alt'   => esc_attr(get_the_title()),
                                       ));
                                   }
                                   ?>
                               </a>
                               <div class="post_view_container d-flex justify-content-around    ">
                                   <a id="view_prodcut" href="#" class="icon_left p-2 text-dark"><i class="bi bi-eye"></i> </a>
                                   <a id="add_to_cart" href="#" class="icon_right p-2 text-dark"><i class="bi bi-cart-plus"></i> </a>
                               </div>

                               <div class="post-title_home_page bg-light text-dark p-2">
                       <a class="text-dark text-decoration-none product_title" href="<?php echo esc_url(get_permalink()); ?>">
                          
                       <p class="p-0 m-0" >
                          <?php
                           $title = get_the_title();
                           echo esc_html(mb_substr($title, 0, 40)) . (mb_strlen($title) > 40 ? '...' : '');
                           ?>
                           </p>
                           <span class="d-block product_price fs-6"><?php echo esc_html__('TK ' . $product_price, _T_DOMAIN); ?></span>
                       </a> 
                   </div>
                           </div>
                       </div>
                   <?php endwhile; ?>
                   <?php wp_reset_postdata(); ?>
               </div>
           <?php endif; ?>

       </div>
   </section>





   <div class="loadmore btn btn-primary text-center mx-auto"> Load more ...</div>









</div>


<?php 




// enqueue script 
function ecommarcetheam_ajax_script() {

	wp_register_script('load-more-script', get_stylesheet_directory_uri(). '/js/load-more.js', array('jquery'), '1.0', true);
	// localize script 
	wp_localize_script('load-more-script', 'ajax_object', array(
		'ajax_url' => admin_url('admin-ajax.php'),
		'security' => wp_create_nonce('load_more_posts'),
	));
	wp_enqueue_script('load-more-script');
}
add_action('wp_enqueue_scripts', 'ecommarcetheam_ajax_script');

add_action('wp_ajax_load_posts_by_ajax', 'load_post_by_ajax_callback');
add_action('wp_ajax_nopriv_load_posts_by_ajax', 'load_post_by_ajax_callback');

function load_post_by_ajax_callback() {
    // Verify the nonce
    check_ajax_referer('load_more_posts', 'security');

    // Get and sanitize the category slug and page number from the AJAX request
    $category_slug = isset($_POST['category_slug']) ? sanitize_text_field($_POST['category_slug']) : 'most-sold';
    $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;

    // Set up the query arguments
    $args = array(
        'category_name'  => $category_slug,
        'posts_per_page' => 4,
        'post_status'    => 'publish',
        'paged'          => $paged,
    );

    $blog_posts = new WP_Query($args);

    // Start output buffering
    ob_start();

    if ($blog_posts->have_posts()) {
        echo '<div class="row g-4" data-category="' . esc_attr($category_slug) . '">';
        while ($blog_posts->have_posts()): $blog_posts->the_post();
            $product_price = get_post_meta(get_the_ID(), '_product_price', true);
            ?>
            <div class="post_item_hover col-lg-3 col-md-3">
                <div class="post-item">
                    <a href="<?php echo esc_url(get_permalink()); ?>" class="post-thumbnail">
                        <?php
                        if (has_post_thumbnail()) {
                            the_post_thumbnail('full', array(
                                'class' => 'img-fluid',
                                'alt'   => esc_attr(get_the_title()),
                            ));
                        }
                        ?>
                    </a>
                    <div class="post_view_container d-flex justify-content-around">
                        <a id="view_prodcut" href="#" class="icon_left p-2 text-dark"><i class="bi bi-eye"></i></a>
                        <a id="add_to_cart" href="#" class="icon_right p-2 text-dark"><i class="bi bi-cart-plus"></i></a>
                    </div>
                    <div class="post-title_home_page bg-light text-dark p-2">
                        <a class="text-dark text-decoration-none product_title" href="<?php echo esc_url(get_permalink()); ?>">
                            <p class="p-0 m-0">
                                <?php
                                $title = get_the_title();
                                echo esc_html(mb_substr($title, 0, 40)) . (mb_strlen($title) > 40 ? '...' : '');
                                ?>
                            </p>
                            <span class="d-block product_price fs-6"><?php echo esc_html__('TK ' . $product_price, '_T_DOMAIN'); ?></span>
                        </a>
                    </div>
                </div>
            </div>
            <?php
        endwhile;
        echo '</div>';
    } else {
       
    }

    // Capture the buffer contents
    $response = ob_get_clean();

    // Reset post data
    wp_reset_postdata();

    // Return the response and exit
    echo $response;
    wp_die();
}?>