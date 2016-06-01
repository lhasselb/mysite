/**
 * File: JimEv.js
 */

(function($) {
    $.entwine(function($) {
        console.log('loaded');
        /**
         * Class: .cms-edit-form .field.switchable
         *
         * Hide each switchable field except for the currently selected link type
         */
        $('.cms-edit-form .field.switchable').entwine({
            onmatch: function() {
                console.log('onmatch');
                var id = this.attr('id'),
                    form = this.closest('form');
                console.log(id);
                //console.log(form);
                console.log(form.find('input[name=LinkType]:checked').attr('id'));
                if(form.find('input[name=LinkType]:checked').val() !== id) {
                    console.log(this);
                    this.hide();
                }

                this._super();
            },
            disappear: function() {
                this.slideUp(500);
            },
            reappear: function() {
                this.slideDown(500);
            }
        });

        /**
         * Input: .cms-edit-form input[name=LinkType]
         *
         * On click of radio button, show selected field, hide all others
         */
        $('.cms-edit-form input[name=LinkType]').entwine({
            onclick: function() {
                console.log('clicked');
                var id = this.val(),
                    form = this.closest('form');

                form.find('.field.switchable').disappear(); //.hide();
                form.find('#' + id).reappear(); //.show();

                this._super();
            }
        });
    });
})(jQuery);
