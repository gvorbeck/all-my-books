/* DOC READY START */
jQuery( document ).ready( function() {
	// This takes the Series Position field out of the Book Data field group and places it next to the Series taxonomy field.
	var seriesNum = jQuery( '#acf-series_position' ).html();
	jQuery( '#acf-series_position' ).detach();
	jQuery( '#tagsdiv-series .inside' ).append( seriesNum );
} );
/* DOC READY STOP */