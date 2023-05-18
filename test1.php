<?php
include_once __DIR__ . '/WorkflowInstance.php';

$obj = new WorkflowInstance();

/**
 * Creating a new instance and instance handler 
 */
// $obj->set_workflow('Intern logsheet');
// $obj->set_user_by_name("Nitish Rajbongshi");
// // $obj->set_employee_id(700123);

// // $obj->set_instance_values("Telephone bill", "Telephone bill approval request", 'pdf', "telephone_bill_2023.pdf");
// $obj->set_instance_values("Intern logsheet", "Intern logsheet approval request");
// $obj->create();


/**
 * Show details related to the instance
 */
// $obj->load_instance(92);
// $obj->show_instance();


/**
 * Show details about workflow and step related to an instance
 */
// $obj->load_instance(89);
// $obj->show_workflow();


/**
 * Current step
 */
echo ("\n\nCurrent Step:");
$obj->load_instance(92);
$obj->current_step();


/**
 * Previous step
 */
echo ("\n\nPrevious Step:");
$obj->load_instance(92);
$obj->previous_step();


/**
 * Next step
 */
echo ("\n\nNext Step:");
$obj->load_instance(92);
$obj->next_step();


/**
 * Show all the logs
 */
echo ("\n\nLogs\n");
$obj->load_instance(92);
$obj->logs();



// display the instance to a particular person 
// echo ("\n\nStep for an employee\n");
// $obj->set_employee_id(500123);
// $obj->show_single_instance();


// display the instance to a particular group
// echo ("\n\nStep for a group\n");
// $obj->set_group_id(123123);
// $obj->show_group_instance();


/**
 * Update the status of the instance
 */
$obj->load_instance(92);

// $obj->set_group_id(123123);
$obj->set_employee_id(123321);

$obj->accept();     // Accept the step 
// $obj->reject();     // Reject the step 
// $obj->rollback();   // reject and go to previous step 
// $obj->goto(3);     // go to a particular id

$obj->set_remarks("Remarks");

$obj->update();
