<?php 
// print_r($old_task);
?>
<link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css"
      rel="stylesheet">
<table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Task</th>
                <th>Hours Taken</th>
                <th>In Scope</th>
                <th>Out Scope</th>
                <th>Extra Work</th>
                <th>Remarks</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
                <?php
                foreach ($old_task as $key => $task){
                            echo '<tr>';
                        echo '<td>'.$old_task[$key]['task_name'].'</td>';
                        echo '<td>'.$old_task[$key]['hours_taken'].'</td>';
                        echo '<td>'.$old_task[$key]['in_scope'].'</td>';
                        echo '<td>'.$old_task[$key]['out_scope'].'</td>';
                        echo '<td>'.$old_task[$key]['extra_work'].'</td>';
                        echo '<td>'.$old_task[$key]['remarks'].'</td>';
                        echo '<td>'.$old_task[$key]['date_of_task'].'</td>';
                        echo '<td><a href="edit_task/'.$old_task[$key]['task_id'].'">Edit</a></td>';
                        echo '</tr>';
                    
                }
                ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Task</th>
                <th>Hours Taken</th>
                <th>In Scope</th>
                <th>Out Scope</th>
                <th>Extra Work</th>
                <th>Remarks</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </tfoot>
    </table>
    <script type="text/javascript">
        $(document).ready(function() {
    $('#example').DataTable();
} );
    </script>