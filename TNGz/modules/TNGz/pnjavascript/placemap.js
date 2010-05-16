/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * @package TNGz
 * @url http://code.zikula.org/tngz
 * @license http://www.gnu.org/copyleft/gpl.html
 *
 * @author Wendel Voigt
 * @version $Id$
 */
 
 // This file contains common javascript routines to help display TNG place information with Google Maps
 
// Initialzie map variables
function InitializeMap() {
    placemap.MarkerList           = [];    // list of the place markers
    placemap.MarkerTextList       = [];    // list of the html for the pop-up for each marker
    placemap.MarkerNameList       = [];    // list of the html for the side bar for each marker
    placemap.MarkerPlaceLevelList = [];    // list of the level for each place marker
    placemap.PlaceCount           = 0;     // Number of places in the list
    placemap.ShowPins             = true;  // Toggle switch for all markers
    placemap.map                  = null;  // Placeholder for map
    placemap.cluster              = null;  // Placeholder for cluster information
    
    if(typeof(placemap.cmspath)              == "undefined"){ placemap.cmspath              = 'genealogy/'; }
    if(typeof(placemap.placesearch_url)      == "undefined"){ placemap.placesearch_url      = 'index.php?module=TNGz&func=main&show=placesearch&'; }
    if(typeof(placemap.id_sidebar)           == "undefined"){ placemap.id_sidebar           = 'side_bar'; }
    if(typeof(placemap.id_sidebar_li_prefix) == "undefined"){ placemap.id_sidebar_li_prefix = 'placeID_'; }
    if(typeof(placemap.id_placelevel_prefix) == "undefined"){ placemap.id_placelevel_prefix = 'placelevelID_'; }
    if(typeof(placemap.pin_dir)              == "undefined"){ placemap.pin_dir              = 'googlemaps/'; }
    if(typeof(placemap.pinplacelevelfile)    == "undefined"){ placemap.pinplacelevelfile    = []; }
    if(typeof(placemap.center_lat)           == "undefined"){ placemap.center_lat           = 0; }
    if(typeof(placemap.center_lng)           == "undefined"){ placemap.center_lng           = 0; }
    if(typeof(placemap.center_zoom)          == "undefined"){ placemap.center_lat           = 1; }

    placemap.PlaceLevels    = [1,2,3,4,5,6,0]; // Valid Place levels
    placemap.PlaceLevelShow = [];              // Display flag for each Place level
    placemap.baseIcon = [];                    // Icon for each Place Level
      
    for ( var i in placemap.PlaceLevels ) {        // Initialize
        placemap.PlaceLevelShow[i] = true;
        placemap.baseIcon[i] = new GIcon();
        placemap.baseIcon[i].image  = placemap.cmspath + placemap.pin_dir + placemap.pinplacelevelfile[i];
        placemap.baseIcon[i].shadow = placemap.cmspath + placemap.pin_dir + 'shadow50.png';
        placemap.baseIcon[i].iconSize = new GSize(20, 34);
        placemap.baseIcon[i].shadowSize = new GSize(37, 34);
        placemap.baseIcon[i].iconAnchor = new GPoint(9, 34);
        placemap.baseIcon[i].infoWindowAnchor = new GPoint(9, 2);
        placemap.baseIcon[i].infoShadowAnchor = new GPoint(18, 25);
    }
    
    window.onload=LoadMap;
    window.onunload=GUnload;
}

// Create and load the Map
function LoadMap() {
   // if ($("map")) {
        placemap.map = new GMap2(document.getElementById("map"));
        placemap.map.addControl(new GLargeMapControl());
        placemap.map.addControl(new GMapTypeControl());
        placemap.map.setCenter(new GLatLng( placemap.center_lat,placemap.center_lng), placemap.center_zoom);
        placemap.cluster = new ClusterMarker(placemap.map);
        placemap.cluster.clusteringEnabled=placemap.usecluster;
        showhide(null, true );
    //}
}

// Opens Pop-up for pin 
function myclick(i) {
    placemap.cluster.triggerClick(i);
}

// Update the markers and sidebar taking into acount what is enabled or not
function myMapRefresh() {
    var NewMarkerList = [];  // This is the updated marker list
    var NewSideBar    = "";
    var NewCount      = 0;

    for (var y=0; y<placemap.PlaceCount; y++) {
        if (placemap.PlaceLevelShow[placemap.MarkerPlaceLevelList[y]]) {
            // Show marker
            NewMarkerList[NewCount]=placemap.MarkerList[y];

            NewSideBar += '<li id="' + placemap.id_sidebar_li_prefix + NewCount + '">';
            NewSideBar += '<a href="javascript:myclick(' + NewCount + ')">' + placemap.MarkerNameList[y] + '</a>';
            NewSideBar += '</li>';

            NewCount++;
        }
    }

    // Update the sidebar information
    document.getElementById( placemap.id_sidebar ).innerHTML = '<ul class="placemap-scrollbar-list" >'+NewSideBar+'</ul>';

    // Refresh Markers
    placemap.cluster.removeMarkers();
    if (NewCount > 0) {
        placemap.cluster.addMarkers(NewMarkerList);
    }
    placemap.cluster.refresh(true);
}

