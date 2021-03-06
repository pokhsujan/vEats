(function ($) {
    "use strict";

    var $body = $('body');

    var ajax_url = golo_admin_vars.ajax_url;

    $('body').on('click', '.open-popup', function(event) {
        event.preventDefault();
        var id = $(this).attr('href');
        $(id).addClass('active');
    });

    $('body').on('click', '.btn-close,.bg-overlay', function() {
        $(this).parents('.golo-popup').removeClass('active');
        return false;
    });

    $('body').on( 'click', '.install-data', function(event) {
    	event.preventDefault();
		ajaxCall();
	});

    $('.golo-countries-settings .remove-all').on('click', function(event) {
        event.preventDefault();
        $(this).parents('.golo-countries-settings').find("input[type='checkbox']").prop("checked", false);
    });

    function purchase_form() {
        $('.hidden-code input').prop('disabled', true);

        $('.purchase-form.verified .purchase-icon').on('click', function() {
            if( $(this).closest('.purchase-form').hasClass('hidden-code') ) {
                $(this).closest('.purchase-form').removeClass('hidden-code');
                $('.purchase-form input').prop('disabled', false);
            }else{
                $(this).closest('.purchase-form').addClass('hidden-code');
                $('.purchase-form input').prop('disabled', true);
            }
            
        });
    }
	
	function timeout_trigger() {
	   $(".progress").css("max-width",p+"%");
	   $(".progress-view").text(p+"%");
	   if(p!=100) {
	       setTimeout('timeout_trigger()', 50);
	   }
	   p++;
	}

    function toggle() {
        const CURRENT_SECTION = 'glf_theme_options_current_section';
        var _current_page = $('#_current_page').val(),
            currentSection = localStorage.getItem(CURRENT_SECTION + '_' + _current_page);
        if (currentSection === null) {
            var sectionActive = $('.glf-tab li:first').data('id');
            currentSection = sectionActive;
            if (typeof (sectionActive) != 'undefined') {
                localStorage.setItem(CURRENT_SECTION + '_' + _current_page, sectionActive);
            }
            else {
                /**
                 * Off reset section if not exist section
                 */
                $('.glf-theme-options-reset-section').remove();
            }
        }
        $('.glf-tab li').removeClass('active');
        $('.glf-fields-wrapper > .glf-section-container').hide();
        $('.glf-tab li[data-id="' + currentSection + '"]').addClass('active');
        $('.glf-fields-wrapper > .glf-section-container[id="' + currentSection + '"]').show();

        /**
         * Store currentSection when section clicked
         */
        $('.glf-tab li').on('click', function () {
            localStorage.setItem(CURRENT_SECTION + '_' + _current_page, $(this).data('id'));
        });
    }

    function save_options() {
        $('.glf-theme-options-save-options').on('click', function() {
            window.onbeforeunload = null;
        });
    }

    function changeWidthContent() {
        var $tab = $('.area-theme-options .glf-tab');
        if ($tab.length > 0) {
            var $wrap = $('.area-theme-options .glf-meta-box-wrap'),
                $fields = $('.area-theme-options .glf-fields'),
                tabWidth = $tab.outerWidth(),
                wrapWidth = $wrap.width();
            $fields.css({
                'float': 'left',
                'width': (wrapWidth - tabWidth) + 'px',
                'overflow': 'visible'
            });
        }
    }

	/**
	 * The main AJAX call, which executes the import process.
	 *
	 * @param FormData data The data to be passed to the AJAX call.
	 */
	function ajaxCall(data) {
		$.ajax({
           	dataType: 'html',
            url: ajax_url,
            data: {
                'action': 'golo_import_demo_data',
            },
            beforeSend: function () {
            	$('.popup-import .progress-bar-container').removeClass('done');
                $('.popup-import .progress-bar-container').addClass('active');
            },
            success: function (response) {
            	$('.popup-import .progress-bar-container').addClass('done');
            	$('.popup-import .progress-bar-container').removeClass('active');
            	$('.result-import').html(response);
            },
        });
    }
    
    function plugin_action() {
        $( '.golo-plugin-action' ).on( 'click', function(e) {
            e.preventDefault();

            var $el = $( e.currentTarget ),
                $pluginsTable = $( '.golo-box--plugins table' ),
                $pluginRow = $el.closest( '.golo-plugin--required' ),
                pluginAction = $el.attr( 'data-plugin-action' ),
                $icon = $pluginRow.find( 'i, .svg-inline--fa' ),
                ajaxData = {
                    'action': 'process_plugin_actions',
                    'slug': $el.attr( 'data-slug' ),
                    'source': $el.attr( 'data-source' ),
                    'plugin_action': $el.attr( 'data-plugin-action' ),
                    '_wpnonce': $el.attr( 'data-nonce' )
                };

            if ( 'deactivate-plugin' === pluginAction ) {
                $el.html( '<i class="las la-circle-notch la-spin"></i>Deactivating' );
            }

            if ( 'activate-plugin' === pluginAction ) {
                $el.html( '<i class="las la-circle-notch la-spin"></i>Activating' );
            }

            $.ajax({
                type: 'POST',
                url: ajax_url,
                data: ajaxData,
                timeout: 20000
            }).done( ( response ) => {
                console.log(response);
                if ( response.success ) {
                    if ( 'deactivate-plugin' === pluginAction ) {
                        $pluginRow.removeClass( 'golo-plugin--activated' ).addClass( 'golo-plugin--deactivated' );
                        $el.text( 'Activate' )
                            .attr( 'data-plugin-action', 'activate-plugin' )
                            .attr( 'data-nonce', response.data )
                            .removeClass( 'plugin-deactivate' )
                            .addClass( 'plugin-activate' );
                        $icon.addClass( 'fa-times' ).removeClass( 'fa-check' );
                    }

                    if ( 'activate-plugin' === pluginAction ) {
                        $pluginRow.removeClass( 'golo-plugin--deactivated' ).addClass( 'golo-plugin--activated' );
                        $el.text( 'Deactivate' )
                            .attr( 'data-plugin-action', 'deactivate-plugin' )
                            .attr( 'data-nonce', response.data )
                            .removeClass( 'plugin-activate' )
                            .addClass( 'plugin-deactivate' );
                        $icon.addClass( 'fa-check' ).removeClass( 'fa-times' );
                    }

                    var requiredPluginCount = $pluginsTable.find( '.golo-plugin--required.golo-plugin--deactivated' ).length,
                        $pluginCount = $( '.golo-box--plugins .golo-box__footer span' );

                    if ( requiredPluginCount ) {
                        $pluginCount.css( 'color', '#dc433f' ).text( 'Please install and activate all required plugins (' + requiredPluginCount + ')' );
                    } else {
                        $pluginCount.css( 'color', '#6fbcae' ).text( 'All required plugins are activated. Now you can import the demo data.' );
                    }
                } else {
                    $el.text( 'Error' );
                }
            });
        });
    }

    function additionalFieldProcess() {
        var $wrapAdditionalField = $('#additional_fields');
        additionalFieldReadOnly();
        additionalFieldEventProcess($wrapAdditionalField);
        $wrapAdditionalField.on('glf_add_clone_field', function (event) {
            var $target = $(event.target);
            additionalFieldEventProcess($target);

            $target.find('.glf-field-panel-content,.glf-clone-field-panel-inner')
                .find('input').removeAttr('readonly');

            $target.find('.glf-field-panel-content,.glf-clone-field-panel-inner')
                .find('#additional_fields_label input,#additional_fields_id input,#additional_fields_select_choices textarea')
                .val('');

            $target.find('.glf-field-panel-content,.glf-clone-field-panel-inner')
                .find('#additional_fields_field_type select').val('text');
        });

        $(document).on('glf_save_option_success', function () {
            additionalFieldReadOnly();
        });
    }

    function additionalFieldReadOnly() {
        $('.glf-field-panel-content,.glf-clone-field-panel-inner').each(function () {
            var $label = $(this).find('#additional_fields_label input'),
                $id = $label.closest('.glf-field-panel-content,.glf-clone-field-panel-inner').find('#additional_fields_id input');
            if ($id.val() !== '') {
                $id.attr('readonly', 'readonly');
            }
        });

    }

    function additionalFieldEventProcess($wrap) {
        $wrap.find('.glf-field-panel-content,.glf-clone-field-panel-inner').find('#additional_fields_label input').on('change', function () {
            var $label = $(this),
                $id = $label.closest('.glf-field-panel-content,.glf-clone-field-panel-inner').find('#additional_fields_id input');
            if ($id.attr('readonly') !== 'readonly') {
                $id.val(toSlug($label.val()));
            }
        });
    }

    function additionalFeaturesProcess() {
        var $wrap = $('#golo_additional_features');

        $wrap.find('button').on('click', function () {
            var count = $wrap.find('tbody tr').length;
            var html = '<tr>\n' +
                '    <td class="sort">\n' +
                '        <span><i class="dashicons dashicons-menu"></i></span>\n' +
                '    </td>\n' +
                '    <td class="title">\n' +
                '        <input type="text" name="golo_additional_feature_title[' + count +  ']" value="">\n' +
                '    </td>\n' +
                '    <td class="value">\n' +
                '        <input type="text" name="golo_additional_feature_value[' + count + ']" value="">\n' +
                '    </td>\n' +
                '    <td class="remove"><i class="dashicons dashicons-dismiss"></i></td>\n' +
                '</tr>';

            $wrap.find('tbody').append(html);
            $wrap.find('.total').val(count + 1);
        });

        $wrap.find('tbody').sortable({
            'items': 'tr',
            handle: '.sort > span',
            update: function( event, ui ) {
                reindexAdditionalFeatures($wrap);
            },
            stop: function (event, ui) {}
        });

        $wrap.on('click', '.remove > i', function () {
            $(this).closest('tr').remove();
            $wrap.find('.total').val($wrap.find('tbody tr').length);
            reindexAdditionalFeatures($wrap);
        });
    }

    function reindexAdditionalFeatures($wrap) {
        $wrap.find(' tbody > tr').each(function (index) {
            $(this).find('input').each(function () {
                var name = $(this).attr('name');
                name = name.replace( /^(\w+\[)(\d+)(\].*)$/g , function(m,p1,p2,p3){ return p1+index+p3; });
                $(this).attr('name', name);
            });
        });
    }

    function toSlug(str) {
        str = String(str).toString();
        str = str.replace(/^\s+|\s+$/g, "");
        str = str.toLowerCase();

        var swaps = {
            '0': ['??', '???', '??', '???'],
            '1': ['??', '???', '??', '???'],
            '2': ['??', '???', '??', '???'],
            '3': ['??', '???', '??', '???'],
            '4': ['???', '???', '??', '??', '???'],
            '5': ['???', '???', '??', '??', '???'],
            '6': ['???', '???', '??', '??', '???'],
            '7': ['???', '???', '??', '???'],
            '8': ['???', '???', '??', '???'],
            '9': ['???', '???', '??', '???'],
            'a': ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '??', '???', '???', '???', '???', '???', '???', '???', '??', '??', '???', '???', '???', '??', '??', '??', '???', '???', '??', '???', '??'],
            'b': ['??', '??', '??', '???', '???', '???'],
            'c': ['??', '??', '??', '??', '??', '???'],
            'd': ['??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '??', '??', '??', '??', '???', '???', '???', '???'],
            'e': ['??', '??', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '??', '??', '???'],
            'f': ['??', '??', '??', '??', '???', '???'],
            'g': ['??', '??', '??', '??', '??', '??', '??', '???', '???', '??', '???'],
            'h': ['??', '??', '??', '??', '??', '??', '???', '???', '???', '???'],
            'i': ['??', '??', '???', '??', '???', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '???', '???', '??', '???', '???', '???', '??', '???', '???', '??', '??', '??', '???', '???', '???', '??????', '??', '???', '???', '??', '???'],
            'j': ['??', '??', '??', '???', '??', '???'],
            'k': ['??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '??', '???'],
            'l': ['??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???'],
            'm': ['??', '??', '??', '???', '???', '???'],
            'n': ['??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???'],
            'o': ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??????', '??', '??', '??', '???', '???', '???', '??'],
            'p': ['??', '??', '???', '???', '??', '???'],
            'q': ['???', '???'],
            'r': ['??', '??', '??', '??', '??', '??', '???', '???'],
            's': ['??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '??', '???', '???'],
            't': ['??', '??', '??', '??', '??', '??', '??', '???', '???', '??', '???', '???', '???'],
            'u': ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '??', '??', '??', '??', '??', '???', '???', '???', '??', '??'],
            'v': ['??', '???', '??', '???'],
            'w': ['??', '??', '??', '???', '???', '???'],
            'x': ['??', '??', '???'],
            'y': ['??', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???'],
            'z': ['??', '??', '??', '??', '??', '??', '???', '???', '???'],
            'aa': ['??', '???', '??'],
            'ae': ['??', '??'],
            'ai': ['???'],
            'ch': ['??', '???', '???', '??'],
            'dj': ['??', '??'],
            'dz': ['??', '???'],
            'ei': ['???'],
            'gh': ['??', '???'],
            'ii': ['???'],
            'ij': ['??'],
            'kh': ['??', '??', '???'],
            'lj': ['??'],
            'nj': ['??'],
            'oe': ['??', '??', '??'],
            'oi': ['???'],
            'oii': ['???'],
            'ps': ['??'],
            'sh': ['??', '???', '??'],
            'shch': ['??'],
            'ss': ['??'],
            'sx': ['??'],
            'th': ['??', '??', '??', '??', '??'],
            'ts': ['??', '???', '???'],
            'ue': ['??'],
            'uu': ['???'],
            'ya': ['??'],
            'yu': ['??'],
            'zh': ['??', '???', '??'],
            '(c)': ['??'],
            'A': ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '??', '???', '??', '??', '??', '???', '??'],
            'B': ['??', '??', '???', '???'],
            'C': ['??', '??', '??', '??', '??', '???'],
            'D': ['??', '??', '??', '??', '??', '??', '???', '???', '??', '??', '???'],
            'E': ['??', '??', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '??', '???', '??', '??', '??', '??', '??', '???'],
            'F': ['??', '??', '???'],
            'G': ['??', '??', '??', '??', '??', '??', '???'],
            'H': ['??', '??', '??', '???'],
            'I': ['??', '??', '???', '??', '???', '??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '???'],
            'J': ['???'],
            'K': ['??', '??', '???'],
            'L': ['??', '??', '??', '??', '??', '??', '??', '???', '???'],
            'M': ['??', '??', '???'],
            'N': ['??', '??', '??', '??', '??', '??', '??', '???'],
            'O': ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '???', '??'],
            'P': ['??', '??', '???'],
            'Q': ['???'],
            'R': ['??', '??', '??', '??', '??', '???'],
            'S': ['??', '??', '??', '??', '??', '??', '??', '???'],
            'T': ['??', '??', '??', '??', '??', '??', '???'],
            'U': ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '??', '??'],
            'V': ['??', '???'],
            'W': ['??', '??', '??', '???'],
            'X': ['??', '??', '???'],
            'Y': ['??', '???', '???', '???', '???', '??', '???', '???', '???', '??', '??', '??', '??', '??', '??', '???'],
            'Z': ['??', '??', '??', '??', '??', '???'],
            'AE': ['??', '??'],
            'Ch': ['??'],
            'Dj': ['??'],
            'Dz': ['??'],
            'Gx': ['??'],
            'Hx': ['??'],
            'Ij': ['??'],
            'Jx': ['??'],
            'Kh': ['??'],
            'Lj': ['??'],
            'Nj': ['??'],
            'Oe': ['??'],
            'Ps': ['??'],
            'Sh': ['??'],
            'Shch': ['??'],
            'Ss': ['???'],
            'Th': ['??'],
            'Ts': ['??'],
            'Ya': ['??'],
            'Yu': ['??'],
            'Zh': ['??']
        };
        Object.keys(swaps).forEach(function (swap) {
            swaps[swap].forEach(function (s) {
                str = str.replace(new RegExp(s, "g"), swap);
            });
        });
        return str.replace(/[^a-z0-9 -]/g, "").replace(/\s+/g, "-").replace(/-+/g, "-").replace(/^-+/, "").replace(/-+$/, "");
    }

    var css_class_wrap = '.golo-place-select-meta-box-wrap';
    var golo_get_city_by_country = function () {
        var $this = $(".golo-place-country-ajax", css_class_wrap);
        var $place_city = $(".golo-place-city-ajax", css_class_wrap);
        var $is_slug = $place_city.attr('data-slug');
        if (typeof($is_slug) === 'undefined') {
            $is_slug = '1';
        }
        if ($this.length && $place_city.length) {
            var selected_country = $this.val();
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: {
                    'action': 'golo_get_city_by_country_ajax',
                    'country': selected_country,
                    'type': 0,
                    'is_slug': $is_slug
                },
                beforeSend: function () {
                    $place_city.parent().children('.golo-loading').remove();
                    $place_city.parent().append('<span class="golo-loading"><i class="las la-circle-notch la-spin golo-loading__icon"></i></span>');
                },
                success: function (response) {
                    $place_city.html(response);
                    var val_selected = $place_city.attr('data-selected');
                    if (typeof val_selected !== 'undefined') {
                        $place_city.val(val_selected);
                    }
                    $place_city.parent().children('.golo-loading').remove();
                },
                error: function () {
                    $place_city.parent().children('.golo-loading').remove();
                },
                complete: function () {
                    $place_city.parent().children('.golo-loading').remove();
                }
            });
        }
    };

    var golo_get_neighborhoods_by_city = function () {
        var $this = $(".golo-place-city-ajax", css_class_wrap);
        var $place_neighborhood = $(".golo-place-neighborhood-ajax", css_class_wrap);
        var $is_slug = $place_neighborhood.attr('data-slug');
        if (typeof($is_slug) === 'undefined') {
            $is_slug='1';
        }
        if ($this.length && $place_neighborhood.length) {
            var selected_city = $this.val();
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: {
                    'action': 'golo_get_neighborhoods_by_city_ajax',
                    'city': selected_city,
                    'type': 0,
                    'is_slug': $is_slug
                },
                beforeSend: function () {
                    $place_neighborhood.parent().children('.golo-loading').remove();
                    $place_neighborhood.parent().append('<span class="golo-loading"><i class="las la-circle-notch la-spin golo-loading__icon"></i></span>');
                },
                success: function (response) {
                    $place_neighborhood.html(response);
                    var val_selected = $place_neighborhood.attr('data-selected');
                    if (typeof val_selected !== 'undefined') {
                        $place_neighborhood.val(val_selected);
                    }
                    $place_neighborhood.parent().children('.golo-loading').remove();
                },
                error: function () {
                    $place_neighborhood.parent().children('.golo-loading').remove();
                },
                complete: function () {
                    $place_neighborhood.parent().children('.golo-loading').remove();
                }
            });
        }
    };

    $(document).ready(function() {

        additionalFieldProcess();

        purchase_form();

        toggle();

        save_options();

        plugin_action();

        setTimeout(function() {
            changeWidthContent();
        }, 100);

        var css_class_wrap = '.golo-place-select-meta-box-wrap';

        golo_get_city_by_country();
        $(".golo-place-country-ajax", css_class_wrap).on('change', function () {
            golo_get_city_by_country();
        });

        golo_get_neighborhoods_by_city();
        $(".golo-place-city-ajax", css_class_wrap).on('change', function () {
            golo_get_neighborhoods_by_city();
        });

    });

})(jQuery);