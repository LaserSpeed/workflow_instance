<?php

include_once __DIR__ . "/connection.php";

class InstanceController
{
    private $conn;

    private $handler_table_person;
    private $handler_table_group;
    private $status_code_table;

    private $instance_id;
    private $step_handleby_id;
    private $group_id = null;
    private $is_group = false;
    private $response_id;
    private $remarks;
    private $status;
    private $step;
    private $status_desc;
    private $created_at;
    private $updated_at;

    protected function set_instance_id($id)
    {
        $this->instance_id = $id;
    }

    protected function set_group_id($id)
    {
        $this->group_id = $id;
    }

    protected function set_status($code)
    {
        $this->status = $code;
    }

    protected function set_step($step)
    {
        $this->step = $step;
    }

    protected function set_remarks($remarks)
    {
        $this->remarks = $remarks;
    }

    protected function set_handleby_id($id)
    {
        $this->step_handleby_id = $id;
    }

    protected function __construct()
    {
        $this->conn = connect_db();

        $data = json_decode(file_get_contents(__DIR__ . '/config.json'), TRUE);
        $this->handler_table_person = $data['handler_table_person'];
        $this->handler_table_group = $data['handler_table_group'];
        $this->status_code_table = $data['status_code'];
    }

    protected function set_values($instance_id, $handleby_id, $group = false, $status = 0)
    {
        $this->instance_id = $instance_id;
        if ($group == false) {
            $this->step_handleby_id = $handleby_id;
        } else {
            $this->group_id = $handleby_id;
            $this->is_group = true;
        }
        $this->status = $status;
    }


    /**
     * Functionality to create instance for user
     * Differentiate between single person or a group in role
     */
    protected function create()
    {
        try {
            // var_dump("Creating a new instance controller");
            // var_dump("Is a group id available here: ", $this->group_id);
            // var_dump("Is it a group : ", $this->is_group);

            if ($this->is_group) {
                // var_dump("Group id found and creating in the group table");
                $query = "
                INSERT INTO " . $this->handler_table_group . " SET instance_id = :instance_id, group_id = :group_id
                ";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam('group_id', $this->group_id);
            } else {
                // var_dump("Group id not found and creating in the person table");
                $query = "
                    INSERT INTO " . $this->handler_table_person . " SET instance_id = :instance_id, step_handleby = :step_handleby
                ";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam('step_handleby', $this->step_handleby_id);
            }

            $stmt->bindParam('instance_id', $this->instance_id);
            if ($stmt->execute()) {
                // var_dump("Creating a controller is completed");
                return true;
            } else
                return false;
        } catch (PDOException $e) {
            echo json_encode($e);
            return false;
        }
    }


