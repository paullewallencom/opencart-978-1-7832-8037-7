<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?php echo $heading_title; ?></h1>
  
  <?php if ($feedbacks) { ?>
<div class="content">
    <?php foreach ($feedbacks as $feedback) { ?>
    <div>
      <div class="name"><b>Name: <?php echo $feedback['feedback_author']; ?></b></div>
      <div class="description"><?php echo $feedback['description']; ?></div> 
      <hr />
    </div>
    <?php } ?>

 
  <div class="pagination"><?php echo $pagination; ?></div>
</div>
  <?php } ?>
  <?php if (!$feedbacks) { ?>
  <div class="content"><?php echo $text_empty; ?></div>
  <div class="buttons">
   <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
  </div>
  <?php } ?>
  <?php echo $content_bottom; ?></div> 
<?php echo $footer; ?>