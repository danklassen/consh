<div class="control-group">
  <?php print $form->label('{field_name}', t('{field_title}'))?>
  <div class="controls">
  	<?php Loader::element('editor_config'); ?>
    <?php Loader::element('editor_controls'); ?>
    <?php print $form->textarea('{field_name}', ${field_name}, array('class' => 'ccm-advanced-editor'));?>
  </div>
</div>
