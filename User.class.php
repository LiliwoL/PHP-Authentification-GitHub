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
            }catch (PDOException $e)
            {
                die ('DB Error');
            }
        }
    }

    /**
     * Vérifie si l'utilisateur existe
     * Si oui, il est mis à jour
     * Sinon, il est créé
     *
     * @param $data
     * @return bool
     */
    function checkUser($data = array())
    {
        if(!empty($data))
        {
            // Check whether the user already exists in the database
            $checkQuery = $this->db->prepare(
                "SELECT * FROM ".$this->userTbl." 
                WHERE oauth_provider = :oauth_provider AND oauth_uid = :oauth_uid LIMIT 1;"
            );
            $checkQuery->execute(
                [
                    ':oauth_provider'          => $data['oauth_provider'],
                    ':oauth_uid'               => $data['oauth_uid']
                ]);
            $userData = $checkQuery->fetch();

            // Add modified time to the data array
            if(!array_key_exists('modified',$data))
            {
                $data['modified'] = date("Y-m-d H:i:s");
            }

            if( $userData  )
            {
                // Prepare column and value format
                $colvalSet = '';
                $i = 0;
                foreach($data as $key=>$val)
                {
                    $pre = ($i > 0)?', ':'';
                    $colvalSet .= $pre.$key."='".mysqli_escape_string($val)."'";
                    $i++;
                }

                // Update user data in the database
                $updateQuery = $this->db->prepare(
                    "UPDATE ".$this->userTbl." 
                    SET " . $colvalSet . "
                    WHERE oauth_provider = :oauth_provider AND oauth_uid = :oauth_uid;"
                );
                $updateQuery->execute(
                    [
                        'oauth_provider'          => $data['oauth_provider'],
                        'oauth_uid'               => $data['oauth_uid']
                    ]);
            }
            else
            {
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
                    $values  .= $pre."'". $val."'";
                    $i++;
                }

                // Insert user data in the database
                $this->db->query(
                    "INSERT INTO ".$this->userTbl." 
                    (" . $columns . ")
                    VALUES (" . $values . ");"
                );
            }
        }

        // Return user data
        return !empty($userData)?$userData:false;
    }

    function displayAll()
    {
        // Check whether the user already exists in the database
        $checkQuery = $this->db->prepare(
            "SELECT * FROM ".$this->userTbl.";"
        );
        $checkQuery->execute([]);

        $output = '';
        foreach ($checkQuery->fetch() as $key=>$value)
        {
            $output .= $key . " --> " . $value . "<br>";
        }
        return $output;
    }
}