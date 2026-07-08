<?php $block = $args['block']; ?>
<section class="features-benefits">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="title">
                    <?php if($block['title']): ?>
                    <h2><?php echo $block['title']; ?></h2>
                    <?php endif; ?>
                    <?php if($block['subtitle']): ?>
                    <h3><?php echo $block['subtitle']; ?></h3>
                    <?php endif; ?>
                </div>
                <?php $features = $block['features']; if($features):  ?>
                <div class="accordion" id="features-benefits">
                    <?php foreach($features AS $i=>$feature): ?>
                    <div class="card">
                        <div class="card-header" id="heading-<?php echo $i; ?>">
                            <a href="#" data-toggle="collapse" data-target="#collapse-<?php echo $i; ?>" aria-controls="collapse-<?php echo $i; ?>" <?php if($feature['open_close']): ?>aria-expanded="true"<?php endif; ?>>
                                <?php echo $feature['title']; ?>
                            </a>
                        </div>
                        <div id="collapse-<?php echo $i; ?>" class="collapse <?php if($feature['open_close']): ?>show<?php endif; ?>" aria-labelledby="heading-<?php echo $i; ?>" data-parent="#features-benefits">
                            <div class="card-body">
                                <?php echo $feature['content']; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>