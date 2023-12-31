<div class="tab-pane" id="drugs">
  <script>
    $(document).ready(function() {
      //script of assign drug click
      $('#addDrug').click(function() {
        $.ajax($('#addDrug').attr('href')).done(function(data) {
          $('#tmpDiv').html(data);
        });
        return false;
      });
      //script of payment click
      $('#drugGroup tr > td> a').on('click', drugItemsAction);
    });
  </script>
  <script>
    function drugItemsAction(e) {
      e.preventDefault();
      // console.log("values : "+$(this).attr('csrf_test_name'));
      var csrfVal = $(this).attr('csrf_test_name'); //$('form input[name="csrf_test_name"]').val();
      var dpi = $(this).attr('dpi');
      var di = $(this).attr('di');
      var pi = $(this).attr('pi');
      var tc = $(this).attr('tc') * 1;
      if ($(this).attr('action') == 'pay') {
        $.post('<?php echo site_url("drug/payment") . "/"; ?>' + dpi, 'csrf_test_name=' + csrfVal + '&drug_patient_id=' + dpi + '&drug_id=' + di + '&patient_id=' + pi, function(data) {
          if (data == 'ok') {
            $('#dpi' + dpi + ' .actions').html('');
            var paid = $('#paid').text() * 1;
            var unpaid = $('#unpaid').text() * 1;
            paid += tc;
            unpaid -= tc;
            $('#paid').text(paid);
            $('#unpaid').text(unpaid);

            if (paid > 0) $('.paid').removeClass('hidden');
            else $('.paid').addClass('hidden');
            if (unpaid > 0) $('.unpaid').removeClass('hidden');
            else $('.unpaid').addClass('hidden');
            if (paid > 0 && unpaid > 0) $('.tc').removeClass('hidden');
            else $('.tc').addClass('hidden');
          } else if (data == 'mismatch') {
            alert('Payment data mismatch');
          } else if (data == 'invalid') {
            alert('Invalid data');
          }
        });
      } else if ($(this).attr('action') == 'delete') {
        var csrfVal = $(this).attr('csrf_test_name'); //$('form input[name="csrf_test_name"]').val();

        $.post('<?php echo site_url("drug/deletedpi") . "/"; ?>' + dpi, 'csrf_test_name=' + csrfVal + '&drug_patient_id=' + dpi + '&drug_id=' + di + '&patient_id=' + pi, function(data) {
          if (data == 'ok') {
            $('#dpi' + dpi).remove();
            var paid = $('#paid').text() * 1;
            var unpaid = $('#unpaid').text() * 1;
            $('#unpaid').text(unpaid -= tc);
            $('#tc').html(unpaid + paid);
            if (unpaid > 0) $('.unpaid').removeClass('hidden');
            else $('.unpaid').addClass('hidden');
            if (paid > 0 && unpaid > 0) $('.tc').removeClass('hidden');
            else $('.tc').addClass('hidden');
          } else if (data == 'mismatch') {
            alert('Delete data mismatch');
          } else if (data == 'invalid') {
            alert('Invalid data');
          }
        });
      }
    }
  </script>
  <?php
  if (($this->session->userdata('ba_user_id') == $doctor->user_id || $this->bitauth->has_role('pharmacy')) && $status_code > 1) {
    //searchbox for drug + result
    echo anchor('drug/search', 'Assign a Drug', array('id' => 'addDrug'));
    //hidden form to inject info to
    echo "<div class='hidden'>" . form_open('drug/assign', array('id' => 'addDrugForm'));
    echo form_input('drug_id', '', 'id="drug_id"');
    echo form_input('patient_id', $patient->patient_id, 'id="patient_id"');
    echo form_input('times', '', 'id="times"');
    echo form_input('slot', '', 'id="slot"');
    echo form_input('type', '', 'id="type"');
    echo form_input('patient_doctor_id', '', 'id="drug_patient_doctor_id"');
    echo form_input('dosage_mg', '', 'id="dosage_mg"');
    echo form_input('dosage_ml', '', 'id="dosage_ml"');
    echo form_input('dosage', '', 'id="dosage"');
    // echo form_input('ofc', '', 'id="ofc"');
    // echo form_input('weight', '', 'id="weight"');
    // echo form_input('height', '', 'id="height"');
    echo form_input('no_of_item', '', 'id="no_of_item"');
    echo form_input('total_cost', '', 'id="total_cost"');
    echo form_input('memo', '', 'id="memo"');
    echo form_close() . '</div>';
  } ?>
  <div id='drugError'></div>
  <div id='drugGroup' class="responsive-table">
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>Prescribed Date</th>
          <!-- th>Type</th -->
          <th>Name</th>
          <!-- th>نام</th -->
          <th>Times</th>
          <th>Slot</th>
          <!-- th>OFC</th -->
          <!-- th>Weight</th -->
          <!-- th>Height</th -->
          <!-- th>UPrice</th -->
          <th>Days</th>
          <!-- th>Total Cost</th -->
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (@$drugs) {
          $i = 0;
          $paid = 0;
          $unpaid = 0;
          foreach ($drugs as $drug) {

            //table item for each drug
            $this->drugs->load($drug->drug_id);
            $epoch = $drug->assign_date;
            $dt = new DateTime("@$epoch");
            $prescribed_datetime = $dt->format('Y-m-d H:i:s');

            echo '<tr id="dpi' . $drug->drug_patient_id . '"><td class="id">' . ++$i . '</td>' .
              '<td>' . $prescribed_datetime . '</td>' .
              // '<td>' . $drug->type . '</td>' .
              '<td>' . $this->drugs->drug_name_en . '</td>' .
              '<!-- td>' . $this->drugs->drug_name_fa . '</td -->' .
              '<td>' . $drug->times . '</td>' .
              '<td>' . $drug->slot . '</td>' .
              // '<td>' . $drug->ofc . '</td>' .
              // '<td>' . $drug->weight . '</td>' .
              // '<td>' . $drug->height . '</td>' .
              '<!-- td>' . $drug->drugs->price . '</td -->' .
              '<td>' . $drug->no_of_item . '</td>' .
              '<!-- td>' . $drug->total_cost . '</td -->';
            if (!($drug->user_id_discharge && $drug->discharge_date)) {
              $unpaid += $drug->total_cost;
              echo '<td class="actions">' . anchor('#', 'Delete ', array('csrf_test_name' => $this->security->get_csrf_hash(), 'dpi' => $drug->drug_patient_id, 'di' => $drug->drug_id, 'pi' => $drug->patient_id, 'action' => 'delete',/*'onclick'=>'drugItemsAction',*/ 'tc' => $drug->total_cost));
              if ($this->bitauth->has_role('receptionist')) {
                // echo anchor('#', 'Pay ', array('csrf_test_name' => $this->security->get_csrf_hash(), 'dpi' => $drug->drug_patient_id, 'di' => $drug->drug_id, 'pi' => $drug->patient_id, 'action' => 'pay',/*'onclick'=>'drugItemsAction',*/ 'tc' => $drug->total_cost));
              } else {
                echo '</td>';
              }
            } else {
              $paid += $drug->total_cost;
              echo '<td></td>';
            }
            echo '</tr>';
          }
          // echo '<tr class="paid text-success ' . ($paid ? '' : 'hidden') . '"><td></td><td></td><td></td><td></td><td>Paid:</td><td id="paid">' . $paid . '</td><td></td></tr>';
          // echo '<tr class="unpaid text-danger ' . ($unpaid ? '' : 'hidden') . '"><td></td><td></td><td></td><td></td><td>Unpaid:</td><td id="unpaid">' . $unpaid . '</td><td></td></tr>';
          // echo '<tr class="tc text-info ' . ($paid && $unpaid ? '' : 'hidden') . '"><td></td><td></td><td></td><td></td><td>Total:</td><td id="tc">' . ($paid + $unpaid) . '</td><td></td></tr>';
        } ?>
      </tbody>
    </table>
  </div>
</div>