<?php
/*
 * User Class
 */

class User
{
    private $dsn        = DATABASE_URL;
    private $userTbl    = DB_USER_TBL;
    private $db         = null;

    function __construct()
    {
        if(!isset($this->db))
        {
            // Connect to the database
            try {
                $this->db = new PDO($this->dsn);
            }catch (PDOException $e){
                die ('DB Error');
            }
        }
    }

    function checkUser($data = array()): bool
    {
        if(!empty($data))
        {
            // Check whether the user already exists in the database
            $checkQuery = $this->db->prepare(
                "SELECT * FROM ".$this->userTbl." 
                WHERE oauth_provider = :oauth_provider AND oauth_uid = :oauth_uid;"
            );

            $checkResult = $checkQuery->execute(
                [
                    ':oauth_provider'   => $data['oauth_provider'],
                    ':oauth_uid'               => $data['oauth_uid']
                ]);

            // Add modified time to the data array
            if(!array_key_exists('modified',$data))
            {
                $data['modified'] = date("Y-m-d H:i:s");
            }

            if($checkResult->num_rows > 0)
            {
                // Prepare column and value format
                $colvalSet = '';
                $i = 0;
                foreach($data as $key=>$val)
                {
                    $pre = ($i > 0)?', ':'';
                    $colvalSet .= $pre.$key."='".$this->db->real_escape_string($val)."'";
                    $i++;
                }
                $whereSql = " WHERE oauth_provider = '".$data['oauth_provider']."' AND oauth_uid = '".$data['oauth_uid']."'";

                // Update user data in the database
                $query = "UPDATE ".$this->userTbl." SET ".$colvalSet.$whereSql;
                $update = $this->db->query($query);
            }else{
                // Add created time to the data array
                if(!array_key_exists('created',$data))
                {
                    $data['created'] = date("Y-m-d H:i:s");
                }

                // Prepare column and value format
                $columns = $values = '';
                $i = 0;
                foreach($data as $key=>$val)
                {
                    $pre = ($i > 0)?', ':'';
                    $columns .= $pre.$key;
                    $values  .= $pre."'". real $this->db->real_escape_string($val)."'";
                    $i++;
                }

                // Insert user data in the database
                $query = "INSERT INTO ".$this->userTbl." (".$columns.") VALUES (".$values.")";
                $insert = $this->db->query($query);
            }

            // Get user data from the database
            $result = $this->db->query($checkQuery);
            $userData = $result->fetch_assoc();
        }

        // Return user data
        return !empty($userData)?$userData:false;
    }
}