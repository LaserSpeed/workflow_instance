<?php
include_once __DIR__ . "/Workflow.php";
include_once __DIR__ . "/Step.php";
include_once __DIR__ . "/InstanceController.php";

class WorkflowInstance extends InstanceController
{
    private $conn;

    private $instance_table;
    private $workflow_table;
    private $employee_table;
    private $step_table;
    private $status_code_table;

    private $instance_id;
    private $employee_id;
    private $group_id;
    private $workflow_id;
    private $workflow_name;
    private $instance_name;
    private $instance_description;
    private $instance_type;
    private $instance_file;
    private $instance_status;
    private $status_code;

    private $step_obj;
    private $workflow_obj;

    public function set_employee_id($employee_id)
    {
        var_dump("setting the set handleby id");
        InstanceController::set_handleby_id($employee_id);
        $this->employee_id = $employee_id;
    }

    public function get_employee_id()
    {
        return $this->employee_id;
    }

    public function set_group_id($group_id)
    {
        var_dump("setting the group id");
        InstanceController::set_group_id($group_id);
        $this->group_id = $group_id;
        var_dump("Group id: ", $this->group_id);
    }

    public function get_group_id()
    {
        return $this->group_id;
    }

    public function __construct()
    {
        $this->conn = connect_db();

        // calling the constructor of InstanceController
        parent::__construct();

        $data = json_decode(file_get_contents(__DIR__ . '/config.json'), TRUE);
        $this->instance_table = $data['instance_table'];
        $this->workflow_table = $data['workflow_table'];
        $this->employee_table = $data['employee_table'];
        $this->step_table = $data['step_table'];
        $this->status_code_table = $data['status_code'];

        $this->step_obj = new Step();
        $this->workflow_obj = new Workflow();
    }

    /**
     * Set the user who is creating an instance of the workflow model
     */
    public function set_user_by_name($employee_name)
    {
        $name = htmlspecialchars(strip_tags($employee_name));
        $this->get_id_by_employee_name($name);
    }

    public function set_user_by_id($employee_id)
    {
        $this->employee_id = $employee_id;
    }

