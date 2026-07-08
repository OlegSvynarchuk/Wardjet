<?php $block = $args['block']; ?>
<section>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-sm-10 text-center">
                <h1><?php echo $block['title']; ?></h1>
                <?php 
                if ($block['subtitle']):
                ?>
                    <p><?php echo $block['subtitle']; ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>