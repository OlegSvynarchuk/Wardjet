<?php
/**
* Template Name: text only page
*/


//compile list of industries

// $args = array(  
//     'post_type' => 'industry',
//     'post_status' => 'publish',
//     'posts_per_page' => 100, 
//     'orderby' => 'title', 
//     'order' => 'ASC' 
// );

// $loop = new WP_Query( $args ); 


// $industry_map = array('Aerospace' => 263, 'Research &amp; Prototyping' => 338, 'Dye Making' => 339, 'Foam Converting'=>337,        'Plastic & Composite Fabrication'=>335, 'Plastic &amp; Composite Fabrication' => 335);
// while ( $loop->have_posts() ) : 
//     $loop->the_post(); 
//     $industry_map[$post->post_title] = $post->ID;
// endwhile;

// print_r($industry_map);

// $args = array(  
//     'post_type' => 'products',
//     'post_status' => 'publish',
//     'posts_per_page' => 100, 
//     'orderby' => 'title', 
//     'order' => 'ASC' 
// );

// $loop = new WP_Query( $args ); 

// while($loop->have_posts())
// {
//     $loop->the_post(); 
//     echo get_the_title().'<br>';
//     print_r(get_field('industries_list'));
//     $terms = get_the_terms($post->ID, 'industry');

//     $ind_list = array();
//     foreach($terms as $term):
//         $name = trim($term->name);
//         if (array_key_exists($name, $industry_map)):
//             $ind_list[] = $industry_map[$name];
//         else:
//             echo "didnt find term: X".$name."X\n<br>";
//         endif;
//     endforeach;


//     echo "final list is ".print_r($ind_list,1);

//     if (count($ind_list) >0 )
//         update_field('industries_list', $ind_list, $post->ID);

//     wp_reset_postdata();
// }


get_header(); ?>
<div id="text">
    <section>
        <div class="container">
            <h2 class="heading"><?=get_the_title()?></h2>

            <?php 
            the_content();
            ?>
        </div>
    </section>
</div>

<?php get_template_part('template-parts/agg-contact');?>
</div>
<?php
get_footer();