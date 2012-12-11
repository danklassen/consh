<div class="control-group">
  <?php print $form->label('{field_name}', t('{field_title}'))?>
  <div class="controls">
    <?php print $page_selector_form_helper->selectPage('{field_name}', ${field_name});?>
  </div>
</div>
