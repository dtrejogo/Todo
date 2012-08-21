<div id="ajax-content">

    <div class="box create rounded">

        <a class="button good" id="create" rel="create" href="#add">
            <?php echo lang('todo_create_new_button'); ?>
        </a>

        <h3><?php echo lang('todo_create_new'); ?></h3>

        <p><?php echo lang('todo_edit_text'); ?></p>

    </div>

    <br />

    <?php if (isset($records) && is_array($records) && count($records)) : ?>

        <h2>Todo</h2>
        <table>
            <thead>
                <tr>

                    <th>Title</th>
                    <th>Description</th>
                    <th>
                        <?php if ($order == "date" && $type == "asc"): ?>
                            <a href="#index/date/desc">date</a>
                            <img src="<?php print site_url('bonfire/themes/admin/images/up.png') ?>"/>
                        <?php elseif ($order == "date" && $type == "desc"): ?>
                            <a href="#index/date/asc">date</a>
                            <img src="<?php print site_url('bonfire/themes/admin/images/down.png') ?>"/>
                        <?php else: ?>
                            <a href="#index/date/asc">date</a>
                        <?php endif ?>
                    </th>
                    <th>
                        <?php if ($order == "priority" && $type == "asc"): ?>
                            <a href="#index/priority/desc">Priority</a>
                            <img src="<?php print site_url('bonfire/themes/admin/images/up.png') ?>"/>
                        <?php elseif ($order == "priority" && $type == "desc"): ?>
                            <a href="#index/priority/asc">Priority</a>
                            <img src="<?php print site_url('bonfire/themes/admin/images/down.png') ?>"/>
                        <?php else: ?>
                            <a href="#index/priority/asc">Priority</a>
                        <?php endif ?>
                    </th>
                    <th>Completed</th>

                    <th><?php echo lang('todo_actions'); ?></th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($records as $record) : ?>
                    <tr id="row-<?php print $record->id ?>">

                        <td><?php echo $record->todo_title ?></td>
                        <td><?php echo $record->todo_description ?></td>
                        <td><?php echo  date('m/d/Y h:i a', strtotime($record->todo_date)); ?></td>
                        <td><?php echo $priorities[$record->todo_priority] ?></td>
                        <td id="done-<?php echo $record->id ?>"><?php echo $record->todo_completed ? 'yes' : 'no' ?></td>
                        <td>
                            <a  href="<?php print '#edit/' . $record->id ?>"><?php print lang('todo_edit') ?></a>
                            <a  href="#" onclick="delete_record(<?php print $record->id ?>); return false;"><?php echo lang('todo_delete_record'); ?></a>
                            <span id="done-text-<?php echo $record->id ?>">
                                <?php if (!$record->todo_completed):?>
                            <a  href="#" onclick="done_record(<?php print $record->id ?>); return false;"><?php echo lang('todo_done_record'); ?></a>
                            <?php else:?>
                            Done
                            <?php endif ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>