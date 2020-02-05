<?php
    class Consultas{

        public function conn() {          

            $dbhost = "localhost";
            $dbname = "prueba_soap";        
            $dbuser = "root";
            $dbpass = "";   

            if($conn = new PDO("mysql:host=".$dbhost.";dbname=".$dbname, $dbuser, $dbpass,array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'))){
                return($conn);
             }
             else {
                return null;
            }
        }

        public function select($table, $rows = '*', $join = null, $where = null, $order = null, $limit = null){
            $conn = $this->conn();
            $sql = 'SELECT '.$rows.' FROM '.$table;
            if($join != null){
                $sql .= ' JOIN '.$join;
            }
            if($where != null){
                $sql .= ' WHERE '.$where;
            }
            if($order != null){
                $sql .= ' ORDER BY '.$order;
            }
            if($limit != null){
                $sql .= ' LIMIT '.$limit;
            }
            
            $query = $conn->prepare($sql);
            $query->execute(); 
            return $query->fetchAll();
        }

        public function consultarFormatos($date){
            echo json_encode($this->select('extension_archivos','count(id) AS total, nombre_extension, fecha_consulta','','fecha_consulta="'.$date.'" GROUP BY nombre_extension','',''));
        }

        public function consultarDocumentos($date){
            $conn = $this->conn();
            $result = $this->select('fecha_consulta','*','','fecha="'.$date.'"','','');

            if(empty($result)){
                $documentos = $this->consultarAPI($date);

                $this->guardarDocumentos($documentos, $date);
            }

            $sql = $conn->prepare("SELECT * FROM archivos_obtenidos AS ao INNER JOIN extension_archivos AS ea ON ao.id_archivo=ea.id_archivo WHERE ao.fecha_consulta='".$date."'");
            $sql->execute();
            echo json_encode($sql->fetchAll());
        }

        public function guardarDocumentos($documentos, $date){
            $conn = $this->conn();

            $sql = $conn->prepare("INSERT INTO fecha_consulta VALUES(NULL,'".$date."')");
            $sql->execute();

            foreach($documentos["Archivo"] as $key => $value){
                $id = $value['@attributes']['Id'];
                $nombre = $value['@attributes']['Nombre'];

                $datos = explode(".",$nombre);
                $extension = end($datos);

                $nombre = $datos[0];

                $sql = $conn->prepare("INSERT INTO archivos_obtenidos VALUES(NULL,'".$id."','".$nombre."','".$date."')");
                $sql->execute();

                $sql = $conn->prepare("INSERT INTO extension_archivos VALUES(NULL,'".$id."','".$extension."','".$date."')");
                $sql->execute();
            }
        }

        public function consultarAPI($date){

            $curl = curl_init();

            curl_setopt_array($curl, array(
            
            CURLOPT_URL => "http://test.analitica.com.co/AZDigital_Pruebas/WebServices/SOAP/index.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>'
                <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
                xmlns:xsds="http://www.analitica.com.co/AZDigital/xsds/">
                    <soapenv:Header/>
                    <soapenv:Body>
                        <xsds:BuscarArchivo>    
                            <Condiciones>               
                                <Condicion Tipo="FechaInicial" Expresion="'.$date.' 00:00:00"/>          
                            </Condiciones>   
                        </xsds:BuscarArchivo>  
                    </soapenv:Body>
                </soapenv:Envelope>',
            CURLOPT_HTTPHEADER => array("Content-Type: text/xml"),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

           $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response );
            $xml = new SimpleXMLElement($response);
            $body = $xml->xpath('//soapEnvelope')[0];
            
            $array = json_decode(json_encode((array)$body), TRUE); 
            $array = $array['soapBody']['azRtaBuscarArchivo'];
            
            return $array;
        }
    }

?>