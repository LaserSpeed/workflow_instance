<?php 
    include_once __DIR__ . '/WorkflowInstance.php';

    $obj = new WorkflowInstance();

    // $obj->set_workflow('Intern logsheet');
    // $obj->set_user_by_name("Nitish Rajbongshi");
    // // $obj->set_employee_id(700123);

    // // $obj->set_instance_values("Telephone bill", "Telephone bill approval request", 'pdf', "telephone_bill_2023.pdf");
    // $obj->set_instance_values("Intern logsheet", "Intern logsheet approval request");
    // $obj->create();

    // Load and show the details about the instance
    // $obj->load_instance(83);
    // $obj->show_instance_details();
    
    // display the instance to a particular person 
    // $obj->set_employee_id(500123);
    // $obj->show_instance_by_person();

    // $obj->set_group_id(123111);
    // $obj->show_instance_by_group();

    /**
     * Function to load and display the current status of the instance
     * 
     */
    // $obj->load_instance(83);
    // $obj->set_current_status();
    // $obj->show_status();

    
    /**
     * Update the status of the instance
    */
    $obj->load_instance(85);
    // $obj->set_group_id(123123);
    $obj->set_employee_id(500123);
    // // $obj->set_status("reject");
    $obj->accept();     // Accept the step 
    // $obj->reject();     // Reject the step 
    // $obj->rollback();   // reject and go to previous step 
    // // $obj->goto(5);     // go to a particular id
    $obj->set_remarks("Accept and forward");
    // $obj->set_remarks("Reject due to worng information");
    $obj->update();
?>