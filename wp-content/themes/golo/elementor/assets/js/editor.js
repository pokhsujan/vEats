(
	function( $ ) {
		'use strict';

		elementor.channels.editor.on( 'section:activated', function( sectionName, editor ) {
			var editedElement = editor.getOption( 'editedElementView' );

			if ( sectionName == null ) {
				return;
			}

			var widgetType = editedElement.model.get( 'widgetType' );

			// Flipped true site on edit.
			if ( 'golo-flip-box' === widgetType ) {
				var isBackSection = false;

				if ( - 1 !== sectionName.indexOf( 'back_side_section' ) || - 1 !== sectionName.indexOf( 'button_style_section' ) ) {
					isBackSection = true;
				}

				editedElement.$el.toggleClass( 'golo-flip-box--flipped', isBackSection );

				var $backLayer = editedElement.$el.find( '.back-side' );

				if ( isBackSection ) {
					$backLayer.css( 'transition', 'none' );
				}

				if ( ! isBackSection ) {
					setTimeout( function() {
						$backLayer.css( 'transition', '' );
					}, 10 );
				}
			}

			// Edit heading wrapper style.
			if ( 'golo-heading' === widgetType && 'wrapper_style_section' === sectionName ) {
				editedElement.$el.addClass( 'golo-heading-wrapper-editing' );
			} else {
				editedElement.$el.removeClass( 'golo-heading-wrapper-editing' );
			}

			// Force show arrows when editing arrows of any widgets has swiper.
			if ( 'swiper_arrows_style_section' === sectionName ) {
				editedElement.$el.addClass( 'golo-swiper-arrows-editing' );
			} else {
				editedElement.$el.removeClass( 'golo-swiper-arrows-editing' );
			}

			// Force show marker overlay when editing.
			if ( 'markers_popup_style_section' === sectionName ) {
				editedElement.$el.addClass( 'golo-map-marker-overlay-editing' );
			} else {
				editedElement.$el.removeClass( 'golo-map-marker-overlay-editing' );
			}
		} );
	}
)( jQuery );
