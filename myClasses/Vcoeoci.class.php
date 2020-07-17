<?PHP namespace myClasses;

/**
 * Ist für die Kommunikation mit der Datenbank zuständig, nimmt meist SQL-Statements entgegen und leitet sie 
 * and die Datenbank weiter.
 */
class Vcoeoci{

    public $conn;
    public $route;
    public $query;
    public $statement;
    public $ncols;
    public $fieldnames;
    public $id;
    public $class;
    public $arrQuery;
    public $nrows;

    
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
    
    public function route(string $route){
        $this->route = $route;
    }

    /**
     * Erzeugt aus den Ergebnissen einer SQL-Abfrage ($strQuery) ein Array und gibt dieses zurück... 
     *
     * @param [type] $strQuery
     * @return Array
     */
    public function doArray($strQuery) : Array{
        $this->query = $strQuery;
        $this->statement = oci_parse($this->conn, $this->query);
        oci_execute($this->statement);
        $this->nrows = oci_fetch_all($this->statement, $results);
        return $results;
    }

    /**
     * Methode bewirkt die Ausführung einer Oracle PL/SQL-Procedure, die wiederum einen Tabellenvergleich
     * zweier Tabellen durchführt und die Ergebnisse dieses Vergleichs wiederum in Form einer Tabelle bereitstellt,
     * auf die zu einem späteren Zeitpunkt zugegriffen werden kann.
     *
     * @param [type] $table1
     * @param [type] $table2
     * @param [type] $option
     * @return void
     */
    public function setTableComparison($table1, $table2, $option){
        
        switch ($option) {
            case 1:
            $this->query = "BEGIN SP_TABLE_COMPARE_TABLE(:strTab1, :strTab2); END;";
            break;
            case 2:
            $this->query = "BEGIN SP_TABLE_COMPARE_TABLE_TYPE(:strTab1, :strTab2); END;";
            break;
            case 3:
            $this->query = "BEGIN SP_TABLE_COMPARE_TABLE_LENGTH(:strTab1, :strTab2); END;";
            break;
        }

        $this->statement = oci_parse ($this->conn, $this->query);
        
        oci_bind_by_name($this->statement, ":strTab1", $table1);
        oci_bind_by_name($this->statement, ":strTab2", $table2);
        oci_execute ($this->statement);
        
    }

    /**
     * Methode ermittelt die Feld-Zuweisungen, die aufgrund von Gleichheit der Felder (Name, Datentyp, Feldgröße) automatisch
     * aufeinander bezogen werden können, und gibt diese als Array zurück.
     *
     * @param [type] $par_option
     * @return array
     */
    public function getAutoassignFields($par_option) : array {

    //$par_option entspricht dem Verleichstyp (1...nur Feldname vergleichen, 2..Feldname und Datentyp vergleichen, 3..Feldname, Datentype und Feldgröße vergleichen)    
        if($par_option == 1 || $par_option == 2 || $par_option ==3){
            $option = $par_option;
        }else{
            $option = 2; //default
        }
        
        $query2 = "SELECT COLUMN_NAME FROM TABLECOMPARE_OPTION$option WHERE VERGLEICH = 'UN'";

        $this->query = $query2;
        $this->statement = oci_parse($this->conn, $this->query);
        oci_execute($this->statement);
        $autoassignments = [];
        $assgncount = 0;
        while (($row = oci_fetch_array($this->statement, /*OCI_NUM  OCI_BOTH */ OCI_ASSOC+OCI_RETURN_NULLS)) != false) { 
        $assgncount += 1;
            foreach($row AS $field => $value){
                $autoassignments[$assgncount][$field] = $value; 
            }

        }

        return $autoassignments;

    }

