jQuery(document).ready(function ($) {
    'use strict';

    var $table_items = $('.wp-admin #wpcontent .wp-list-table tbody tr');

    if ( $table_items.length > 0 ) {

            $table_items.each(function( index ) {
                $(this).find('.name > strong').after('<span class="close" data-toggler><span class="dashicons dashicons-arrow-right"></span><span class="dashicons dashicons-arrow-down"></span></span>');
            });

            var currentLevel = 0;
            var prevLevel = 0;

            $table_items.each(function( index ) {

                var regex = /level-(\d+?)/i;
                var $classes = $(this).attr('class');
                var match = $classes.match(regex);

                prevLevel = currentLevel;

                if ( match && match[1] ) {
                    currentLevel = parseInt( match[1] );

                    console.log(index, currentLevel, prevLevel);

                    if ( currentLevel < prevLevel ) {

                        prevItemsClear( $(this), 'level-' + prevLevel );

                    }

                    if ( currentLevel === prevLevel ) {
                        $(this).prev().find('[data-toggler]').remove();
                    }
                    

                }

            });

            // if ( $table_items.last().hasClass('level-0') ) {
            //     $table_items.last().find('[data-toggler]').remove();
            // }

            // If last 
            if ( $table_items.last() ) { $table_items.last().find('[data-toggler]').remove(); }

            // Add button 
            $('#posts-filter .bulkactions:first').append('<a class="button button-primary action" data-toggle-all="open">Ouvrir tout / Toggle All</a>');

    }

    function prevItemsClear( $curItem, selector ) {

        var item = $curItem;
        while ( item.prev().hasClass( selector ) ) {
            item = item.prev();
            item.find('[data-toggler]').remove();
        }

    }

    // Toggle all
    $(document).on('click', '[data-toggle-all]', function(e) {
        console.log('toggle all');
        e.preventDefault();
        var action = $(this).data('toggle-all');
        if ( action === 'open' ) {
            $(this).data('toggle-all', 'close' );
            $table_items.each(function( index ) {
                $(this).show().find('[data-toggler]').removeClass('close');
                $(this).prev().removeClass('close');
            });
        } else {
            $(this).data('toggle-all', 'open' );
            $table_items.each(function( index ) {
                $(this).find('[data-toggler]').addClass('close');
                $(this).prev().addClass('close');
                if ( !$(this).hasClass('level-0') ) {
                    $(this).hide();
                }
            });
        }
    });

    $(document).on('click', '.wp-admin #wpcontent .wp-list-table tbody tr [data-toggler]', function(e) {

        e.preventDefault();

        $(this).toggleClass('close');
        var close = $(this).closest('tr').find('[data-toggler]').hasClass('close');

        var regex = /level-(\d+?)/i;
        var $classes = $(this).closest('tr').attr('class');
        var match = $classes.match(regex);

        if ( match && match[1] ) {

            var currentLevel = match[1];
            var nextLevel = parseInt( currentLevel ) + 1;

            var allSiblings = $(this).closest('tr').nextAll();

            allSiblings.each(function (index) {

                var item = $(this).closest('tr');
                if ( item.hasClass( 'level-'+ currentLevel ) ) {
                    return false;
                }

                if ( close ) {
                    item.hide();
                    item.find('[data-toggler]').addClass('close');
                } else if ( item.hasClass( 'level-'+ nextLevel ) ) {
                    item.toggle();
                }

            });

        }

    });

});