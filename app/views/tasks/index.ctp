<h3>Tasks</h3>
<div class="cpanel">
    Display as:
<?php if ( $view_mode == 'list' ): ?>
    <strong>List</strong>&nbsp;|&nbsp;<a href="/tasks/setmode/calendar">Calendar</a>
<?php else: ?>
    <a href="/tasks/setmode/list">List</a>&nbsp;|&nbsp;<strong>Calendar</strong>
<?php endif; ?>        
    <a href="/tasks/add" style="margin-left:15px">Add task</a>
</div>