    /**
     * Ermittelt ob eine Abfrage Ergebniszeilen liefert oder nicht...
     *
     * @param string $query
     * @return boolean
     */
    public function anyResultsForThisQuery(string $query) : bool{

        $this->query = $query;
        $this->statement = oci_parse($this->conn, $this->query);
        oci_execute($this->statement);
        $count = 0;
        while(($row = oci_fetch_array($this->statement, OCI_ASSOC+OCI_RETURN_NULLS)) != false){
            $count += 1;
        }

        if($count>0){
            return true;
        }else{
            return false;
        }

    }

    /**
     * Erwartet einen SQL-String einer Auswahlabfrage (SELECT) und gibt das Ergebnis als array zurück...
     *
     * @param string $query
     * @return array
     */
    public function fetchArrayFromQuery(string $query) : array{

        $this->query = $query;
        $this->statement = oci_parse($this->conn, $this->query);
        oci_execute($this->statement);
        return oci_fetch_array($this->statement, OCI_ASSOC+OCI_RETURN_NULLS);

    }

    /**
     * DML-SQL-Statements werden mit dieser Methode an die Datenbank weitergereicht...
     *
     * @param [type] $query
     * @return void
     */
    public function dmlQuery($query){

        $this->query = $query;
        $this->statement = oci_parse($this->conn, $this->query);
        oci_execute($this->statement);
    }

    /**
     * Erzeugt eine HTML-Tabelle auf Basis eines SQL-Strings einer Auswahlabfrage (SELECT)...
     * Die Methode bedient sich der Klasse Tabler (? Objektabhängikeit, die vermieden werden könnte ?)
     *
     * @param [type] $query
     * @param string $class
     * @return void
     */
    public function doTable($query, string $id, string $class){
        
        $this->query = $query;
        
        $this->class = $class;
    
        $this->id = $id;

        $this->statement = oci_parse ($this->conn, $this->query);

        oci_execute ($this->statement);
        
        $this->ncols = oci_num_fields($this->statement);
        
        for ($i = 1; $i <= $this->ncols; $i++) {
            $this->fieldnames[] = oci_field_name ($this->statement , $i);
        }
        
        //an dieser Stelle sollte die Klasse Tabler übernehmen...
        require_once('Tabler.class.php');
        $tblr = New \myClasses\Tabler;
        echo $tblr->doTable($this);
        
    }

    /**
     * Erstellt eine einfache Tabelle bzw. einen HTML-String (ohne Footer) auf Basis eines SQL-Strings einer Auswahlabfrage (SELECT), 
     * wobei allfällige Abfrageparameter (bind parameters) per assoziativem Array übergeben werden können.
     * Die Methode bedient sich der Klasse Tabler (? Objektabhängikeit, die vermieden werden könnte ?)
     *
     * @param [type] $query
     * @param string $class
     * @param array $params
     * @return void
     */
    public function doSimpleTableParams($query, string $class, array $params){

        $this->query = $query;
        
        $this->class = $class;

        $this->statement = oci_parse ($this->conn, $this->query);

        //Parameter auswerten...
        Foreach($params As $key => $value){
            //oci_bind_by_name($this->statement, ":strTab1", $table1);
            oci_bind_by_name($this->statement, $key, $value);
        }

        oci_execute ($this->statement);
        
        $this->ncols = oci_num_fields($this->statement);
        
        for ($i = 1; $i <= $this->ncols; $i++) {
            $this->fieldnames[] = oci_field_name ($this->statement , $i);
        }
        
        //an dieser Stelle sollte die Klasse Tabler übernehmen...
        require_once('Tabler.class.php');
        $tblr = New \myClasses\Tabler;
        echo $tblr->doSimpleTable($this);

    }

    Public Function ArrayFromDB(string $query) : array {

        $arr = [];

        foreach ($this->conn->query($query) as $row) {
        
            $marker = [
                $row['lat'],
                $row['lon'], 
                $row['title'],
                $row['body'],
                $row['filepath']
            ];

        $arr[] = $marker;

        }

        return $arr;

    }

    Public Function execute(string $query) : int {
        
        $insert = $this->conn->prepare($query);
        $insert->execute();

        $recAffs = $insert->rowCount();

        return $recAffs;

    }
}


?>