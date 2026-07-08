<section class="content-area mainhero">
  <div class="site-main" role="main">
    <?php if( have_rows('hero_gallery') ): 
      $count=0;
      $count1=0;
    ?>
      <div id="slider">
       <div id="carousel" class="carousel slide" data-ride="carousel">
          <!-- Wrapper for slides -->
          <div class="carousel-inner">

            <?php while( have_rows('hero_gallery') ): 
              the_row(); 
              $image = get_sub_field('image');
              ?>

              <div class="carousel-item <?=($count1==0)?'active':''?>" style="background: linear-gradient(180deg, rgba(9,60,113,0) 0%, rgba(9,60,113,0) 40%, rgba(9,60,113,1) 100%), url(<?php echo $image['url']; ?>) no-repeat top center / cover">
                <div class="container-fluid herobg">
                  <div class="container content-over">
                    <div class="callout">
                      <div class="row justify-content-center">
			<?php if( get_sub_field('topcontent') ): ?>
                   			<div class="col-lg-9 col-10">
                          <p class="top-content linea_texto_3 color-white text-center  wow animate__fadeIn"><?php the_sub_field('topcontent'); ?></p>

                        </div>
                       <?php endif; ?>
                        <?php if( get_sub_field('heading') ): ?>
                        <div class="col-lg-9 col-10">
                          <h1 class="linea_texto_1 color-white text-center  wow animate__fadeIn"><?php the_sub_field('heading'); ?></h1>
                        </div>
                       <?php endif; ?>
                       <div class="col-lg-8 col-10 copydiv">
                         <?php if( get_sub_field('copy') ): ?>
                             <p class="linea_texto_2 text-center  wow animate__fadeIn"><?php the_sub_field('copy'); ?></p>
                           <?php endif; ?>
                         <?php 
                         $mainbanner_link = get_sub_field('cta');
                         if( $mainbanner_link ): 
                             $mainbanner_link_url = $mainbanner_link['url'];
                             $mainbanner_link_title = $mainbanner_link['title'];
                             $mainbanner_link_target = $mainbanner_link['target'] ? $mainbanner_link['target'] : '_self';
                             ?>
                             <p class="cta text-center"><a href="<?php echo esc_url( $mainbanner_link_url ); ?>" target="<?php echo esc_attr( $mainbanner_link_target ); ?>"><?php echo esc_html( $mainbanner_link_title ); ?></a></p>
                         <?php endif; ?>
                        </div>
                     </div>
                   </div>
                 </div>
               </div>
              </div><!-- item -->
                <?php $count1++; ?> 


              <?php endwhile; ?>
            </div><!-- carousel inner -->


        <!-- Indicators -->
        <ol class="carousel-indicators">
          <?php while( have_rows('hero_gallery') ): 
            the_row(); 
            ?>
            <li data-target="#carousel" data-slide-to="<?=$count; ?>" class="<?=($count==0)?'active':''?>">X</li>      
            <?php 
            $count++; endwhile; ?>
        </ol>
         

          </div><!-- #carousel -->
        </div><!--#slider-->

    <?php endif; ?>
  </div><!-- #main -->
</section><!-- #primary -->
