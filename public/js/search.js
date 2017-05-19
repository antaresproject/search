//
// Search Auto Complete Component 
//

// T0D0 :
// Item count - check!
// Highlight term - check!
// Categoriez - check! 
// Load More - check!
// data-protection - WIP!
// Integration with old V - WIP!

AntaresAC = function () {};

AntaresAC.prototype.init = function () {

    var self = this;
    if ($("#main-search").length === 0) {
        return false;
    }
    self.helpers.updateDOM();
    self.logic();

}


AntaresAC.prototype.helpers = {

    updateDOM: function () {
        $('body').append("<div class='ac-container'></div>");
    }
}

AntaresAC.prototype.keyboard = function () {

//    $('#main-search').keypress(function (e) {
//        if (e.which == 13) {
//            var label = $('#search-form label');
//            if ($(this).val().length <= 0) {
//                var placeholder = label.data('min-length');
//                label.html(placeholder);
//                return false;
//            } else {
//                label.html(label.data('placeholder'));
//            }
//            return true;
//        }
//    });

}
function Search() {
    this.index = 0;
    this.total = 0;
    this.init = function () {
        var self = this;

        $(document).on("datatables.searchLoaded", function (event, datatable, data, total) {
            self.total = total;
            ++self.index;
            var panel = datatable.closest('.mdl-tabs__panel'), tabId = panel.attr('id'), tab = $('.mdl-tabs__tab[href="#' + tabId + '"]');
            if (data.recordsFiltered <= 0) {
                panel.remove();
                tab.remove();
            } else {
                var title = tab.html() + " (" + data.recordsFiltered + ")";
                tab.html(title);
            }
        });
    }
    this.check = function () {
        var self = this;
        var intervalId = setInterval(function () {
            if (self.index === self.total) {
                clearInterval(intervalId);
                self.unhide();

            }
        }, 200);
    }
    this.unhide = function () {
        $('.mdl-tabs').removeClass('opacity-forced');
        $('.mdl-tabs__panel:first,.mdl-tabs__tab:first').addClass('is-active');
    };
}
var search = new Search();
search.init();
search.check();


AntaresAC.prototype.logic = function () {

    var self = this;

    var searchCache = {};
    var category = null;

    var showAllUrl = '#';

    $.widget("custom.catcomplete", $.ui.autocomplete, {
        _create: function (items) {
            this._super();
            this.widget().menu("option", "items", "> :not(.ui-autocomplete-category)");
        },

        _renderMenu: function (ul, items) {


            var itemsCount = 0;

            var that = this;

            currentCategory = "";
            $(ul).addClass('antares-ac');
            var categories = [];
            for (i = 0; i < items.length; i++) {
                if (items[i].category != '') {
                    categories.push(items[i].category);
                }
            }
            $.each(items, function (index, item) {
                ++itemsCount;
                var li;
                if (item.category != currentCategory) {
                    ul.append("<li class='ui-autocomplete-category'>" + item.category + "<span>(" + item.total + ")</span></li>");
                    currentCategory = item.category;
                }
                li = that._renderItemData(ul, item);
                if (item.category) {
                    li.attr("aria-label", item.category);
                }
                li.append('<a href="' + item.url + '"></a>');
                li.find('>a').html(item.content);
            });

            $('.antares-ac').removeClass('has-footer');

            //Footer
            if (itemsCount >= 10) {

                if (!$('.ac-container .antares-ac__footer').length) {
                    $('.antares-ac').addClass('has-footer');
                    $('.antares-ac').append("<li class='antares-ac__footer'><div class='mdl-button mdl-js-button mdl-js-ripple-effect'>Load More</div></li>")
                }

            }

        }
    });

//    $('#main-search').on('input', function () {
//        if (!$(this).val()) {
//            $(this).closest('.search-box').find('.mdl-textfield__label').show();
//        }
//    });



    $("#main-search").catcomplete({
        delay: 250,
        appendTo: ".ac-container",
        focus: function (event, ui) {
            $(event.toElement).removeClass('ui-state-focus');
            $('.antares-ac__footer').removeClass('ui-menu-item');
        },
        open: function () {
            $('.antares-ac__footer').removeClass('ui-menu-item');
            $('.ui-autocomplete-category').prev('li').addClass('last-in-category');
            $('.ui-autocomplete.antares-ac').perfectScrollbar();
        },
        close: function () {
            var parent = $(this).closest('.search-box');
            var label = parent.find('.mdl-textfield__label');
            var input = parent.find('#main-search');

            if (!input.val()) {
                label.show();
            }
        },
        select: function () {
            var parent = $(this).closest('.search-box');
            var label = parent.find('.mdl-textfield__label');
            label.hide();
        },
        _resizeMenu: function () {
            this.menu.element.outerWidth(600);
        },
        source: function (request, response) {
            showAllUrl = $('#search-form').attr('action') + '?search=' + $('#main-search').val();
            category = null;
            itemsCount = 0;
            var search = request.term,
                    form = $('#search-form'),
                    container = form.find('input').parent();
//            if (search in searchCache) {
//                response(searchCache[search]);
//                return;
//            }
            var $element = $(this.element),
                    previous_request = $element.data("jqXHR");
            if (previous_request) {
                previous_request.abort();
            }

            if (container.hasClass('is-invalid')) {
                container.removeClass('is-invalid');
            }
            $element.data("jqXHR", $.ajax({
                type: "GET",
                beforeSend: function (request) {
                    request.setRequestHeader("search-protection", $('.search-protection').val());
                },
                url: form.attr('action').split('?')[0] + "?search=" + search,
                dataType: 'JSON',
                processData: false,
                success: function (data) {
                    searchCache[search] = data;
                    response(data);
                    if (data.length <= 0) {
                        response([{
                                'empty': true
                            }]);
                    }
                },
                error: function (error) {
                    if (error.responseJSON === undefined) {
                        return;
                    }
                    if (error.responseJSON.message.length > 0) {
                        container.addClass('is-invalid');
                        attributes = {
                            text: error.responseJSON.message,
                            dismissQueue: true,
                            layout: 'topRight',
                            maxVisible: 10,
                            timeout: 3000,
                            animation: {
                                open: 'animated bounceInRight',
                                close: 'animated bounceOutRight',
                                easing: 'swing',
                                speed: 1000
                            }
                        };
                        noty($.extend({}, APP.noti.errorFM("lg", "full"), attributes));
                    }

                }
            }));
        },

    });

}

$(function () {
    window.AntaresAC = new AntaresAC();
    window.AntaresAC.init();

});
