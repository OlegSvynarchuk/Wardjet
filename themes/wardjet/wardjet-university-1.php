<?php 
/**
Template Name: University 1
*/

get_header(); ?>

<div id="university-1">
    <?php get_template_part('template-parts/wardjet-secondary-hero', null, ['show_overlay'=>true]);?>
    <div class="about-kpis">
        <div class="container">
            <?php the_field('page_description');?>
        </div>
    </div>

    <section>
    	<div class="container">
    		<div class="row">
    			<div class="d-none d-sm-block col-sm-2">
    				<div class="mt-5">
    					<h4>Courses</h4>
	    				<?=get_field('menu')?>
	    			</div>
    			</div>
    			<div class="col-12 col-sm-10">
		    		<?php 
		    		while (have_rows('content_blocks')):
		    			the_row();
		    		?>
		    			<div class="row mt-5">
		    				<div class="col-12"><h3><?=get_sub_field('header')?></h3></div>
		    				<div class="col-12"><?=get_sub_field('description')?></div>
		    				<div class="col-12">
		    					<div class="row">
		    					<?php 
		    						while(have_rows('images')):
		    							the_row();
		    					?>
		    							<div class="col-4 univ-cb-img">
		    								<img src="<?=get_sub_field('image')['url']?>">
		    								<?php 
		    								if (get_sub_field('header')):
		    								?>
			    								<h4><?=get_sub_field('header')?></h4>
		    								<?php 
			    							endif;
			    							?>
		    								<?php 
		    								if (get_sub_field('description')):
		    								?>			    							
			    								<?=get_sub_field('description')?>
		    								<?php 
			    							endif;
			    							?>			    								
		    							</div>
		    					<?php 
		    						endwhile;
		    					?>
			    				</div>
		    				</div>
		    			</div>

		    		<?php 
		    		endwhile;
		    		?>
		    	</div>
		    </div>
    	</div>
    </section>

    <?php get_template_part('template-parts/agg-contact');?>

   <?php 
   get_footer();
   ?>
