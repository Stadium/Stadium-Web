<?php
require_once dirname(__FILE__) . "/JSONResponseHandler.php";
require_once dirname(__FILE__) . "/db_connect.php";

/**
* Class that can be used to get and create an individual event
*/
class Event extends JSONResponseHandler
{
    public $db;

    public function __construct() {
        $this->db = DB_CONNECT::connect();
    }

    /** This method is used to retrieve the data for a specific event
     * @param $eventid : the pk of the event bring retrieved
     */
    public function get ($eventid) {

        $stmt = $this->db->prepare("
                    SELECT *
                    FROM stadium_events
                    WHERE eventid = ?;");
        if ($stmt) {

            $params = array($eventid);
            if ($stmt->execute($params)) {

                if ($stmt->rowCount() > 0) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    $response = array (
                        'hostid' => $row['hostid'],
                        'title' => $row['title'],
                        'sport' => $row['sport'],
                        'event_desc' => $row['event_desc'],
                        'location' => $row['location'],
                        'eventid' => $row['eventid'],
                        'skill_level' => $row['skill_level'],
                        'longitude' => $row['longitude'],
                        'latitude' => $row['latitude'],
                        'event_start' => $row['event_start'],
                        'event_end' => $row['event_end'],
                        'privacy' => $row['privacy']
                    );

                    $this->json_response_success("Event successfully retrieved!", $response);

                } else {
                    $this->json_response_error("Error getting event! - No rows returned");
                }

            } else {
                $this->json_response_error("Error getting event - A Database error occurred while executing the stmt!");
            }
        } else {
            $this->json_response_error("PDO stmt could not be created!");
        }

        $stmt->closeCursor();
        unset($this->db);
    }

    /** This method is used to create a new event entry in the database
     * @param $title : title of event
     * @param $sport : sport of event
     * @param $event_desc : description of event
     * @param $location : location of event
     * @param $eventid : id number of event
     * @param $skill_level : skill level of event
     * @param $longitude : longitude of event location
     * @param $latitude : latitude of event location
     * @param $event_start : unix stamp of event start time
     * @param $event_end : unix stamp of event end time
     * @param $privacy : privacy of event
     */
    public function create(
        $hostid,
        $title,
        $sport,
        $event_desc,
        $location,
        $skill_level,
        $longitude,
        $latitude,
        $event_start,
        $event_end,
        $privacy) {

        $stmt = $this->db->prepare("SELECT * FROM stadium_events WHERE eventid = ? AND hostid = ?;");
 
        if ($stmt) {
            $params = array($eventid, $hostid);

            if ($stmt->execute($params)) {

                if($stmt->rowCount() > 0){
                    //If the event already exists then
                    $this->json_response_error("Error occurred, event already exists");
                }
                else{
                    //Event does not exist
                    $stmt = $this->db->prepare("INSERT INTO stadium_events(
                        hostid,
                        title,
                        sport,
                        event_desc,
                        location,
                        skill_level,
                        longitude,
                        latitude,
                        event_start,
                        event_end,
                        privacy
                        )
                        VALUES (?,?,?,?,?,?,?,?,?,?,?);");
                        if($stmt){
                            $params = array($hostid, $title, $sport, $event_desc, $location, $skill_level, $longitude, $latitude, $event_start, $event_end, $privacy);
                            if ($stmt->execute($params)) {
                                $this->json_response_success("Event successfully created!", $response);
                            }
                            else{
                                $this->json_response_error("Entry into stadium_events table could not be made - A Database error occurred!");
                            }
                        }
                        else{
                            $this->json_response_error("PDO stmt could not be created!");
                        }
                }

            } else {
                $this->json_response_error("Cannot access stadium_events table - A Database error occurred while executing the stmt!");
            }
        } else {
            $this->json_response_error("PDO stmt could not be created!");
        }

        $stmt->closeCursor();
        unset($this->db);
    }
}