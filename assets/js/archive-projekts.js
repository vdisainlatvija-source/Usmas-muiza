(function($){
    $(document).ready(function() {
        // Filter dropdown toggle
        $('.filter-toggle').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $dropdown = $(this).closest('.filter-dropdown');
            var $toggle = $(this);
            var $options = $dropdown.find('.filter-options');
            var isOpen = $toggle.hasClass('is-open');

            // Toggle current dropdown (allow both to be open simultaneously)
            if (isOpen) {
                $toggle.removeClass('is-open');
                $options.removeClass('is-open');
            } else {
                $toggle.addClass('is-open');
                $options.addClass('is-open');
            }
        });

        // Close dropdowns when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.filter-dropdown').length) {
                $('.filter-toggle').removeClass('is-open');
                $('.filter-options').removeClass('is-open');
            }
        });

        // Close dropdowns on escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('.filter-toggle').removeClass('is-open');
                $('.filter-options').removeClass('is-open');
            }
        });
    });
}(window.jQuery));
