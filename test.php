<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
include_once __DIR__ . '/Workflow.php';
include_once __DIR__ . '/Step.php';


$obj = new Workflow();

/**
 * CREATE WORKFLOW
 * 
 * step1: set the value using set_value() method
 * step2: call the create() method
 * 
 * Syntax: set_value(workflow_name(string), workflow_description(string))
 */
// $obj->set_workflow_values("workflow3", "description3");
// $obj->create();

/**
 *  UPDATE WORKFLOW
 * 
 *  step1: Set the value to be updated
 *  step2: call the update() method  
*/
// $obj->set_workflow_values("workflow1", "description1");
// $obj->update("workflow");

/**
 * DELETE WORKFLOW
 * 
 * Syntax: delete(workflow_name(string))
 */
// $obj->delete('workflow');

/**
 * GET WORKFLOW
 * 
 * Get all the details about the workflow and the steps for that workflow
 */
$obj->load('Intern logsheet');
$obj->print();


/**
 * LOAD WORKFLOW
 * 
 * Load and display the workflow details only
 */
// $obj->get_workflow('workflow1');
// $obj->print_workflow();


/**
 * ADD STEP
 * 
 * step1: set the step values
 * step2: add step by add_step() method
 * 
 * Syntax: set_step_values(step_name(string), step_description(string), step_order(int), step_type(string, person||group||custom_id), step_handleby(string), workflow_name(string))
 */
// $obj->set_step_values('step5', 'description3', '4', 'person', 'FLA', 'workflow1');
// $obj->add_step();

/**
 * UPDATE STEP
 * Two methods are available: 1. Update by name, 2. Update by step_ID
 * 
 * step1: set the values to be updated
 * step2: call either update_step_by_name or update_step_by_id method to update
 * 
 * Syntax: 
 *      update_step_by_id(step_id(int))
 *      update_step_by_name(workflow_name(string), step_name(string))
 */
// $obj->set_step_values('step2', 'description2', '2', 'custom', 'HR00123');
// $obj->update_step_by_id(193);
// $obj->update_step_by_name('workflow1', 'step');


/**
 * DELETE STEP
 * 
 * Two methods are available: 1. Update by name, 2. Update by step_ID
 * 
 * Syntax: 
 *      delete_step_by_id(step_id(int))
 *      delete_step_by_name(workflow_name(string), step_name(string))
 */
// $obj->delete_step_by_id(200);
// $obj->delete_step_by_name('workflow1', 'step10');

/**
 * DETAILS ABOUT STEP
 * 
 * Get the all details related to a steps
 */
// $obj->get_step_details('workflow1', 'step2');


$obj->load_workflow('Intern logsheet');

$step1 = new Step();
$step1->set_values('Admin', 'Final approval by Admin', 'custom', '123321');
$obj->add_step_in_position(4, $step1);

// $step2 = new Step();
// $step2->set_values('step2', 'description2', 'custom', 'HRD0023');
// $obj->add_step_in_position(2, $step2);

// $step3 = new Step();
// $step3->set_values('step3', 'description3', 'group', 'Finance');
// $obj->add_step_in_position(3, $step3);

// $step4 = new Step();
// $step4->set_values('step4', 'description4', 'person', 'SLA');
// $obj->add_step_in_position(4, $step4);