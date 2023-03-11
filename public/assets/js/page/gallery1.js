/**
 * Gallery
 */

$(".gallery .gallery-item").each(function () {
  var me = $(this);

  me.attr('href', me.data('image'));
  me.attr('title', me.data('title'));
  if (me.parent().hasClass('gallery-fw')) {
    me.css({
      height: me.parent().data('item-height'),
    });
    me.find('div').css({
      lineHeight: me.parent().data('item-height') + 'px'
    });
  }
  me.css({
    backgroundImage: 'url("' + me.data('image') + '")'
  });
});
if (jQuery().Chocolat) {
  $(".gallery").Chocolat({
    className: 'gallery',
    imageSelector: '.gallery-item',
  });
}
