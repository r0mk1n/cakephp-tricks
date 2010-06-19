<div class="cpanel">
    Display as:
<?php if ( $view_mode == 'list' ): ?>
    <strong>List</strong>&nbsp;|&nbsp;<a href="/events/setmode/calendar">Calendar</a>
<?php else: ?>
    <a href="/events/setmode/list">List</a>&nbsp;|&nbsp;<strong>Calendar</strong>
<?php endif; ?>
    <a href="/events/add" style="margin-left:15px">Add event</a>
</div>
<?php if ( !empty( $this->data ) || $view_mode == 'calendar' ): ?>
    <?= $this->element( "events/{$view_mode}" ); ?>
<?php else: ?>
        <div class="cmessage">You have no incomplete events.</div>
<?php endif; ?>
<div id="location_info_popup"></div>
