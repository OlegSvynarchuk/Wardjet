<?php

$table = get_field($args['field_name'] );
if ( ! empty ( $table ) ) {    
    echo '<table border="0" class="table table-striped table-bordered">';
        if ( ! empty( $table['caption'] ) ) {
            echo '<caption>' . $table['caption'] . '</caption>';
        }

        if ( ! empty( $table['header'] ) ) {
            echo '<thead>';
                echo '<tr>';
                    foreach ( $table['header'] as $th ) {
                        echo '<th>';
                        echo $th['c'];
                        echo '</th>';
                    }
                echo '</tr>';
            echo '</thead>';
        }
        echo '<tbody>';
        foreach ( $table['body'] as $tr ) {
            echo '<tr>';
            foreach ( $tr as $td ) {
                echo '<td>';
                echo $td['c'];
                echo '</td>';
            }

            echo '</tr>';
        }
        echo '</tbody>';
    echo '</table>';
}?>

