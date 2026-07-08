<?php $block = $args['block']; ?>
<section class="videos-section">
    <div class="container">
        <div class="row justify-content-center">
            <?php $videos = $block['videos']; if($videos): foreach($videos AS $i=>$video): ?>
                <div class="col-sm-6">
                    <div class="video">
                        <div class="title">
                            <a><?php echo $video['title']; ?></a>
                        </div>
                        <div class="placeholder">
                            <?php echo $video['embed']; ?>
                        </div>
                        <div class="subtitle">
                            <a><?php echo $video['subtitle']; ?></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</section>