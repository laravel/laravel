"use strict";
var KTSigninGeneral = function() {
    var t, e, i;
    return {
        init: function() {
            t = document.querySelector("#kt_sign_in_form"), e = document.querySelector("#kt_sign_in_submit"), i = FormValidation.formValidation(t, {
                    fields: {
                        username: {
                            validators: {
                                notEmpty: {
                                    message: "Username address is required"
                                }
                            }
                        },
                        password: {
                            validators: {
                                notEmpty: {
                                    message: "The password is required"
                                }
                            }
                        }
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger,
                        bootstrap: new FormValidation.plugins.Bootstrap5({
                            rowSelector: ".fv-row"
                        })
                    }
                })
                // , e.addEventListener("click", (function(n) {
                //     n.preventDefault(), i.validate().then((function(i) {
                //         "Valid" == i ? (e.setAttribute("data-kt-indicator", "on"), e.disabled = !0, setTimeout((function() {
                //             e.removeAttribute("data-kt-indicator"), e.disabled = !1, Swal.fire({
                //                 text: "You have successfully logged in!",
                //                 icon: "success",
                //                 buttonsStyling: !1,
                //                 confirmButtonText: "Ok, got it!",
                //                 customClass: {
                //                     confirmButton: "btn btn-primary"
                //                 }
                //             }).then((function(e) {
                //                 e.isConfirmed && (t.querySelector('[name="email"]').value = "", t.querySelector('[name="password"]').value = "")
                //             }))
                //         }), 2e3)) : Swal.fire({
                //             text: "Sorry, looks like there are some errors detected, please try again.",
                //             icon: "error",
                //             buttonsStyling: !1,
                //             confirmButtonText: "Ok, got it!",
                //             customClass: {
                //                 confirmButton: "btn btn-primary"
                //             }
                //         })
                //     }))
                // }))
        }
    }
}();
KTUtil.onDOMContentLoaded((function() {
    KTSigninGeneral.init()
}));