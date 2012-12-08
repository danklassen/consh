<?php defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
?>

<div class="control-group">
  <?php print $form->label('heading', t('Heading'))?>
  <div class="controls">
    <?php print $form->text('heading',$heading);?>
  </div>
</div>
