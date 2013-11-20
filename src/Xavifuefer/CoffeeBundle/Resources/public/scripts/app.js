;(function($, application, window) {

    "use strict";

    application.Comm = function ( google ) {
        var self = this
            , imageSearch = new google.search.ImageSearch();

        /**
         * Using Google API this method finds images matching a term sent
         */
        self.find = function( term, container, callback ){
            imageSearch.setSearchCompleteCallback(this, self.drawResults, [container]);
            imageSearch.execute(term);
            callback();
        };

        /**
         * draws Google Custom Search API response
         * Todo: why always just 4 results?
         */
        self.drawResults = function( $container ){
            var results = imageSearch.results;

            if (results && results.length > 0) {
                $container.innerHTML = '';

                // Loop through our results, printing them to the page.
                for (var i = 0; i < results.length; i++) {
                    var result = results[i]
                        , newImgContainer = document.createElement('a')
                        , newImg = document.createElement('img');

                    newImg.src = result.unescapedUrl;
                    newImg.width = newImg.height = 150;

                    newImgContainer.href = '#';
                    newImgContainer.className = 'thumbnail';
                    newImgContainer.appendChild(newImg);

                    $container.append(newImgContainer);
                }
            }
        }

        /**
         * post a form data via AJAX
         */
        self.postForm = function( url, data, callback ){
            var jqXHR = $.post( url, data )
            .done(function( response, textStatus, jqXHR ) {
                var success = true
                    , message = response;
                callback( success, message );
            })
            .fail(function( jqXHR, textStatus, errorThrown ) {
                var success = false
                    , message = errorThrown;
                callback( success, message );
            });
        };

        return self;
    };

    application.Bind = function (app, selector) {
        var $wrapper = $(selector || window.document)
            , $searchForm           = $('#search-form')
            , $searchInput          = $('.-search-input')
            , $searchResultCont     = $('#search-result')
            , $searchResultAction   = $('#search-result-actions')
            , $grinderPanel         = $('.-grinder-content')
            , $addToGrinderBtn      = $('.-add-to-grinder-button')
            , beanPrototype         = $grinderPanel.data('prototype')
            , addToGrinderSel       = '.-add-grinder-form'
            , searchBtnSel          = '.-search-button'
            , useImagesBtnSel       = '.-use-images-button'
            , ActionsGrindSel       = '.-grinder-actions-grind'
        ;

        $wrapper.find($searchInput).tooltip({
            title: "It can't be empty"
        });

        $wrapper
            .on('click', searchBtnSel, function( event ) { // Search button is clicked
                var term = $searchInput.val();

                if( '' == $searchInput.val() ){
                    $searchInput.tooltip('show');
                    $searchForm.addClass('has-error');
                    return;
                }

                $searchInput.tooltip('hide');
                $searchForm.removeClass('has-error');

                app.find(term, $searchResultCont, function(){
                    $searchResultAction.show('slow');
                });
            })
            .on('click', '.thumbnail', function( event ){ // A thumbnail is clicked
                event.preventDefault();

                $(this).toggleClass('selected');
            })
            .on('submit', $searchForm, function( event ){ // All images selected are sent via POST
                event.preventDefault();

                var imagesSelected = $searchResultCont.find(".selected")
                    , $sendButton = $(useImagesBtnSel)
                ;

                if( 0 < imagesSelected.length ){
                    var term = $searchInput.val()
                        , data = $searchForm.serialize();

                    $sendButton.button('loading');

                    // building an array-like query parameter made of all the images selected
                    data += '&urls=[';
                    for ( var i = 0; i < imagesSelected.length; i++ ) {
                        if( i > 0 ) data += ',' ;
                        data += imagesSelected[i].firstElementChild.src;
                    };
                    data += ']';

                    app.postForm( $searchForm.prop('action'), data, function( success, response ) {
                        $sendButton.button('reset');
                        if( true === success && true === response.success ) { window.location = response.url; }
                    });
                } else {
                    $sendButton.tooltip({ title: "You have to select one or more..." });
                }
            })
            .on('submit', addToGrinderSel, function( event ){ // Posts to REDIS the number and images of beans
                event.preventDefault();

                var $form = $(this)
                    , $addButton = $form.find($addToGrinderBtn);

                $addButton.button('loading');

                app.postForm( $form.prop('action'), $form.serialize(), function( success, response ) {
                    $addButton.button('reset');
                    if( true === success && true === response.success ) {
                        var newBean = beanPrototype
                            .replace('[bean_url]', response.bean.url)
                            .replace('[bean_amount]', response.bean.amount)
                        ;
                        $grinderPanel.append(newBean);
                    }
                });
            })
            .on('click', ActionsGrindSel, function(){ // finishes all the process
                window.location = Routing.generate('xavifuefer_coffee_thanks', { 'term': $(this).data('term') });
            })
        ;
    };

})(jQuery, window.app || (window.app = {}), window);