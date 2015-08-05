<?php

# The MIT License (MIT)
#
# Copyright (c) 2015 Matt Okeson-Harlow, grephead.com, LLC
# 
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
# 
# The above copyright notice and this permission notice shall be included in
# all copies or substantial portions of the Software.
# 
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
# THE SOFTWARE.

require 'geocoder.php';

if ( $_REQUEST['table'] === 'employee' or $_REQUEST['table'] === 'clients' ) {
    $addr_array = geocoder::get_address_list( $_REQUEST['table'] );

    $output = array();
    foreach ( $addr_array as $entry ) {
        $loc = geocoder::getLocation( $entry['address'] );
        $name = sprintf( "%s %s", $entry['first_name'], $entry['last_name'] );
        $output[] = array(
            'lat'       => $loc['lat'],
            'lon'       => $loc['lon'],
            'address'   => $entry['address'],
            'name'      => $name,
            'last_name' => $entry['last_name']
        );
    }
    $json = json_encode( $output );
    echo $json;
}
?>
