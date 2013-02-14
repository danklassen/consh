<div class="control-group">
  <?php print $form->label('{field_name}', t('{field_title}'))?>
  <div class="controls">
    <?php ${field_name}_file = File::getByID(${field_name}); ?>
    <?php print $file_form_helper->image('{field_name}', '{field_name}', "Select {field_title}", ${field_name}_file);?>
  </div>
</div>
