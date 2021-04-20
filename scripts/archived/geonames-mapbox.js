// infobox div=info_box
function infobox( longitude, latitude, level ) {
    jQuery.get('https://dt-mapping-builder/geocode-info-box.php', { type: 'info', longitude: longitude, latitude: latitude, level: level }, null, 'html' ).done(function(data) {
        jQuery('#info_box').empty().append( '<br>' + data )
    })
}

