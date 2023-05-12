<?php 
    include_once __DIR__ . '/WorkflowInstance.php';

    $obj = new WorkflowInstance();

    // $obj->set_workflow('Intern Logsheet');
    // $obj->set_user_by_name("Nitish rajbongshi");
    // // $obj->set_user_by_id(1);

    // // $obj->set_instance_values("Telephone bill", "Telephone bill approval request", 'pdf', "telephone_bill_2023.pdf");
    // $obj->set_instance_values("Intern logsheet", "Intern logsheet approval request", 'pdf', "logsheet_2023.pdf");
    // $obj->create();

    // $obj->load(56);
    // $obj->show();
    
    // display the instance to a particular person 
    // $obj->set_employee_id(500123);
    // $obj->show_instance_by_person();

    // $obj->set_group_id(123111);
    // $obj->show_instance_by_group();

    /**
     * Function to load and display the current status of the instance
     * 
     */
    // $obj->load_instance(64);
    // $obj->set_current_status();
    // $obj->display_status();
    
    // $obj->load_instance(18);
    // $obj->go_particular_step(4);
    
    /**
     * Update the status of the instance
    */
    $obj->load_instance(71);
    $obj->set_group_id(123123);
    $obj->set_employee_id(120023);
    $obj->set_status("accept");
    $obj->set_acknowledgement("Accept and forward");
    $obj->update_status();
    // $obj->update_instance();

    // modify new status

?>