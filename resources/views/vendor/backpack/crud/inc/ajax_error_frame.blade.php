<script>
$(document).ajaxComplete((e, result, settings) => {
    if(result.responseJSON?.exception !== undefined) {
        $.ajax({...settings, accepts: "text/html", backpackExceptionHandler: true});
    }
    else if(settings.backpackExceptionHandler) {
        Noty.closeAll();
        showErrorFrame(result.responseText);
    }
});

const showErrorFrame = html => {
    let page = document.createElement('html');
    page.innerHTML = html;
    page.querySelectorAll('a').forEach(a => a.setAttribute('target', '_top'));

    let modal = document.getElementById('ajax-error-frame');

    if (typeof modal !== 'undefined' && modal !== null) {
        modal.innerHTML = '';
    } else {
        modal = document.createElement('div');
        modal.id = 'ajax-error-frame';
        modal.style.position = 'fixed';
        modal.style.width = '100vw';
        modal.style.height = '100vh';
        modal.style.padding = '5vh 5vw';
        modal.style.backgroundColor = 'rgba(0, 0, 0, 0.4)';
        modal.style.zIndex = 200000;
    }

    let iframe = document.createElement('iframe');
    iframe.style.backgroundColor = '#17161A';
    iframe.style.borderRadius = '5px';
    iframe.style.width = '100%';
    iframe.style.height = '100%';
    iframe.style.border = '0';
    iframe.style.boxShadow = '0 0 4rem';
    modal.appendChild(iframe);

    document.body.prepend(modal);
    document.body.style.overflow = 'hidden';
    iframe.contentWindow.document.open();
    iframe.contentWindow.document.write(page.outerHTML);
    iframe.contentWindow.document.close();

    // Close on click
    modal.addEventListener('click', () => hideErrorFrame(modal));

    // Close on escape key press
    modal.setAttribute('tabindex', 0);
    modal.addEventListener('keydown', e => e.key === 'Escape' && hideErrorFrame(modal));
    modal.focus();
}

const hideErrorFrame = modal => {
    modal.outerHTML = '';
    document.body.style.overflow = 'visible';
}
</script>