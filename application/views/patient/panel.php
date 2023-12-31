<style>
  input#checkup_fee {
    margin-right: 5px;
  }

  #printit {
    margin-right: 10px;
  }
</style>

<?php




$status_code = $doctor->status;
switch ($status_code) {
  case 0:
    $status = 'Waiting';
    break;
  case 1:
    $status = 'Finished';
    break;
  case 2:
    $status = 'Treatment';
    break;
  default:
    $status = 'Unknown';
    break;
}
if (!empty($patient->patient_id)) {


?>
  <div class="panel-group" id="pInfo">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#pInfo" href="#pInfoBody">
            <?php echo html_escape($patient->patient_id . ' - ' . $patient->first_name . ' ' . $patient->last_name) . ' (<span id="status">' . $status . '</span>)'; ?>
          </a>
          <?php if ($status_code != 1) {
            echo '<div class="pull-right" id="statusFormContainer">';
            echo form_open('patient/status/' . $doctor->patient_doctor_id, array('id' => 'statusForm')); //should return new status
            echo form_hidden('patient_doctor_id', $doctor->patient_doctor_id);
            echo form_hidden('patient_id', $patient->patient_id);
            echo form_hidden('user_id', $this->session->userdata('ba_user_id'));
            echo form_hidden('status', $status_code ? 1 : 2);
            echo form_hidden('url', current_url());
            echo form_button('button', 'Print', 'id="printit"');
            echo form_submit('submit', $status_code ? 'Finish' : 'Accept', 'id="statusLink"');
            echo form_close(); ?>
      </div>
    <?php } ?>
    <?php if ($status_code == 1) {
      echo '<div class="pull-right" id="statusFormContainer">';
      echo form_open('patient/status/' . $doctor->patient_doctor_id, array('id' => 'statusForm')); //should return new status
      echo form_hidden('patient_doctor_id', $doctor->patient_doctor_id);
      echo form_hidden('patient_id', $patient->patient_id);
      echo form_hidden('user_id', $this->session->userdata('ba_user_id'));
      echo form_hidden('status', 0);
      echo form_hidden('url', current_url());
      echo form_input('fee', 400, 'id="checkup_fee"');
      echo form_submit('submit', 'Create Appointment', 'id="statusLink"');
      echo form_close(); ?>
    </div>
  <?php } ?>

  </h4>
  </div>
  <div id="pInfoBody" class="panel-collapse collapse in">
    <div class="panel-body">
      <div class='col col-xs-6'>
        <label>Patient Doctor ID: </label>
        <strong id="fk_patient_doctor_id"><?php echo $doctor->patient_doctor_id; ?></strong><br />
        <label>Card No: </label> <?php echo html_escape($patient->card_no); ?><br />
        <label>Father Name: </label> <?php echo html_escape($patient->fname); ?><br />
        <label>Gender: </label> <?php echo $patient->gender ? 'Male' : 'Female'; ?><br />
        <label>Phone: </label> <?php echo html_escape($patient->phone); ?><br />
        <label>Date of Birth: </label> <?php echo $patient->birth_date; ?><br />
        <?php if ($patient->address) echo '<label>Address: </label>' . html_escape($patient->address) . '<br/>';
        if ($patient->memo) echo '<label>Memo: </label>' . html_escape($patient->memo) . '<br/>';
        ?>
      </div>
      <div class="col col-xs-6">
        <label>Date: </label> <?php echo date('M d, Y', gmt_to_local($patient->create_date, 'UP45')); ?><br />
        <label><?php echo $patient->id_type; ?>: </label> <?php echo html_escape($patient->social_id); ?><br />
        <label>Doctor: </label> <?php echo html_escape(@$doc_info->first_name . ' ' . @$doc_info->last_name); ?><br />
        <label>Email: </label> <?php echo html_escape($patient->email); ?><br />
      </div>
      <div class="col col-xs-6">
        <label>Status: </label> <?php if (intval($patient->is_active) === 0) echo "Inactive/Deleted";
                                else echo "Active"; ?><br />

        <label>Charges Collected: </label>
        <strong id="currentFees"><?php echo $doctor->fee; ?></strong>
        <a href="#" href="" data-toggle="modal" data-target="#feesform" id="change-fee">Change</a><br />


      </div>

      <?php echo '<div class="pull-right" title="Go to Patients">' . anchor('patient', '<span class="glyphicon glyphicon-arrow-left"></span>') . '</div>'; ?>
    </div>
  </div>
  </div>
  </div>
  <div>
    <ul class="nav nav-tabs" id="panelTab">
      <li class="active"><a href="#redalerts" data-toggle="tab">
          <font color='red'>Red Alert
        </a></font>
      </li>
      <li><a href="#comments" data-toggle="tab">Comments</a></li>
      <li><a href="#examination" data-toggle="tab">Examination</a></li>
      <li><a href="#drugs" data-toggle="tab">Drugs</a></li>
      <li><a href="#xrays" data-toggle="tab">X-Rays</a></li>
      <li><a href="#labs" data-toggle="tab">Laboratory Tests</a></li>
    </ul>

    <div class="tab-content">
      <style>
        .tab-pane {
          padding-top: 10px;
        }
      </style>
      <?php
      include_once 'panel/redalert.php';
      include_once 'panel/comments.php';
      include_once 'panel/examination.php';
      include_once 'panel/drugs.php';
      include_once 'panel/labs.php';
      include_once 'panel/xrays.php';
      ?>
    </div>
    <script>
      $(function() {
        $('#panelTab a:first').tab('show')
      })
    </script>
  </div>
<?php
} else {
  echo '<div class="alert alert-danger text-center"><h1>Patient Not Found</h1></div><div class="pull-right" title="Go to Patients">' . anchor('patient', '<span class="glyphicon glyphicon-arrow-left"></span>') . '</div>';
}
?>

<script>
  $(document).ready(function() {

    $('#printit').click(function() {
      window.location.href = "<?php echo site_url("patient/print_prescription_pad/" . $patient->patient_id); ?>";
    })

  });
</script>