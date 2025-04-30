<script>
    function showToastrMessage(type, title, message, timeOut, progressBar, closeButton, positionClass,
        preventDuplicates,
        onHidden) {
        if (!timeOut)
            timeOut = "5000";
        if (!progressBar)
            progressBar = true;
        if (!closeButton)
            closeButton = true;
        if (!positionClass)
            positionClass = "toast-top-right";
        if (!preventDuplicates)
            preventDuplicates = true;

        toastr.options = {
            "closeButton": closeButton,
            //"newestOnTop": false,
            "progressBar": progressBar,
            "positionClass": positionClass,
            "preventDuplicates": preventDuplicates,
            "timeOut": timeOut,
        };
        if (onHidden)
            toastr.options.onHidden = onHidden;

        toastr[type](message, title);
    }
</script>
