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
    // $obj->set_employee_id(12);
    // $obj->show_single_instance();

    // $obj->set_group_id(123123);
    // $obj->show_group_instance();

    /**
     * Function to load and display the current status of the instance
     * 
     */
    // $obj->load_instance(89);
    // $obj->set_current_status();
    // $obj->show_status();

    /**
     * Show details related to the instance
     */
    // $obj->load_instance(89);
    // $obj->show_instance();

    /**
     * Show details about workflow and step related to an instance
     */
    // $obj->load_instance(89);
    // $obj->show_workflow();


    /**
     * Previous step
     */
    $obj->load_instance(89);
    $obj->previous_step();

     /**
     * Current step
     */
    $obj->load_instance(89);
    $obj->current_step();
    // $obj->get_status();

     /**
     * Next step
     */
    $obj->load_instance(89);
    $obj->next_step();
    
    /**
     * Update the status of the instance
    */
    // $obj->load_instance(89);

    // // $obj->set_group_id(123123);
    // $obj->set_employee_id(123321);

    // $obj->accept();     // Accept the step 
    // // $obj->reject();     // Reject the step 
    // // $obj->rollback();   // reject and go to previous step 
    // // $obj->goto(3);     // go to a particular id

    // $obj->set_remarks("Remarks");
    
    // $obj->update();