// A function to create the marker, save the right information, and set up the event window
function createMarker(lat,lng,name,placelevel,tree) {
    var point  = new GLatLng(lat,lng);
    var marker = new GMarker(point, placemap.baseIcon[placelevel]);
    var i      = placemap.PlaceCount;
        
    placemap.MarkerList[i]           = marker;
    placemap.MarkerTextList[i]       = WindowText(lat, lng, name, tree);
    placemap.MarkerNameList[i]       = name;
    placemap.MarkerPlaceLevelList[i] = placelevel;

    GEvent.addListener(placemap.MarkerList[i], "click", function() {
        marker.openInfoWindowHtml(placemap.MarkerTextList[i]);
    });

    placemap.PlaceCount++;

}

// A function to create the text for a pop-up event window
function WindowText(lat, lng, name, tree){
    var url   = placemap.placesearch_url + 'tree=' + tree + '&psearch='+ encodeURI(name);
    var title = '<a href="'+ url + '" target="blank">' + name + '</a>';
    var txt   = '<div style="text-align:center;" >';
        txt  += '<strong>' + title + '<\/strong><br \/>';
        txt  += '<\/div>';
        txt  += '<div style="text-align:center; white-space:nowrap;"><br \/>';
        txt  += '<a title="Zoom in" ';
        txt  += 'href="javascript:void(0)" onclick="myZoom(' + lat + ',' + lng + ', 2)">';
        txt  += '<strong>Zoom In<\/strong><\/a>';
        txt  += '&nbsp;|&nbsp;';
        txt  += '<a title="Zoom Out" ';
        txt  += 'href="javascript:void(0)" onclick="myZoom(' + lat + ',' + lng + ', -2)">';
        txt  += '<strong>Zoom Out<\/strong><\/a>';
        txt  += '<\/div>';
        return txt;
}


// Function to Zoom map in or out
function myZoom(lat, lng, level) {
    lat   = (typeof lat   == 'undefined') ? 0 : lat;
    lng   = (typeof lng   == 'undefined') ? 0 : lng;
    level = (typeof level == 'undefined') ? 1 : level;

    var NewZoom = placemap.map.getZoom() + level;

    NewZoom = (NewZoom <  0) ?  0 : NewZoom;
    NewZoom = (NewZoom > 15) ? 19 : NewZoom;

    placemap.map.setCenter(new GLatLng(lat, lng), NewZoom);
}


// Function to show, hide, or toggle the display of pins based upon their place level
function showhide(placelevel, show ) {
  placelevel = ( typeof placelevel == 'undefined' ) ? null : placelevel ;
  show       = ( typeof show       == 'undefined' ) ? null : show ;

  placemap.map.closeInfoWindow(); // just in case one is open
      
  if (show !== true && show !== false) {
     // if not already set to true or false, set to the oposite of what it is now (toggle)
     if ( placelevel != null ) {
         show = document.getElementById( placemap.id_placelevel_prefix + placelevel).checked;
         placemap.PlaceLevelShow[placelevel] = show;
     } else { // All pins
         show = !placemap.ShowPins;
         for ( var i in placemap.PlaceLevels ) {
             placemap.PlaceLevelShow[i]=show;
         }
     }
  }
  var checkedvalue = (show) ? true : false ;

  // Set the legend check boxes
  if (placelevel == null) {
      // All the markers
      for ( var i in placemap.PlaceLevels ) {
          if (liElement = document.getElementById( placemap.id_placelevel_prefix + i)) {
              liElement.checked = checkedvalue;
          }
      }
      placemap.ShowPins = show; // Also, record what is showing for next toggle
  } else { 
      // Just one marker
      if (liElement = document.getElementById( placemap.id_placelevel_prefix + placelevel)) {
          liElement.checked = checkedvalue;
      }
      if (checkedvalue) {
          // If one Place level is shown, then checkmark the "all" and record state
          placemap.ShowPins = true;
          if (liElement = document.getElementById( placemap.id_placelevel_prefix + 'all')) {
              liElement.checked = true;
          }
      } else {
         // Check to see if all of the Place Levels are unchecked,
         var AllUnchecked = true;
         for ( var i in placemap.PlaceLevels ) {
             if (placemap.PlaceLevelShow[i]){
                 AllUnchecked = false;
             }
         }
         // if so, then uncheck the "all" and record state
         if (AllUnchecked) {
             placemap.ShowPins = false;
             if (liElement = document.getElementById( placemap.id_placelevel_prefix + 'all')) {
                 liElement.checked = false;
             }
         }
      }
  }
  myMapRefresh();
}

function toggleClustering() {
    placemap.cluster.clusteringEnabled=!placemap.cluster.clusteringEnabled;
    placemap.cluster.refresh(true);
}
