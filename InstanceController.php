<?php

include_once __DIR__ . "/connection.php";

class InstanceController
{
    private $conn;

    private $trace_table;
    private $trace_table_group;
    private $status_code_table;

    private $instance_id;
    private $step_handleby_id;
    private $group_id = null;
    private $is_group = false;
    private $acknowledgement;
    private $status;
    private $created_at;
    private $updated_at;

    public function set_instance_id($id)
    {
        $this->instance_id = $id;
    }

    public function set_group_id($id)
    {
        $this->group_id = $id;
    }

    public function set_status($code)
    {
        $this->status = $code;
    }

    public function set_acknowledgement($ack)
    {
        $this->acknowledgement = $ack;
    }

    public function set_handleby_id($id)
    {
        $this->step_handleby_id = $id;
    }

    public function __construct()
    {
        $this->conn = connect_db();

        $data = json_decode(file_get_contents(__DIR__ . '/config.json'), TRUE);
        $this->trace_table = $data['trace_instance'];
        $this->trace_table_group = $data['trace_instance_group'];
        $this->status_code_table = $data['status_code'];
    }

    public function __destruct()
    {
        $this->acknowledgement = "Pending";
        $this->status = '0';
    }

    public function set_values($instance_id, $handleby_id, $group = false, $status = 0)
    {
        $this->instance_id = $instance_id;
        if ($group == false) {
            $this->step_handleby_id = $handleby_id;
        } else {
            $this->group_id = $handleby_id;
            $this->is_group = true;
        }
        $this->status = $status;

        var_dump("after setting the values");
        var_dump($this->instance_id = $instance_id);
        var_dump($this->step_handleby_id);
        var_dump($this->group_id);
        var_dump($this->status = $status);
    }


    /**
     * Functionality to create instance for user
     * Differentiate between single person or a group in role
     */
    protected function create()
    {
        try {
            var_dump("Creating new instance controller");
            var_dump("is  group id available: ", $this->group_id);

            var_dump("is it a group ", $this->is_group);

            if ($this->is_group) {
                var_dump("group id available so we are here");
                $query = "
                INSERT INTO " . $this->trace_table_group . " SET instance_id = :instance_id, group_id = :group_id
                ";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam('group_id', $this->group_id);
            } else {
                var_dump("group id not available so we are here");
                $query = "
                    INSERT INTO " . $this->trace_table . " SET instance_id = :instance_id, step_handleby = :step_handleby
                ";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam('step_handleby', $this->step_handleby_id);
            }

            $stmt->bindParam('instance_id', $this->instance_id);
            // $stmt->bindParam('status', $this->status);
            if ($stmt->execute()) {
                var_dump("completed");
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
     */
    protected function load_instance_by_person($employee_id)
    {
        try {
            $query = "
            SELECT * FROM " . $this->trace_table . " WHERE step_handleby = :employee_id
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
    protected function load_instance_by_group($group_id)
    {
        try {
            $query = "
            SELECT * FROM " . $this->trace_table_group . " WHERE group_id = :group_id
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
    public function update()
    {
        try {
            var_dump("updating the status");
            $updatedAt = date('Y-m-d H:i:s');

            var_dump("is group id available: ", $this->group_id);
            if (is_null($this->group_id)) {
                var_dump("Group id not available so we are here");
                $query = "
                        UPDATE " . $this->trace_table . " SET status = :status, acknowledgement = :acknowledgement, updated_at = :updated_at WHERE instance_id = :instance_id AND step_handleby = :step_handleby
                    ";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam("step_handleby", $this->step_handleby_id);
            } else {
                var_dump("Group id available so we are here");
                $query = "
                        UPDATE " . $this->trace_table_group . " SET status = :status, acknowledgement = :acknowledgement, handled_by = :handled_by, updated_at = :updated_at WHERE instance_id = :instance_id AND group_id = :group_id
                    ";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam("group_id", $this->group_id);
                $stmt->bindParam("handled_by", $this->step_handleby_id);
            }

            $stmt->bindParam("instance_id", $this->instance_id);
            $stmt->bindParam("acknowledgement", $this->acknowledgement);
            $stmt->bindParam("status", $this->status);
            $stmt->bindParam("updated_at", $updatedAt);

            if ($stmt->execute()) {
                var_dump("updated instance");
                return true;
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
    public function is_accepted()
    {
        try {
            var_dump("is group in is accepted ", $this->is_group);
            if (($this->is_group) and isset($this->group_id)) {
                $query = "
                SELECT status from " . $this->trace_table_group . " WHERE instance_id = :instance_id AND group_id = :group_id 
                ";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam("group_id", $this->group_id);
            } else {
                $query = "
                    SELECT status from " . $this->trace_table . " WHERE instance_id = :instance_id AND step_handleby = :step_handleby 
                ";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam("step_handleby", $this->step_handleby_id);
            }
            $stmt->bindParam("instance_id", $this->instance_id);
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row['status'] == 1)
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
}
