<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-user"></span> Add Today Task</div>
    <div class="db-header-extra"> 
</div>
</div>

<ol class="breadcrumb">
  <li><a href="<?php echo site_url() ?>">Home</a></li>
  <li class="active">Add Daily Task</li>
</ol>

 <form action="add_task" method="post">
    <div class="form-group">
      <label for="task_name">Task:</label>
      <input type="text" class="form-control" id="task_name" placeholder="Enter task" name="task_name">
    </div>
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <div class="form-group">
      <label for="hours">Task Hours Taken:</label>
      <input type="text" class="form-control" id="hours" placeholder="Enter hours taken" name="hours">
    </div>
    <div class="form-group">
      <label for="in_scope">In Scope:</label>
      <input type="text" class="form-control" id="in_scope" placeholder="Enter in scope" name="in_scope">
    </div>
    <div class="form-group">
      <label for="out_scope">Out Scope:</label>
      <input type="text" class="form-control" id="out_scope" placeholder="Enter out scope" name="out_scope">
    </div>
    <div class="form-group">
      <label for="extra_work">Extraordinay Work:</label>
      <input type="text" class="form-control" id="extra_work" placeholder="Enter if Extraordinay" name="extra_work">
    </div>
    <div class="form-group">
      <label for="remarks">Remarks:</label>
      <input type="text" class="form-control" id="remarks" placeholder="Enter remarks(if you want)" name="remarks">
    </div>
    <button type="submit" class="btn btn-default add_task">Submit</button>
  </form>

</div>