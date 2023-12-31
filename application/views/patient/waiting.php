<legend><?php echo "- " . @$title; ?></legend>
<?php //pagination should be added if have time.
if (!empty($waitings)) {
  echo "<div class='table-responsive'><table class='table table-bordered table-striped'><thead><tr>
           <th>Sr.</th>
           <th>Token No.</th>
           <th>Card No</th>
           <th>Name</th>
           <th>Father Name</th>
           <th>Phone</th>
           <th>Age</th>
           <th>G</th>
           <th>Doctor</th>
           <th></th>
       </tr></thead><tbody>";
  $row = 1;
  foreach ($waitings as $waiting) {
    $token_no = $waiting->token_no;
    $this->patients->load($waiting->patient_id);
    $_patient = $this->patients;
    $_doctor = $this->bitauth->get_user_by_id($waiting->user_id);
    $actions = '';
    if ($this->bitauth->has_role('receptionist')) {
      $actions = anchor('patient/panel/' . $_patient->patient_id, '<span class="glyphicon glyphicon-cog"></span>', array('title' => 'Patient Control Panel'));
      $actions .= anchor('patient/edit_patient/' . $_patient->patient_id, '<span class="glyphicon glyphicon-edit"></span>', array('title' => 'Edit Patient'));
      $actions .= anchor('patient/clear/' . $_patient->patient_id, '<span class="glyphicon glyphicon-remove"></span>', array('title' => 'Clear Patient'));
    }

    echo '<tr id="' . $_patient->patient_id . '" title="' . $_patient->memo . '">' .
      '<td>' . $row . '</td>' .
      '<td>' . html_escape($token_no) . '</td>' .
      '<td>' . html_escape($_patient->card_no) . '</td>' .
      '<td>' . html_escape($_patient->first_name . ' ' . $_patient->last_name) . '</td>' .
      '<td>' . html_escape($_patient->fname) . '</td>' .
      '<td>' . html_escape($_patient->phone) . '</td><td>';
    if (isset($_patient->birth_date)) echo $_patient->birth_date;
    else echo '';
    echo '</td><td>';
    if ($_patient->gender) echo 'M';
    else echo 'F';
    echo '</td>' .
      '<td>' . @$_doctor->first_name . ' ' . @$_doctor->last_name . '</td>' .
      '<td class="hidden-print">' . $actions . '</td>' .
      '</tr>';

    $row++;
  }
  echo "</tbody></table></div>";
}
echo '<divclass="hidden-print">' . anchor('patient/register', 'Register Patient', array('class' => 'hidden-print')) . '</div>';
?>