    /**
     * Get the id of the employee by providing the name
     */
    private function get_id_by_employee_name($employee_name)
    {
        try {
            $query = '
                SELECT employee_id FROM ' . $this->employee_table . ' WHERE employee_name = :employee_name;
                ';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam('employee_name', $employee_name);
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $this->employee_id = $row['employee_id'];
                }
            }
        } catch (PDOException $e) {
            echo json_encode($e);
        }
    }

    /**
     * Set the workflow before create an instance of the workflow model
     */
    public function set_workflow($workflow_name)
    {
        $this->get_id_by_workflow_name($workflow_name);
    }

    /**
     * Get the id of the workflow by providing the name of the workflow
     */
    private function get_id_by_workflow_name($workflow_name)
    {
        try {
            $query = '
                SELECT workflow_id FROM ' . $this->workflow_table . ' WHERE workflow_name = :workflow_name;
                ';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam('workflow_name', $workflow_name);
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $this->workflow_id = $row['workflow_id'];
                }
            }
        } catch (PDOException $e) {
            echo json_encode($e);
        }
    }


    /**
     * Set the values for an instance
     */
    public function set_instance_values($name, $description, $type, $file, $status = 1)
    {
        $this->instance_name = htmlspecialchars(strip_tags($name));
        $this->instance_description = htmlspecialchars(strip_tags($description));
        $this->instance_type = htmlspecialchars(strip_tags($type));
        $this->instance_file = htmlspecialchars(strip_tags($file));
        $this->instance_status = htmlspecialchars(strip_tags($status));
    }

    /**
     * Creating a new instance
     */
    public function create()
    {
        try {
            $query = '
                    INSERT INTO ' . $this->instance_table . ' SET `employee_id` = :employee_id, `workflow_id` = :workflow_id, `instance_name` = :name, `instance_description` = :description, `instance_type` = :type, `instance_file`=:file, `instance_status`=:status
                ';
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam('employee_id', $this->employee_id);
            $stmt->bindParam('workflow_id', $this->workflow_id);
            $stmt->bindParam('name', $this->instance_name);
            $stmt->bindParam('description', $this->instance_description);
            $stmt->bindParam('type', $this->instance_type);
            $stmt->bindParam('file', $this->instance_file);
            $stmt->bindParam('status', $this->instance_status);

            if ($stmt->execute()) {
                $last_id = $this->conn->lastInsertId();
                $this->handle_instance($last_id);
                $this->load($last_id);
                return true;
            } else
                return false;
        } catch (PDOException $e) {
            echo json_encode($e);
            return false;
        }
    }

    public function load($instance_id)
    {
        try {
            $query = "
            SELECT * FROM " . $this->instance_table . " WHERE instance_id = :instance_id;
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam('instance_id', $instance_id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->instance_name = $row['instance_name'];
            $this->instance_description = $row['instance_description'];
            $this->instance_status = $row['instance_status'];
            $this->workflow_name = $this->workflow_obj->get_name_by_id($row['workflow_id']);
        } catch (PDOException $e) {
            echo json_encode($e);
            return false;
        }
    }

    public function show()
    {
        $output = "\nInstance name: " . $this->instance_name . "\nDescription: " . $this->instance_description . "\nWorkflow: " . $this->workflow_name . "\nStatus: " . $this->instance_status . "";
        echo $output;
    }


    /**
     * This function will create an instance controller 
     * According to the instance controller a person or a group can view a particular intance
     * which is only related to the person or the group 
     */
    public function handle_instance($instance_id)
    {
        try {
            var_dump("Creating new instance controller");
            $query = "
            SELECT * FROM " . $this->instance_table . " WHERE instance_id = :instance_id;
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam('instance_id', $instance_id);
            if ($stmt->execute()) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                var_dump("fetching step information");
                $this->employee_id = $row['employee_id'];
                $this->instance_id = $row['instance_id'];
                $this->workflow_id = $row['workflow_id'];
                $this->instance_status = $row['instance_status'];

                var_dump("getting the step handler id");
                // get id handler id who is responsible for the handling the step of the instance
                $handler_id = $this->step_obj->get_step_handler_id($this->employee_id, $this->workflow_id, $this->instance_status);

                var_dump("handler id is: ", $handler_id);

                // check for either group or a single person 
                $is_group = $this->step_obj->is_group();

                var_dump("is this a group ", $is_group);

                // set the values
                InstanceController::set_values($this->instance_id, $handler_id, $is_group);

                // create the new instance controller record
                if (InstanceController::create()) {
                    var_dump("all done");
                    return true;
                }
                else
                    return false;
            } else
                return false;
        } catch (PDOException $e) {
            echo json_encode($e);
            return false;
        }
    }

    /**
     * Load function to load the current status of an instance
     */
    public function load_instance($id)
    {
        try {
            var_dump("loading the instance");
            $this->instance_id = $id;
            InstanceController::set_instance_id($id);
            $query = "
                SELECT * FROM " . $this->instance_table . " WHERE instance_id = :instance_id;
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam("instance_id", $id);
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $this->workflow_id = $row['workflow_id'];
                        $this->employee_id = $row['employee_id'];
                        $this->instance_name = $row['instance_name'];
                        $this->instance_description = $row['instance_description'];
                        $this->instance_status = $row['instance_status'];

                        var_dump("loading the instance complete");
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo json_encode($e);
        }
    }

    /**
     * Set the current status of the intance 
     */
    public function set_current_status()
    {
        try {
            $query = "
            SELECT w.workflow_name, s.* FROM " . $this->workflow_table . " w INNER JOIN " . $this->step_table . " s ON w.workflow_id = s.workflow_id WHERE w.workflow_id = :workflow_id AND step_order = :step_order;
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam("workflow_id", $this->workflow_id);
            $stmt->bindParam("step_order", $this->instance_status);
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $this->step_obj->set_step_values('null', $row['step_name'], $row['step_description'], $this->instance_status, $row['step_type'], $row['step_handleby']);
                    }
                }
            }
        } catch (PDOException $e) {
            echo json_encode($e);
        }
    }


    /**
     * Display the current status of the instance 
     * Here using the step_obj calling the method of step to display the current status of the instance
     */
    public function display_status()
    {
        $this->step_obj->display_current_step();
    }


    /**
     * The below three function will handle the operation related to status of an instance
     */
    private function go_next_step()
    {
        try {
            var_dump("going to the next step");
            $updatedAt = date('Y-m-d H:i:s'); 
            $next_step = $this->instance_status + 1;
            var_dump("Next step count ", $next_step);
            $total_steps = $this->workflow_obj->step_count($this->workflow_id);
            var_dump("Total step available ", $total_steps);
            if ($next_step <= $total_steps) {
                var_dump("checking condition");
                $query = "
                UPDATE " . $this->instance_table . " SET instance_status = :status, updated_at = :updated_at WHERE instance_id = :instance_id
                ";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam("instance_id", $this->instance_id);
                $stmt->bindParam("updated_at", $updatedAt);
                $stmt->bindParam("status", $next_step);
                if ($stmt->execute()) {
                    var_dump("jump to next step");
                    return true;
                    // if ($next_step > $total_steps) {
                    //     return false;
                    // } else {
                    //     return true;
                    // }
                } else {
                    var_dump("not going to next step");
                    return false;
                }
            }
        } catch (PDOException $e) {
            echo json_encode($e);
        }
    }

    private function go_previous_step()
    {
        try {
            if ($this->instance_status > 1) {
                $updatedAt = date('Y-m-d H:i:s'); // Returns the current date and time in the format 'YYYY-MM-DD HH:MM:SS'
                $next_step = $this->instance_status - 1;
                $query = "
                    UPDATE " . $this->instance_table . " SET instance_status = :status, updated_at = :updated_at WHERE instance_id = :instance_id
                 ";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam("instance_id", $this->instance_id);
                $stmt->bindParam("updated_at", $updatedAt);
                $stmt->bindParam("status", $next_step);
                if ($stmt->execute())
                    return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo json_encode($e);
        }
    }

    private function go_particular_step($step_number)
    {
        try {
            $total_steps = $this->workflow_obj->step_count($this->workflow_id);
            $updatedAt = date('Y-m-d H:i:s');
            if ($step_number >= 1 and $step_number <= $total_steps) {
                $query = "
                    UPDATE " . $this->instance_table . " SET instance_status = :status, updated_at = :updated_at WHERE instance_id = :instance_id
                 ";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam("instance_id", $this->instance_id);
                $stmt->bindParam("updated_at", $updatedAt);
                $stmt->bindParam("status", $step_number);
                if ($stmt->execute())
                    return true;
                else
                    return false;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo json_encode($e);
        }
    }


    /**
     * Show instance to a perticular person
     */
    public function show_instance_by_person()
    {
        try {
            $employee_id = $this->get_employee_id();
            InstanceController::load_instance_by_person($employee_id);
        } catch (PDOException $e) {
            echo json_encode($e);
            return false;
        }
    }

    /**
     * Show instance to a perticular group
     */
    public function show_instance_by_group()
    {
        try {
            $group_id = $this->get_group_id();
            InstanceController::load_instance_by_group($group_id);
        } catch (PDOException $e) {
            echo json_encode($e);
            return false;
        }
    }

    public function set_status($status)
    {
        $this->status_code = $this->get_status_name($status);
        var_dump("this is the status code: ", $this->status_code);
        InstanceController::set_status($this->status_code);
    }

    public function update_instance()
    {
        var_dump("Current status code: ", $this->status_code);
        if ($this->status_code == 1) {
            if ($this->go_next_step()) {
                $this->handle_instance($this->instance_id);
            } else {
                var_dump("not going for next step");
                return false;
            }
        }

        if ($this->status_code == -2) {
            $this->go_previous_step();
        }
    }

    private function get_status_name($status)
    {
        $query = "
                SELECT * FROM " . $this->status_code_table . " WHERE status_name = :status_name;
            ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam("status_name", $status);
        $stmt->execute();
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['status_code'];
        }
    }

    public function set_acknowledgement($ack)
    {
        InstanceController::set_acknowledgement($ack);
    }

    public function update_status()
    {
        InstanceController::update();
    }
}