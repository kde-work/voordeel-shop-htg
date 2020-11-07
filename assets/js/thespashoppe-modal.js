(function ($) {
    if (!$) {
        console.error('jQuery and $ are missing');
        return;
    }

    /**
     * Form water testing Modal Class.
     */
    function SpaModals() {
        this.$body = $('body');

        this.events_init();
    }

    /**
     * Events init.
     */
    SpaModals.prototype.events_init = function() {
        let _this = this;

        // Close Modals
        this.$body.on('click', '.wt-modal__close', function (e) {
            _this.close_modals();
        });
    };


    /**
     * Open modal.
     *
     * @param  modal_class
     */
    SpaModals.prototype.open_modal = function(modal_class) {
        let $modal = $('.wt-modal--' + modal_class);

        if ($modal.length) {
            $modal.css({'display' : 'flex'});
            $modal.addClass('wt-modal--open');
        }
    };

    /**
     * Close modal.
     *
     * @param  modal_class
     */
    SpaModals.prototype.close_modal = function(modal_class) {
        let $modal = $('.wt-modal--' + modal_class);

        if ($modal.length) {
            $modal.hide();
        }
    };

    /**
     * Close all modals.
     */
    SpaModals.prototype.close_modals = function() {
        let $modals = $('.wt-modal');

        $modals.each(function () {
            let $this = $(this),
                $modal_box = $('.wt-modal__box', $this);

            $modal_box.removeClass('wt-modal__box--complete');
            $this.hide().removeClass('.wt-modal--open');
        });
    };

    /**
     * Loader start.
     */
    SpaModals.prototype.loader_start = function() {
        let $modal = $('.wt-modal--open'),
            $box = $('.wt-modal__box', $modal);

        $box.addClass('wt-modal__box--loading');
    };

    /**
     * Loader stop.
     */
    SpaModals.prototype.loader_stop = function() {
        let $modal = $('.wt-modal--open'),
            $box = $('.wt-modal__box', $modal);

        $box.removeClass('wt-modal__box--loading');
    };

    /**
     * Show Complete.
     */
    SpaModals.prototype.complete = function(text) {
        let $modal = $('.wt-modal--open'),
            $box = $('.wt-modal__box', $modal),
            $complete = $('.wt-modal__complete-text', $modal);

        if (!text.length) {
            text = 'The request was sent successfully.';
        }
        $complete.html(text);
        $box.addClass('wt-modal__box--complete');
    };

    // Init.
    $(function () {
        window.SpaModals = SpaModals;
        window.theSpaModals = new SpaModals();
    });
}($ || window.jQuery));