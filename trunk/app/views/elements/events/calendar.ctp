<?php
    $first_day = 0;
    $first_of_month = gmmktime( 0, 0, 0, $month, 1, $year );
    $day_names = array();
    for ($n=0, $t = ( 3 + $first_day ) * 86400; $n < 7; $n++ , $t+=86400) {
        $day_names[$n] = ucfirst( gmstrftime( '%A', $t ) );
    }
    list ( $month, $year, $month_name, $weekday ) = explode( ',', gmstrftime( '%m,%Y,%B,%w', $first_of_month ) );

    $weekday = ( $weekday + 7 - $first_day ) % 7;
    $title   = htmlentities( ucfirst( $month_name ) ) . '&nbsp;' . $year;

// calculating next/prev links
    if ( $month > 1 ) {
        $prev_month = $month - 1;
        $prev_year = $year;
    } else {
        $prev_month = 12;
        $prev_year = $year - 1;
    }

    if ( $month < 12 ) {
        $next_month = $month + 1;
        $next_year = $year;
    } else {
        $next_month = 1;
        $next_year = $year + 1;
    }
?>
<div class="row-b" style="text-align:center;padding: 10px;font-weight:bold;">
    <a href="/events/index/month:<?= $prev_month?>/year:<?= $prev_year ?>" style="float:left">&laquo;</a>
    <?= $month_name ?>
    <a href="/events/index/month:<?= $next_month?>/year:<?= $next_year ?>" style="float:right">&raquo;</a>
</div>
<table cellpadding="5" cellspacing="5" width="100%" class="calendar">
    <tr>
<?php foreach($day_names as $d): ?>
        <th width="14%"><?= $d ?></th>
<?php endforeach; ?>
    </tr>
    <tr>
<?php if ( $weekday > 0 ): ?>
    <td colspan="<?= $weekday ?>">&nbsp;</td>
<?php endif ?>
<?php for ( $day=1, $days_in_month = gmdate( 't', $first_of_month ); $day <= $days_in_month; $day++, $weekday++ ): ?>
<?php
        if ( $weekday == 7 ){
// start a new week
            $weekday   = 0;
            echo "</tr><tr>";
        }
        $dclass = '';
        if ( intval( date( "Ymd" ) ) == intval( date( "Ymd", mktime( 0, 0, 0, $month, $day, $year ) ) ) ) {
            $dclass = ' class="today"';
        }
        if ( isset( $this->data[$day] ) && is_array( $this->data[$day] ) ):
?>
            <td <?= $dclass ?>><span><?= $day ?></span>
                <table width="100%" cellpadding="0" cellspacing="0" border="0" border-collapse="collapse">
<?php foreach ( $this->data[$day] as $k => $row ): ?>
                    <tr id="row_<?= $row['Event']['id']?>">
                        <td>
                            <?= $form->input( "Event.{$row['Event']['id']}.complete", array( 'label'=>'', 'class'=>'complete', 'value'=>$row['Event']['id'], 'type'=>'checkbox' ) ) ?>
                            <a href="/events/info/<?= $row['Event']['id']?>" class="event_info"><?= $row['Event']['title'] ?>
                        </td>
                    </tr>
<?php endforeach; ?>
                </table>
            </td>
<?php else: ?>
            <td <?= $dclass ?>><span><?= $day ?></span></td>
<?php endif; ?>
<?php endfor; ?>
<?php if ( $weekday != 7 ): ?>
    <td colspan="<?= 7 - $weekday ?>">&nbsp;</td>
<?php endif ?>
    </tr>
</table>
<div id="event_info_popup"></div>
