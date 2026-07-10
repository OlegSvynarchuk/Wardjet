<?php $block = $args['block']; ?>
<section class="images-gallery">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <?php
                $sections = $block['sections'];
                if ($sections): ?>
                <div class="sections-list">
                    <ul>
                        <li class="active"><a href="#" class="all">All</a></li>
                        <?php foreach($sections AS $section): ?>
                        <li><a href="#" class="<?php echo str_replace(array('.',' '), array('','-'), trim(strtolower($section['title']))); ?>"><?php echo $section['title']; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row" id="images-area">
            <?php

            if($sections):
                foreach($sections AS $section):
                    if($section['images']):
                        foreach($section['images'] AS $image):
                ?>
                <div id="feature-<?php echo esc_attr(sanitize_title($image['title'])); ?>" class="col-sm-4 <?php echo str_replace(array('.',' '), array('','-'), trim(strtolower($section['title']))); ?>" style="scroll-margin-top:130px;">
                    <div class="image-wrapper">

                        <div class="placeholder">
                            <?php if(wp_get_attachment_image($image['image'], 'medium')): echo wp_get_attachment_image($image['image'], 'medium'); else: ?><img src="https://via.placeholder.com/300C/O" /><?php endif; ?>
                            <div class="image-tools">
                                <a href="<?php echo wp_get_attachment_image_url($image['image'], 'full'); ?>" class="image-lightbox">
                                    <i class="fas fa-plus"></i>
                                </a>
                                <?php if($image['link']): ?>
                                <a href="<?php echo $image['link']['url']; ?>" <?php if($image['link']['target']): ?>target="_blank"<?php endif; ?>>
                                    <i class="fas fa-ellipsis-h"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="title">
                            <?php if($image['link']): ?>
                            <a href="<?php echo $image['link']['url']; ?>" <?php if($image['link']['target']): ?>target="_blank"<?php endif; ?>><?php echo $image['link']['title']; ?></a>
                            <?php else: ?>
                            <?php echo $image['title']; ?>
                            <?php endif; ?>
                        </div>
                        <div class="subtitle">
                            <?php echo $image['description']; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; endif; endforeach; endif; ?>
        </div>
    </div>
</section>