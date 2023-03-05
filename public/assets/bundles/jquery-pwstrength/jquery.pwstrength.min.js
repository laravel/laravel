/**
 * jquery.pwstrength http://matoilic.github.com/jquery.pwstrength
 *
 * @version v0.1.1
 * @author Mato Ilic <info@matoilic.ch>
 * @copyright 2013 Mato Ilic
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 */
(function(c){function g(a){var b=c.pwstrength(c(this).val()),a=a.data,d;d=a.classes[b];a.indicator.removeClass(a.indicator.data("pwclass"));a.indicator.data("pwclass",d);a.indicator.addClass(d);a.indicator.find(a.label).html(a.texts[b])}c.pwstrength=function(a){var b=0,d=a.length,c,e,f;b+=d<5?0:d<8?5:d<16?10:15;(c=a.match(/[a-z]/g))&&(b+=1);(d=a.match(/[A-Z]/g))&&(b+=5);d&&c&&(b+=2);(e=a.match(/\d/g))&&e.length>1&&(b+=5);(f=a.match(/\W/g))&&(b+=f.length>1?15:10);d&&c&&e&&f&&(b+=15);a.match(/\s/)&&
(b+=10);return b<15?0:b<20?1:b<35?2:b<50?3:4};c.fn.pwstrength=function(a){a=c.extend({label:".label",classes:["pw-very-weak","pw-weak","pw-mediocre","pw-strong","pw-very-strong"],texts:["very weak","weak","mediocre","strong","very strong"]},a||{});a.indicator=c("#"+this.data("indicator"));return this.keyup(a,g)}})(jQuery);
