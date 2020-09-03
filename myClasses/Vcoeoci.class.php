<?PHP namespace myClasses;

/**
 * Ist für die Kommunikation mit der Datenbank zuständig, nimmt meist SQL-Statements entgegen und leitet sie 
 * and die Datenbank weiter.
 */
class Vcoeoci{

    public $conn;
    // public $route;
    // public $query;
    // public $statement;
    // public $ncols;
    // public $fieldnames;
    // public $id;
    // public $class;
    // public $arrQuery;
    // public $nrows;

    
    /**
     * Im Konstruktor wird nach einer passenden Datenbankverbindung Ausschau gehalten und bei Bedarf eine solche erzeugt.
     */
    public function __construct(){

        try{
            //$this->conn = oci_connect('vcoe', 'vcoe', '192.168.1.29:1523/vcoe2014.vcoe.local');
            //Anstatt wie in der Zeile oberhalb eine Datenbankverbindung bei jedem abzusetzenden SQL-Statement zu
            //erzeugen, wird die statische Methode der Connection-Klasse aufgerufen, die das Singleton-Pattern implementiert
            //hat...
            require_once('ConnectionMysql.class.php');
            $this->conn = \myClasses\ConnectionMysql::getInstance();
            

        }
        catch(Exception $e)
        {
            echo 'Fehler! - Datenbankverbindung konnte nicht hergestellt werden '; 
            echo "Fehler",  $e->getMessage();
        }
        
    }  

    Public Function ArrayFromDB(string $query) : array {

        $arr = [];

        foreach ($this->conn->query($query) as $row) {
        
            $marker = [
                $row['lat'],
                $row['lon'], 
                $row['title'],
                $row['body'],
                $row['filepath'],
                $row['notification_type'],
                $row['plz'],
                $row['terms_of_use'],
                $row['entryid']
            ];

        $arr[] = $marker;

        }

        return $arr;

    }

    Public Function ScalarFromDB(string $query) : int {

        foreach ($this->conn->query($query) as $row) {
        
            $scalar = $row[0];

        }

        return $scalar;

    }

    Public Function execute(string $query) : int {
        
        $statement = $this->conn->prepare($query);
        $statement->execute();

        $recAffs = $statement->rowCount();

        return $recAffs;

    }
}


?>