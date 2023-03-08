<?php
/*
 * User Class
 */
class User
{
    private $dsn        = DATABASE_URL;
    private $userTbl    = DB_USER_TBL;
    private $db         = null;

    /**
     *
     */
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
     * @param array $data
     * @return bool
     */
    function checkUser(array $data = array())
    {
        if(!empty($data))
        {
            // ******
            // Check whether the user already exists in the database
            // ******
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

            // If user exists
            if( $userData )
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
                if( !array_key_exists('created', $data) )
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

                // Query string
                $queryString = "INSERT INTO ".$this->userTbl." 
                    (" . $columns . ")
                    VALUES (" . $values . ");";

                // Insert user data in the database
                // https://www.php.net/manual/fr/pdo.prepare.php
                $insertQueryStatement = $this->db->prepare($queryString);

                $insertQueryStatement->execute();

                // die($insertQueryStatement->debugDumpParams());
                // Récupération du dernier enregistrement pour renvoi ultérieur
                $lastInsertId = $this->db->lastInsertId();

                $userData = $this->findOne( $lastInsertId );
            }
        }

        // Return user data
        return !empty($userData)?$userData:false;
    }

    /**
     * @param int $id
     * @return array
     */
    public function findOne( int $id ): array
    {
        // ******
        // Find One user by its id
        // ******
        $findUserQueryStatement = $this->db->prepare(
            "SELECT * FROM ".$this->userTbl." 
                WHERE id = :id LIMIT 1;"
        );

        // Dump params
        $findUserQueryStatement->bindParam(":id", $id);

        if ( $findUserQueryStatement->execute() )
        {
            $output = $findUserQueryStatement->fetch();

            if (!$output){
                $output = array();
            }
        }        
        else{
            $output = array();
        }
        return $output;
    }

    /**
     * @return bool
     */
    public function purge(): bool
    {
        // ******
        // Purge query
        // ******
        $purgeQueryStatement = $this->db->prepare(
            "DELETE FROM users WHERE 1;"
        );

        return $purgeQueryStatement->execute();
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

        $output = "<br><br>";
        if ( !sizeof($results) )
        {
            # Base de données vide
            $output .= "<h1>Aucun utilisateur en base pour le moment</h1>";
        }
        else{
            $output .= "<h1>Liste des utilisateurs en base</h1>";

            # Formatage pour chaque ligne d'utilisateur
            $format = 'ID: %d &emsp;&emsp; | &emsp;&emsp; OAuthProvider: %s &emsp;&emsp; | &emsp;&emsp; OAuthUid: %d &emsp;&emsp; |  &emsp;&emsp; Name: %s &emsp;&emsp; |  &emsp;&emsp; Username: %s &emsp;&emsp; |  &emsp;&emsp; Created: %s';

            foreach ($results as $user)
            {
                $output .= sprintf( $format, $user['id'], $user['oauth_provider'], $user['oauth_uid'], $user['name'], $user['username'], $user['created']);
                $output .= "<br>";

                // Affichage brut
                //$output .= print_r($user, true) . "<br>";
            }

            $output .= '<a href="index.php?purge=true">Purge de la base</a>';
        }

        return $output;
    }
}