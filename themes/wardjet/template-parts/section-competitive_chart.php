<?php $block = $args['block'];
$items = isset($block['items']) ? $block['items'] : array();
?>
<section class="competitive-chart">
    <div class="competitive-chart__inner">
        <?php if (!empty($block['title'])) : ?>
            <h2 class="competitive-chart__title"><?php echo esc_html($block['title']); ?></h2>
        <?php endif; ?>

        <?php if (!empty($items)) : ?>
            <div class="competitive-chart__cards">
                <?php foreach ($items as $item) : ?>
                    <div class="competitive-chart__card">
                        <h3 class="competitive-chart__card-title"><?php echo esc_html($item['title']); ?></h3>
                        <?php if (isset($item['content_type']) && $item['content_type'] === 'table' && !empty($item['table'])) : ?>
                            <div class="competitive-chart__card-content">
                                <?php
                                $table = $item['table'];
                                echo '<table>';
                                if (!empty($table['header'])) {
                                    echo '<thead><tr>';
                                    foreach ($table['header'] as $th) { echo '<th>' . esc_html($th['c']) . '</th>'; }
                                    echo '</tr></thead>';
                                }
                                echo '<tbody>';
                                foreach ($table['body'] as $tr) {
                                    echo '<tr>';
                                    foreach ($tr as $td) { echo '<td>' . esc_html($td['c']) . '</td>'; }
                                    echo '</tr>';
                                }
                                echo '</tbody></table>';
                                ?>
                            </div>
                        <?php elseif (!empty($item['content'])) : ?>
                            <div class="competitive-chart__card-content"><?php echo wp_kses_post($item['content']); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (isset($block['popups']) && !empty($block['popups'])) :
        $popups = $block['popups'];
    ?>
        <div class="competitive-chart__inner">
            <div class="popup-links">
                <?php foreach ($popups as $popup) : ?>
                    <a href="#" data-toggle="modal" data-target="#<?php echo esc_attr($popup['id']); ?>"><?php echo esc_html($popup['title']); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php foreach ($popups as $popup) : ?>
            <div class="modal fade" id="<?php echo esc_attr($popup['id']); ?>" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?php echo esc_html($popup['popup_title']); ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <p><?php echo wp_kses_post($popup['popup_description']); ?></p>
                            <div class="row">
                            <?php foreach ($popup['content'] as $pblock) :
                                get_template_part('template-parts/section', $pblock['acf_fc_layout'], ['block' => $pblock]);
                            endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
