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
            $checkQueryStatement = $this->db->prepare(
                "SELECT * FROM ".$this->userTbl." 
                WHERE oauth_provider = :oauth_provider AND oauth_uid = :oauth_uid LIMIT 1;"
            );

            // Dump params
            $checkQueryStatement->bindParam(":oauth_provider", $data['oauth_provider']);
            $checkQueryStatement->bindParam(":oauth_uid", $data['oauth_uid']);

            $checkQueryStatement->execute();
            $userData = $checkQueryStatement->fetch();

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
                    $colvalSet .= $pre.$key."=". $this->db->quote($val);
                    $i++;
                }

                // Query string
                $queryString = "UPDATE ".$this->userTbl." 
                    SET " . $colvalSet . "
                    WHERE oauth_provider = :oauth_provider AND oauth_uid = :oauth_uid;";

                // Update user data in the database
                // https://www.php.net/manual/fr/pdo.prepare.php
                $updateQueryStatement = $this->db->prepare($queryString);

                // Dump params
                $updateQueryStatement->bindParam(":oauth_provider", $data['oauth_provider']);
                $updateQueryStatement->bindParam(":oauth_uid", $data['oauth_uid']);

                $updateQueryStatement->execute();
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

    /**
     * @return string
     * @TODO: Améliorer l'affichage
     */
    function displayAll()
    {
        // Check whether the user already exists in the database
        $checkQuery = $this->db->prepare(
            'SELECT * FROM ' . $this->userTbl . ';'
        );
        $checkQuery->execute();

        $results = $checkQuery->fetchAll();

        $output = '';
        if ( !sizeof($results) )
            $output = "Aucun user";
        else{
            foreach ($results as $user)
            {
                $output .= print_r($user, true) . "<br>";
            }
        }

        return $output;
    }
}