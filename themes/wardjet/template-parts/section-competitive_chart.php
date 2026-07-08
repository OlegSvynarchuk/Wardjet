<?php $block = $args['block']; ?>
<section class="competitve-chart">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-8">
                <?php if($block['title']): ?>
                    <div class="title">
                        <h2><?php echo $block['title']; ?></h2>
                        <?php echo $block['description']; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">

                <?php $features = $block['items']; if($features):  ?>
                    <div class="accordion" id="features-benefits">
                        <?php foreach($features AS $i=>$feature): ?>
                            <div class="card">
                                <div class="card-header" id="heading-<?php echo $i; ?>">
                                    <a href="#" data-toggle="collapse" data-target="#collapse-<?php echo $i; ?>" aria-controls="collapse-<?php echo $i; ?>"  aria-expanded="true">
                                        <?php echo $feature['title']; ?>
                                    </a>
                                </div>
                                <div id="collapse-<?php echo $i; ?>" class="collapse show" aria-labelledby="heading-<?php echo $i; ?>" data-parent="#features-benefits">
                                    <div class="card-body">
                                        <?php if($feature['content_type'] == 'table'):
                                            $table = $feature['table'];
                                            if ( ! empty ( $table ) ) :
                                            echo '<table>';
                                                if ( ! empty( $table['caption'] ) ):
                                                echo '<caption>' . $table['caption'] . '</caption>';
                                                endif;
                                                if ( ! empty( $table['header'] ) ):
                                                echo '<thead>';
                                                echo '<tr>';
                                                    foreach ( $table['header'] as $th ):
                                                    echo '<th>';
                                                        echo $th['c'];
                                                        echo '</th>';
                                                    endforeach;
                                                    echo '</tr>';
                                                echo '</thead>';
                                                endif;
                                                echo '<tbody>';
                                                foreach ( $table['body'] as $tr ):
                                                echo '<tr>';
                                                    foreach ( $tr as $td ):
                                                    echo '<td>';
                                                        echo $td['c'];
                                                        echo '</td>';
                                                    endforeach;
                                                    echo '</tr>';
                                                endforeach;
                                                echo '</tbody>';
                                                echo '</table>';
                                            endif;
                                        else: ?>
                                        <?php echo $feature['content']; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php $popups = $block['popups']; if($popups):  ?>
                    <div class="popup-links">
                        <?php foreach($popups AS $popup): ?>
                            <a href="#" data-toggle="modal" data-target="#<?php echo $popup['id']; ?>"><?php echo $popup['title']; ?></a>
                        <?php endforeach; ?>
                    </div>
                    <?php foreach($popups AS $popup): ?>
                        <div class="modal fade" id="<?php echo $popup['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $popup['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel"><?php echo $popup['popup_title']; ?></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p><?php echo $popup['popup_description']; ?></p>
                                        <div class="row">
                                        <?php
                                        foreach($popup['content'] as $block):
                                            get_template_part('template-parts/section', $block['acf_fc_layout'], ['block'=>$block]);
                                        endforeach;
                                        ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>