<?php
echo anchor('patient/', '<span class="glyphicon glyphicon-user"></span> <br/>Patients', array("class" => "btn btn-info btn-lg", "role" => "button"));
echo anchor('patient/waiting', '<span class="glyphicon glyphicon-time"></span> <br/>Waiting List', array("class" => "btn btn-info btn-lg", "role" => "button"));
echo anchor('patient/inprogress', '<span class="glyphicon glyphicon-repeat"></span> <br/>In Progress Patients', array("class" => "btn btn-info btn-lg", "role" => "button", "title" => "In Progress Patients List"));
