<?php namespace myClasses;

class Tabler{

    protected $echoString;
    protected $objVcoeOci;

    public function __construct(){
    
    }

    /**
     * Die Methode erzeugt aus den Daten eines Vcoeoci-Objekts eine HTML-Tabelle (mit Header und Footer)
     * Gib das HTLM in Form eines Strings zurück.
     *
     * @param Vcoeoci $objData
     * @return String
     */
    public function doTable(Vcoeoci $objData) : String{

        $this->objVcoeOci = $objData;

        $echoString = "<table id=\"" . $this->objVcoeOci->id . "\" class=\"" . $this->objVcoeOci->class . "\" cellspacing=\"0\" width=\"100%\">\n";
        $echoString .= "<thead>\n";
        $echoString .= "<tr>\n";
        foreach($this->objVcoeOci->fieldnames As $fieldname){
            $echoString .= "<th>$fieldname</th>";
        }
        $echoString .= "</tr>\n";
        $echoString .= "</thead>\n";
        $echoString .= "<tfoot>\n";
        $echoString .= "<tr>\n";
        foreach($this->objVcoeOci->fieldnames As $fieldname){
            $echoString .= "<th>$fieldname</th>";
        }    
        $echoString .= "</tr>\n";
        $echoString .= "</tfoot>\n";   
        $echoString .= "<tbody>\n";
        
            $fieldcount = oci_num_fields($this->objVcoeOci->statement);
            while (($row = oci_fetch_array($this->objVcoeOci->statement, OCI_NUM + OCI_RETURN_NULLS )) != false) {  

            $echoString .= "<tr class=\"\">\n";
            $pk = $row[0];
            $route = $row[5];
            $katbez = $row[1];

            for ($i = 0; $i <= $fieldcount - 1; $i++) {
                if($row[$i] !== null){
                    $link = htmlentities($row[$i], ENT_QUOTES);
                }else{
                    $link = "-";
                }
            
                $echoString .= "<td class=\"\">" . "<a class=\"tablink\" id=\"reiheid$pk\" href=\"" . $route . "?id=$pk&katbez=$katbez\">" . $link . "</a>" . "</td>\n";

            }
            $echoString .= "</tr>\n";
        }

        $echoString .= "</tbody>\n";
        $echoString .= "</table>\n";

        return $echoString;
    }

    
    /**
     * Erzeugt auf Basis eines Vcoeoci-Objekts eine schlichte Form einer Tabelle zurück..
     *
     * @param Vcoeoci $objData
     * @return String
     */
    public function doSimpleTable(Vcoeoci $objData) : String{
        
                $this->objVcoeOci = $objData;
        
                $echoString = "<table id=\"\" class=\"" . $this->objVcoeOci->class . "\" cellspacing=\"0\" width=\"100%\">\n";
                $echoString .= "<thead>\n";
                $echoString .= "<tr>\n";
                foreach($this->objVcoeOci->fieldnames As $fieldname){
                    $echoString .= "<th>$fieldname</th>";
                }
                $echoString .= "</tr>\n";
                $echoString .= "</thead>\n";
                $echoString .= "<tbody>\n";
        
                while (($row = oci_fetch_array($this->objVcoeOci->statement, OCI_NUM /*OCI_BOTH  OCI_ASSOC+OCI_RETURN_NULLS*/ )) != false) {
                    $echoString .= "<tr>\n";
                    $pk = $row[0];
                    foreach ($row as $item) {
                        $link = ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;");
                        $echoString .= "<td class=\"\">" . $link . "</td>\n";
                    }
                    $echoString .= "</tr>\n";
                }
                $echoString .= "</tbody>\n";
                $echoString .= "</table>\n";
        
                return $echoString;
            }   
    
/**
 * Erzeugt eine Tabelle auf Basis eines übergebenen Arrays...
 * Name der CSS-Klasse der zu erzeugenden Tabelle wird mit übergeben
 * Die Klasse des <td> entspricht dem Inhalt des jeweils ersten Feldes
 *
 * @param array $array - Das Array repräsentiert die Tabelle (ohne header)
 * @param string $class - Die Klasse der Tabelle, sowie auch der tbody-tr
 * @param string $headers - Eine Komma-separierte Liste mit allen Spaltenköpfen
 * @return void
 */
    public function doTableFromArray(array $array, string $class, string $headers) : string {

                $echoString = "<table id=\"\" class=\"$class\" cellspacing=\"0\" width=\"100%\">\n";
                $echoString .= "<thead>\n";
                $echoString .= "<tr>\n";

                //Anzahl der Spalten ermitteln (array in array)
                $nrCols = 0;
                foreach($array as $innerarray){
                    if(count($innerarray)>$nrCols){
                        $nrCols = count($innerarray); 
                    }
                }

                //for ($i = 0; $i <= $nrCols-1; $i++) {
                //    $echoString .= "<th></th>";
                //}

                $heads = explode(',', $headers);
                foreach($heads AS $value){
                $echoString .= "<th>$value</th>";
                }
                //Entf.-Spalte
                $echoString .= "<th>Entf.</th>";

                $echoString .= "</tr>\n";
                $echoString .= "</thead>\n";

                $echoString .= "<tfoot>\n";
                $echoString .= "<tr>\n";

                for ($i = 0; $i <= $nrCols-1; $i++) {
                    $echoString .= "<th></th>";
                }
                //Entf.-Spalte
                $echoString .=  "<th></th>";  

                $echoString .= "</tr>\n";
                $echoString .= "</tfoot>\n";   

                $echoString .= "<tbody>\n";

                foreach($array As $key => $value){
                    $tdClass = reset($value);

                    $echoString .= "<tr id=\"$key\" class=\"$class\">\n";
                    foreach($value As $item){
                        $echoString .= "<td class=\"$tdClass\">" . $item . "</td>\n";
                    }
                    //EntfernenSpalte:
                    $echoString .= "<td class=\"$tdClass\"><a href=\"\">Entfernen</a></td>\n";

                    $echoString .= "</tr>\n";
                }

                $echoString .= "</tbody>\n";
                $echoString .= "</table>\n";
        
                return $echoString;


    }

}


?>