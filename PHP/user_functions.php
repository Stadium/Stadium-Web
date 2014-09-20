<?php
require_once dirname(__FILE__) . '/JSONResponseHandler.php';
require_once dirname(__FILE__) . '/db_connect.php';

/**
 * Class User can be used to retrieve and create individual users
 */
class User extends JSONResponseHandler {

    public $db;

    public function __construct() {
        $this->db = DB_CONNECT::connect();
    }

    /** Used to fetch a specified user
     * @param $facebookid
     */
    public function get ($facebookid) {

        $stmt = $this->db->prepare("SELECT * FROM stadium_users WHERE facebookid = ?;");
        if ($stmt) {

            if ($stmt->execute(array($facebookid))) {

                if ($stmt->rowCount() > 0) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    $response['user'] = array (
                        'userid' => $row['userid'],
                        'facebookid' => $row['facebookid'],
                        'email' => $row['email'],
                        'preferred_name' => $row['preferred_name'],
                        'status' => $row['status']
                    );

                    $this->json_response_success("User successfully retrieved!", $response);

                } else {
                    $this->json_response_error("Error getting user - User does not exist!");
                }

            } else {
                $this->json_response_error("A database error occured, could not get user!");
            }
        } else {
            $this->json_response_error("PDO stmt could not be created!");
        }

        $stmt->closeCursor();
        unset($this->db);
    }

    /** Used to sign in a user or create one if it does not already exist in the database
     * @param $facebookid
     * @param $email
     * @param $preferred_name
     * @param $status
     */
    public function create ($facebookid, $email, $preferred_name, $status) {

        //See if the user already exists in the database
        $stmt = $this->db->prepare("SELECT * FROM stadium_users WHERE facebookid = ?;");
        if ($stmt) {

            $params = array($facebookid);
            if ($stmt->execute($params)) {

                if ($stmt->rowCount() == 1) {
                    //If the user exists, sign in
                    $this->json_response_success("User successfully signed in!");

                } else {
                    //If the user does not exist, take the data and create a new user
                    $stmt = $this->db->prepare("
                            INSERT INTO stadium_users (facebookid, email, preferred_name, status)
                            VALUES (?, ?, ?, ?);");
                    $params = array ($facebookid, $email, $preferred_name, $status);

                    if ($stmt->execute($params)) {
                        $this->json_response_success("User successfully created!");
                    } else {
                        $this->json_response_error("Error occurred creating a user");
                    }
                }

            } else {
                $this->json_response_error("Error creating user - A Database error occurred!");
            }
        } else {
            $this->json_response_error("PDO stmt could not be created!");
        }

        $stmt->closeCursor();
        unset($this->db);
    }

    public function join ($eventid, $userid){
        $stmt = $this->db->prepare("SELECT * FROM stadium_attendees WHERE eventid = ? AND userid = ?;");
        if($stmt){
            $params = array($eventid, $userid);
            if($stmt->execute($params)){
                if($stmt->rowCount() > 0){
                    //user is already attending that event
                    $this->json_response_error("User is already going to that event");
                }
                else{
                    //user is not part of that event
                    $stmt = $this->db->prepare("INSERT INTO stadium_attendees(eventid, userid) VALUES(?,?);");
                    if($stmt){
                        if($stmt->execute($params)){
                            $this->json_response_success("User is attending that event");
                        }
                        else{
                            $this->json_response_error("Error adding user to that event - A database error has occured");
                        }
                    }
                    else{
                        $this->json_response_error("Error creating stmt");
                    }
                }

            }
            else{
                $this->json_response_error("Error adding user to that event - A database error has occured");
            }
        }
        else{
            $this->json_response_error("Error creating stmt");
        }
        $stmt->closeCursor();
        unset($this->db);
    }

}