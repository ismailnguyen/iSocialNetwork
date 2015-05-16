<?php
// --- Dummy file ---
//
// @Author: Ismaïl

    public function create($user)
    {

    }

    public function update($user)
    {
        try
        {
            if($user && ($user instanceof User))
            {
                $params = array(":firstname" => $firstname,
                                ":lastname" => $lastname,
                                ":email" => $email,
                                ":birtdhdate" => $birtdhdate,
                                ":id_user" => $id_user
                                );

                $statement = $m_db->prepare("UPDATE User SET firstname=:firstname, lastname=:lastname, email=:email, birthdate=:birthdate WHERE id_user=:id_user");

                if($statement && $statement->execute($params))
                {
                    unset($m_db);
                    return true;
                }
            }
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return false;
        }
    }

    public function delete($id_user)
    {
        try
        {
            if($id_user)
            {
                $statement = $m_db->prepare("DELETE FROM User WHERE id_user=:id_user");

                if($statement && $statement->execute(array(":id_user" => $id_user)))
                    return true;
            }
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return false;
        }
    }

    public function listMostRent()
    {
        try
        {
            $listMostRent = array();

            $statement = $m_db->query("SELECT * FROM User WHERE id_user = (SELECT id_user FROM Loan GROUP BY id_user ORDER BY COUNT(id_user) DESC LIMIT 1)");

            while($row = $statement->fetch(PDO::FETCH_ASSOC))
            {
                $listMostRent[] = ($row['id_user'] => array("firstname" => $row['firstname'],
                                   "lastname" => $row['lastname'],
                                   "email" => $row['email'],
                                   "birthdate" => $row['birthdate'],
                                   "created_date" => $row['created_date'])
                                    );
            }

            return $listMostRent;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return null;
        }
    }
}
