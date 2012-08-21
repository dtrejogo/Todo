
<?php if (validation_errors()) : ?>
    <div class="notification error">
        <?php echo validation_errors(); ?>
    </div>
<?php endif; ?>
<?php
// Change the css classes to suit your needs    
if (isset($todo)) {
    $todo = (array) $todo;
}
$id = isset($todo['id']) ? "/" . $todo['id'] : '';
?>
<?php echo form_open($this->uri->uri_string(), 'class="constrained ajax-form"'); ?>
    <?php if (isset($todo['id'])): ?><input id="id" type="hidden" name="id" value="<?php echo $todo['id']; ?>"  /><?php endif; ?>
<div>
<?php echo form_label('Title', 'todo_title'); ?> <span class="required">*</span>
    <input id="todo_title" type="text" name="todo_title" maxlength="255" value="<?php echo set_value('todo_title', isset($todo['todo_title']) ? $todo['todo_title'] : ''); ?>"  />
</div>

<div>
    <?php echo form_label('Description', 'todo_description'); ?> &nbsp;
<?php echo form_textarea(array('name' => 'todo_description', 'id' => 'todo_description', 'rows' => '5', 'cols' => '80', 'value' => set_value('todo_description', isset($todo['todo_description']) ? $todo['todo_description'] : ''))) ?>
</div>
<div>
<?php echo form_label('date', 'todo_date'); ?> <span class="required">*</span>
    <script>head.ready(function(){$('#todo_date').datetimepicker({ dateFormat: 'mm/dd/yy', timeFormat: 'hh:mm:ss', minDate:0});});</script>
    <input id="todo_date" type="text" name="todo_date"  value="<?php echo set_value('todo_date', isset($todo['todo_date']) ? $todo['todo_date'] : ''); ?>"  />
    
</div>

<div>
    <?php echo form_label('Priority', 'todo_priority'); ?> <span class="required">*</span>
<?php print form_dropdown('todo_priority', $priorities, set_value('todo_priority', isset($todo['todo_priority']) ? $todo['todo_priority'] : '')); ?>
</div>




<div class="text-right">
    <br/>
    <input type="submit" name="submit" value="Create Todo" /> or <a href="#index"><?php print lang('todo_cancel')?></a> 
</div>
<?php echo form_close(); ?>
