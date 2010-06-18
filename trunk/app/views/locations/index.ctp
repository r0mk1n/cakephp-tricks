<div class="cpanel">
    <a href="/locations/add" style="margin-left:15px">Add location</a>
</div>
<?php if ( !empty( $this->data ) ): ?>
<table width="100%" cellpadding="2" cellspacing="2">
    <tr>
        <th><?= $paginator->sort( 'Title', 'Location.title' ) ?></th>
        <th><?= $paginator->sort( 'Address', 'Location.address1' ) ?></th>
        <th><?= $paginator->sort( 'City', 'Location.city' ) ?></th>
        <th><?= $paginator->sort( 'State', 'Location.state' ) ?></th>
        <th><?= $paginator->sort( 'Zip', 'Location.zip' ) ?></th>
        <th width="50px">Operations</th>
    </tr>
<?php foreach( $this->data as $key => $row ): ?>
    <tr class="row-<?= $key % 2 == 0 ? 'a' : 'b' ?>">
        <td><?= $row['Location']['title'] ?></td>
        <td><?= $row['Location']['address1'] ?></td>
        <td><?= $row['Location']['city'] ?></td>
        <td><?= $row['Location']['state'] ?></td>
        <td><?= $row['Location']['zip'] ?></td>
        <td>
            <a href="/locations/edit/<?= $row['Location']['id']?>">edit</a>
            <a href="/locations/delete/<?= $row['Location']['id']?>" class="delete">del</a>
        </td>
    </tr>
<?php endforeach; ?>
</table>
<?= $this->element( 'pagination' ) ?>
<?php else: ?>
    <div class="cmessage">You have no locations yet.</div>
<?php endif; ?>
