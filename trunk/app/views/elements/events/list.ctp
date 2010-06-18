<table width="100%" cellpadding="2" cellspacing="2">
    <tr>
        <th width="20"></th>
        <th><?= $paginator->sort( 'Title', 'Event.title' ) ?></th>
        <th><?= $paginator->sort( 'Exp. date', 'Event.exp_date' ) ?></th>
        <th>Location</th>
        <th><?= $paginator->sort( 'URL', 'Event.url' ) ?></th>
        <th width="50px">Operations</th>
    </tr>
<?php foreach( $this->data as $key => $row ): ?>
    <tr class="row-<?= $key % 2 == 0 ? 'a' : 'b' ?>" id="row_<?= $row['Event']['id']?>">
        <td><?= $form->input( 'Event.complete', array( 'label'=>'', 'class'=>'complete', 'rel'=>$row['Event']['id'], 'type'=>'checkbox' ) ) ?></td>
        <td><?= $row['Event']['title'] ?></td>
        <td><?= $time->niceShort( $row['Event']['exp_date'] ) ?></td>
        <td><a href="/locations/info/<?= $row['Location']['id'] ?>"><?= $row['Location']['title'] ?></a></td>
        <td><?= $row['Event']['url'] ?></td>
        <td>
            <a href="/events/edit/<?= $row['Event']['id']?>">edit</a>
            <a href="/events/delete/<?= $row['Event']['id']?>" class="delete">del</a>
        </td>
    </tr>
<?php endforeach; ?>
</table>
<?= $this->element( 'pagination' ) ?>