    /**
     * Visible the instance to the correct person
     * 
     * Visible either to a single person or multiple number of person
     * having same group
     */
    protected function load_single_instance($employee_id)
    {
        try {
            $query = "
            SELECT * FROM " . $this->handler_table_person . " WHERE step_handleby = :employee_id
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam("employee_id", $employee_id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $this->instance_id = $rows['instance_id'];
                    $this->status = $rows['status'];
                    $this->show();
                }
            }
        } catch (PDOException $e) {
            echo json_encode($e);
            return false;
        }
    }

    /**
     * Visible the instance to the correct group
     */
    protected function load_group_instance($group_id)
    {
        try {
            $query = "
            SELECT * FROM " . $this->handler_table_group . " WHERE group_id = :group_id
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam("group_id", $group_id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $this->instance_id = $rows['instance_id'];
                    $this->status = $rows['status'];
                    $this->show();
                }
            }
        } catch (PDOException $e) {
            echo json_encode($e);
            return false;
        }
    }

    private function show()
    {
        $status_code = $this->get_status_code();
        $output = "\nInstance ID: " . $this->instance_id . "\nStatus: " . $status_code . "";
        echo $output;
    }

    /**
     * Get the status code by providing the staus number
     */
    private function get_status_code()
    {
        try {
            $query = "
            SELECT status_name FROM " . $this->status_code_table . " WHERE status_code = :status_code
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam("status_code", $this->status);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['status_name'];
        } catch (PDOException $e) {
            echo json_encode($e);
            return false;
        }
    }


    /**
     * Updating the instance status accepted or rejected by a person or by a group
     */
    protected function update()
    {
        try {
            // var_dump("Ready to update the instance handler");
            $updatedAt = date('Y-m-d H:i:s');

            // var_dump("Is group id found: ", $this->group_id);
            if (is_null($this->group_id)) {
                // var_dump("Group id not found");
                $query = "
                    UPDATE " . $this->handler_table_person . " SET `status`= :status, `remarks`= :remarks, updated_at = :updated_at WHERE instance_id = :instance_id AND step_handleby = :step_handleby AND trace_id = (SELECT MAX(trace_id) FROM " . $this->handler_table_person . " WHERE instance_id = :instance_id AND step_handleby = :step_handleby );
                ";

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam("step_handleby", $this->step_handleby_id);
            } else {
                // var_dump("Group id found");
                $query = "
                    UPDATE " . $this->handler_table_group . " SET `status`= :status, `remarks`= :remarks, handled_by = :handled_by, updated_at = :updated_at WHERE instance_id = :instance_id AND group_id = :group_id AND trace_id = (SELECT MAX(trace_id) FROM " . $this->handler_table_group . " WHERE instance_id = :instance_id AND group_id = :group_id );
                ";

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam("group_id", $this->group_id);
                $stmt->bindParam("handled_by", $this->step_handleby_id);
            }

            $stmt->bindParam("instance_id", $this->instance_id);
            $stmt->bindParam("remarks", $this->remarks);
            $stmt->bindParam("status", $this->status);
            $stmt->bindParam("updated_at", $updatedAt);

            if ($stmt->execute()) {

                // var_dump("Updated number of row: ", $stmt->rowCount());
                if ($stmt->rowCount() == 1) {
                    // var_dump("Updated the instance controller");
                    return true;
                } else
                    return false;
            } else
                return false;
        } catch (PDOException $e) {
            echo json_encode($e);
            return false;
        }
    }

    /**
     * This function will call to check whether a step is already accepted or not
     * If accepted already it can not revert but if not then it can be modify  
     */
    protected function can_update()
    {
        try {
            // var_dump("is group in is accepted ", $this->is_group);
            if (($this->is_group) or isset($this->group_id)) {
                // var_dump("Fetching from group table");
                $query = "
                SELECT status from " . $this->handler_table_group . " WHERE instance_id = :instance_id AND group_id = :group_id AND trace_id = (SELECT MAX(trace_id) FROM " . $this->handler_table_group . " WHERE instance_id = :instance_id AND group_id = :group_id )
                ";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam("group_id", $this->group_id);
            } else {
                // var_dump("Fetching from person table");
                $query = "
                SELECT status from " . $this->handler_table_person . " WHERE instance_id = :instance_id AND step_handleby = :step_handleby AND trace_id = (SELECT MAX(trace_id) FROM " . $this->handler_table_person . " WHERE instance_id = :instance_id AND step_handleby = :step_handleby )
                ";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam("step_handleby", $this->step_handleby_id);
            }
            $stmt->bindParam("instance_id", $this->instance_id);
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (($row['status'] == 0) or $row['status'] == -1)
                    return true;
                else
                    return false;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo json_encode($e);
            return false;
        }
    }

    protected function logs()
    {
        try {
            $query = "
            SELECT trace_id, instance_id, step_handleby, status, remarks, created_at, updated_at FROM " . $this->handler_table_person . " WHERE instance_id = :instance_id UNION ALL SELECT trace_id, instance_id, handled_by AS step_handleby, status, remarks, created_at, updated_at FROM " . $this->handler_table_group . " WHERE instance_id = :instance_id ORDER BY created_at ASC;
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam("instance_id", $this->instance_id);
            if ($stmt->execute()) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $this->response_id = $row['step_handleby'];
                    $this->status = $row['status'];
                    $this->remarks = $row['remarks'];
                    $this->created_at = $row['created_at'];
                    $this->updated_at = $row['updated_at'];

                    $this->get_status_details();
                    $this->show_logs();
                }
            }
        } catch (PDOException $e) {
            echo json_encode($e);
            return false;
        }
    }

    private function get_status_details()
    {
        try {
            $query = "
            SELECT `status_description` FROM `status_code` WHERE `status_code` = :status_code;
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam("status_code", $this->status);
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->status_desc = $row['status_description'];
            }
        } catch (PDOException $e) {
            echo json_encode($e);
            return false;
        }
    }

    private function show_logs()
    {
        $output = "
            \n
            Handling by       : " . $this->response_id . "
            Status            : " . $this->status_desc . "
            Remarks           : " . $this->remarks . "
            Received at       : " . $this->created_at . "
            Handled at        : " . $this->updated_at . "
        ";
        echo $output;
    }
}
