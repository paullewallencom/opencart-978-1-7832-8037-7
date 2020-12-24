<div class="box">
  <div class="box-heading"><?php echo $heading_title; ?></div>
  <div class="box-content">
    <ul class="box-feedback">
      <?php foreach ($feedbacks as $feedback) { ?>
      <li>
		<?php echo $feedback['description']; ?>
        --<?php echo $feedback['feedback_author']; ?>
      </li>
      <?php } ?>
    </ul>
    <a href="<?php echo $href; ?>"><?php echo $viewall; ?></a>
  </div>
</div>
