document.addEventListener('DOMContentLoaded', function() {
  let prev = null;
  Array.from(document.getElementsByTagName('article')).reverse().forEach(function (article) {
    const dedupId = article.dataset.dedupId;
    if (dedupId === prev) {
      article.getElementsByTagName('header')[0].classList.add('hidden');
    }
    prev = dedupId;
  });
});
