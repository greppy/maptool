/*
The MIT License (MIT)

Copyright (c) 2015 Matt Okeson-Harlow, grephead.com, LLC

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

// change lat and lng to correspond with the desired center of the map
var setup = { "lat" : "35.909683", "lng" : "-81.445363", "zoom" : "9" };

var map;
var infoWnd;
function initialize() {

    var mapProp = {
        center: new google.maps.LatLng(setup['lat'], setup['lng']),
        zoom: Number(setup['zoom']),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    map = new google.maps.Map(document.getElementById('googleMap'), mapProp);
    infoWnd = new google.maps.InfoWindow();
    // var bounds = new google.maps.LatLngBounds();
    
    var last_name_lookup = {};

    $.getJSON( "tables.json", function( table_data ) {
        $.each( table_data, function( i, tables ) {
            $.getJSON( "json.php?table=" + tables['table'], function( data ) {
                $.each( data, function( i, entry ) {
                    var latlng = new google.maps.LatLng( entry['lat'], entry['lon'] );
                    // bounds.extend(latlng);
                    var marker = createMarker(
                        map, latlng, entry['address'], entry['name'], tables['icon']
                    );
                    last_name_lookup[entry['name']] = entry['last_name'];
                    createMarkerButton(marker);
                });
            });
        });
    });
    
    // map.fitBounds(bounds);

    function createMarker( map, latlng, address, title, icon ) {
        var marker = new google.maps.Marker({
            position: latlng,
            map: map,
            icon: icon,
            title: title,
            content: address,
        });

        google.maps.event.addListener(marker, "click", function() {
            infoWnd.setContent("<img src='" + icon + "'><strong>" + title + "</strong><br />" + address );
            infoWnd.open(map,marker);
        });
        return marker;
    }

    function marker_list_lookup(last_name) {
        var patterns = [ "abc", "def", "ghi", "jkl", "mno", "pqr", "stu", "vwx", "yz" ];
        for ( var i = 0; i < patterns.length; i++ ) {
            var regex = "^[" + patterns[i] + "]";
            var re = new RegExp(regex, "i");
            if ( re.test(last_name) ) {
                var string = patterns[i] + "_marker_list";
                return string;
            }
        }
    }

    function createMarkerButton(marker){
        var title = marker.getTitle();
        var icon = marker.getIcon();
        var last_name = last_name_lookup[title];
        var marker_list = marker_list_lookup(last_name);
        var ul = document.getElementById(marker_list);
        var li = document.createElement("li");
        li.innerHTML = "<img src='" + icon + "'>" + title;
        li.className = 'list-group-item';
        ul.appendChild(li);

        google.maps.event.addDomListener(li, "click", function() {
            google.maps.event.trigger(marker, "click");
        });
    }
}
google.maps.event.addDomListener(window, 'load', initialize );
