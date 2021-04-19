(function($) {
    $.fn.osmetaCharCount = function charCount(args) {
        let defaults = {
            limit        : 100,
            message      : 'Your text is too long',
            charStr      : 'char',
            charPluralStr: 'chars',
        };

        let options = $.extend({}, defaults, args);

        if (this.length) {
            return $(this).each(function charCountEachElement() {
                let $this = $(this);

                let addElements = function() {
                    $this.container   = $('<div>').addClass('char-count-container');
                    $this.warning     = $('<div>').addClass('char-count-warning').text(options.message);
                    $this.counter     = $('<div>').addClass('char-count-counter');
                    $this.counterText = $('<div>').addClass('char-count-counter-text');

                    $this.after($this.container);
                    $this.container.append($this.warning);
                    $this.container.append($this.counter);
                    $this.container.append($this.counterText);

                    $this.warning.hide();
                };

                $this.update = function() {
                    let length = $this.val().length;
                    if (options.limit < length) {
                        $this.warning.show();
                        $this.counter.text((length - options.limit) * -1);
                        $this.counter.addClass('invalid');
                    } else {
                        $this.warning.hide();
                        $this.counter.text(length);
                        $this.counter.removeClass('invalid');
                    }

                    if (length > 1) {
                        $this.counterText.text(options.charPluralStr);
                    } else {
                        $this.counterText.text(options.charStr);
                    }
                };

                $this.on('keyup', function() {
                    $this.update();
                });

                addElements();
                $this.update();
            });
        }
    };
})(jQuery);
