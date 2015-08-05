<?php
class geocoder {
		
    static private $url = 'http://maps.google.com/maps/api/geocode/json?sensor=false&address=';
    
    static public function getLocation($address) {
        if ( $lat_lon = self::get_db_lat_lon( $address ) ) {
            return $lat_lon;
        }
        else {
            $url = self::$url.urlencode($address);
            $resp_json = self::curl_get_file_contents($url);
            $resp = json_decode( $resp_json, true );
            if ( $resp['status'] === 'OK' ) {
                $lat = $resp['results'][0]['geometry']['location']['lat'];
                $lon = $resp['results'][0]['geometry']['location']['lng'];
                self::add_db_lat_lon( $address, $lat, $lon );
                return( array( 'lat' => $lat, 'lon' => $lon ) );
            }
            else {
                return false;
            }
        }
    }

    static private function curl_get_file_contents($url) {
        $c = curl_init();
        curl_setopt( $c, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $c, CURLOPT_URL, $url );
        $contents = curl_exec( $c );
        curl_close( $c );
        
        if ( $contents ) {
            return $contents;
        }
        else {
            return FALSE;
        }
    }
    
    static private function get_dbh() {
		require( 'config.php' );
        $dbh = new PDO( "mysql:host=$db_hostname;dbname=$db_database", $db_username, $db_password );
        return $dbh;
    }

	static public function get_address_list($table) {
		$dbh = self::get_dbh();
		$query = "select first_name,last_name,address from $table order by last_name";
		$stmt = $dbh->prepare( $query );
		$stmt->execute();
		$result = $stmt->fetchAll( PDO::FETCH_ASSOC );
		return $result;
	}
    static private function get_db_lat_lon($address) {
        $dbh = self::get_dbh();
        $query = 'select lat,lon from addresses where address=:address limit 1';
        $stmt = $dbh->prepare( $query );
        $stmt->execute( array( ':address' => $address ) );
        if ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
            return $row;
        }
        else {
            return FALSE;
        }           
    }

    static private function add_db_lat_lon($address, $lat, $lon) {
        $dbh = self::get_dbh();
        $query = 'insert into addresses (address,lat,lon) values (?,?,?)';
        $stmt = $dbh->prepare( $query );
        $stmt->execute( array( $address, $lat, $lon ) );
    }
}

# $address = '3280 Frog Crossing Pl, Lenoir, NC';
# $loc = geocoder::getLocation( $address );
# echo '<pre>';
# print_r( $loc );
# echo '</pre>';
?>
