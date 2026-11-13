/* global jQuery, hikashopSiteOrigin */
( function ( $ ) {
	'use strict';

	var cfg = hikashopSiteOrigin;

	function request( data ) {
		return $.getJSON( cfg.ajaxUrl, $.extend( { action: 'hikashop_so_products', nonce: cfg.nonce }, data ) );
	}

	function ids( picker ) {
		var value = picker.find( '.hikashop-so-picker-value' ).val() || '';
		return value.split( ',' ).filter( function ( id ) {
			return /^\d+$/.test( id );
		} );
	}

	function save( picker ) {
		var list = picker.find( '.hikashop-so-picker-list li' ).map( function () {
			return this.getAttribute( 'data-id' );
		} ).get();
		// Widget screens save and preview on change
		picker.find( '.hikashop-so-picker-value' ).val( list.join( ',' ) ).trigger( 'change' );
	}

	function item( id, text ) {
		var li = $( '<li>' ).attr( 'data-id', id );
		li.append( $( '<span>' ).text( text ) );
		li.append( $( '<button type="button" class="hikashop-so-up">' ).attr( 'aria-label', cfg.up ).text( '↑' ) );
		li.append( $( '<button type="button" class="hikashop-so-down">' ).attr( 'aria-label', cfg.down ).text( '↓' ) );
		li.append( $( '<button type="button" class="hikashop-so-remove">' ).attr( 'aria-label', cfg.remove ).text( '×' ) );
		return li;
	}

	function initPicker( picker ) {
		picker.addClass( 'is-ready' );
		var chosen = ids( picker );
		if ( ! chosen.length ) {
			return;
		}
		// The saved products need their names
		request( { ids: chosen.join( ',' ) } ).done( function ( response ) {
			if ( ! response || ! response.success ) {
				return;
			}
			var list = picker.find( '.hikashop-so-picker-list' ).empty();
			response.data.forEach( function ( product ) {
				list.append( item( product.id, product.text ) );
			} );
		} );
	}

	function search( picker ) {
		var results = picker.find( '.hikashop-so-picker-results' );
		var term = picker.find( '.hikashop-so-picker-search' ).val();
		request( { q: term } ).done( function ( response ) {
			results.empty();
			var chosen = ids( picker );
			var found = response && response.success ? response.data : [];
			found.forEach( function ( product ) {
				if ( chosen.indexOf( product.id ) === -1 ) {
					results.append( $( '<li tabindex="0">' ).attr( 'data-id', product.id ).text( product.text ) );
				}
			} );
			if ( ! results.children().length ) {
				results.append( $( '<li>' ).text( cfg.none ) );
			}
		} );
	}

	// Fields shown for some values of another field only
	function toggle( form ) {
		form.find( '[data-show-field]' ).each( function () {
			var row = $( this );
			var source = form.find( '[data-field="' + row.attr( 'data-show-field' ) + '"] select' );
			var values = String( row.attr( 'data-show-values' ) ).split( ',' );
			row.toggle( ! source.length || values.indexOf( source.val() ) !== -1 );
		} );
	}

	function setup( root ) {
		$( root ).find( '.hikashop-so-form' ).addBack( '.hikashop-so-form' ).each( function () {
			var form = $( this );
			toggle( form );
			form.find( '.hikashop-so-picker:not(.is-ready)' ).each( function () {
				initPicker( $( this ) );
			} );
		} );
	}

	var timer = null;
	$( document )
		.on( 'change', '.hikashop-so-form select', function () {
			toggle( $( this ).closest( '.hikashop-so-form' ) );
		} )
		.on( 'focus', '.hikashop-so-picker-search', function () {
			search( $( this ).closest( '.hikashop-so-picker' ) );
		} )
		.on( 'input', '.hikashop-so-picker-search', function () {
			var picker = $( this ).closest( '.hikashop-so-picker' );
			clearTimeout( timer );
			timer = setTimeout( function () {
				search( picker );
			}, 250 );
		} )
		.on( 'keydown', '.hikashop-so-picker-search', function ( e ) {
			// Enter would submit the widget form
			if ( 'Enter' === e.key ) {
				e.preventDefault();
			}
		} )
		.on( 'click keydown', '.hikashop-so-picker-results li[data-id]', function ( e ) {
			if ( 'keydown' === e.type && 'Enter' !== e.key ) {
				return;
			}
			e.preventDefault();
			var picker = $( this ).closest( '.hikashop-so-picker' );
			picker.find( '.hikashop-so-picker-list' ).append( item( this.getAttribute( 'data-id' ), $( this ).text() ) );
			$( this ).remove();
			save( picker );
		} )
		.on( 'click', '.hikashop-so-picker-list button', function () {
			var li = $( this ).closest( 'li' );
			var picker = li.closest( '.hikashop-so-picker' );
			if ( $( this ).hasClass( 'hikashop-so-remove' ) ) {
				li.remove();
			} else if ( $( this ).hasClass( 'hikashop-so-up' ) ) {
				li.prev().before( li );
			} else {
				li.next().after( li );
			}
			save( picker );
		} )
		// SiteOrigin's widget dialog, then WordPress's widget screens
		.on( 'panelsopen', function ( e ) {
			setup( e.target );
		} )
		.on( 'widget-added widget-updated', function ( e, widget ) {
			setup( widget );
		} );

	$( function () {
		setup( document.body );
	} );
}( jQuery ) );
