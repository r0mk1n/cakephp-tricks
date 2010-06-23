<div class="p30">
    <table>
        <tr><th colspan="2">Users</th></tr>
        <tr><td>Activated</td><td><?= $this->data['User']['activated'] ?></td></tr>
        <tr><td>Not activated</td><td><?= $this->data['User']['not_activated'] ?></td></tr>
        <tr style="font-weight:bold"><td>Total</td><td><?= $this->data['User']['activated'] + $this->data['User']['not_activated'] ?></td></tr>
    </table>
</div>
<div class="p30">
    <table>
        <tr><th colspan="2">Events</th></tr>
        <tr><td>Complete</td><td><?= $this->data['Event']['complete'] ?></td></tr>
        <tr><td>Non complete</td><td><?= $this->data['Event']['not_complete'] ?></td></tr>
        <tr><td>Expired *<br /><sub>part on non-complete events</sub></td><td><?= $this->data['Event']['expired'] ?></td></tr>
        <tr style="font-weight:bold"><td>Total</td><td><?= $this->data['Event']['complete'] + $this->data['Event']['not_complete'] ?></td></tr>
    </table>
</div>
<div class="p30">
    <table>
        <tr><th colspan="2">Locations</th></tr>
        <tr style="font-weight:bold"><td>Total</td><td><?= $this->data['Location']['all'] ?></td></tr>
    </table>
</div>
