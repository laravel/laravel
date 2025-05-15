    <!--begin::Global Javascript Bundle(used by all pages)-->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js')}}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js')}}"></script>
    <!--begin::Global Javascript Bundle(used by all pages)-->
    {{-- <script src="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js')}}"></script> --}}
    <!--begin::Page Custom Javascript(used by this page)-->
    <script src="{{ asset('assets/js/custom/widgets.js')}}"></script>
    {{-- <script src="{{ asset('assets/js/custom/apps/chat/chat.js')}}"></script> --}}
    <script src="{{ asset('assets/js/custom/modals/create-app.js')}}"></script>
    <script src="{{ asset('assets/js/custom/modals/upgrade-plan.js')}}"></script>
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/plugins/custom/prismjs/prismjs.bundle.js') }}"></script>
    {{-- <script src="{{ asset('assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js') }}"></script> --}}
    <!--begin::Page Custom Javascript(used by this page)-->
    {{-- <script src="assets/js/custom/authentication/password-reset/new-password.js"></script>
    <script src="assets/js/custom/authentication/sign-in/general.js"></script>
    <script src="assets/js/custom/authentication/sign-up/general.js"></script>
    <script src="assets/js/custom/authentication/sign-in/two-steps.js"></script>
    <script src="assets/js/custom/documentation/charts/chartjs.js"></script>
    <script src="assets/js/custom/documentation/charts/chartjs.js"></script>
    <script src="assets/js/custom/documentation/forms/multiselectsplitter.js"></script>
    <script src="assets/js/custom/documentation/forms/clipboard.js"></script>W
    <script src="assets/js/custom/documentation/charts/amcharts/stock-charts.js"></script>
    <script src="assets/js/custom/apps/user-management/users/view/view.js"></script>
    <script src="assets/js/custom/apps/user-management/users/view/update-details.js"></script>
    <script src="assets/js/custom/apps/user-management/users/view/add-schedule.js"></script>
    <script src="assets/js/custom/apps/user-management/users/view/add-task.js"></script>
    <script src="assets/js/custom/apps/user-management/users/view/update-email.js"></script>
    <script src="assets/js/custom/apps/user-management/users/view/update-password.js"></script>
    <script src="assets/js/custom/apps/user-management/users/view/add-auth-app.js"></script>
    <script src="assets/js/custom/apps/user-management/users/view/add-one-time-password.js"></script>
    <script src="assets/js/custom/apps/user-management/users/list/table.js"></script>
    <script src="assets/js/custom/apps/user-management/users/list/export-users.js"></script>
    <script src="assets/js/custom/apps/user-management/users/list/add.js"></script>
    <script src="assets/js/custom/apps/user-management/roles/list/update-role.js"></script>
    <script src="assets/js/custom/apps/customers/add.js"></script>
    <script src="assets/js/custom/account/settings/signin-methods.js"></script>
    <script src="assets/js/custom/account/settings/profile-details.js"></script>
    <script src="assets/js/custom/account/settings/deactivate-account.js"></script>
    <script src="assets/js/custom/account/security/security-summary.js"></script>
    <script src="assets/js/custom/account/security/license-usage.js"></script>
    <script src="assets/js/custom/documentation/documentation.js"></script>
    <script src="assets/plugins/custom/prismjs/prismjs.bundle.js"></script>
    <script src="assets/js/custom/modals/two-factor-authentication.js"></script>
    <script src="assets/js/custom/modals/create-project.bundle.js"></script>
    <script src="assets/js/custom/modals/users-search.js"></script>
    <script src="assets/js/custom/modals/select-location.js"></script>
    <script src="assets/js/custom/modals/share-earn.js"></script>
    <script src="assets/js/custom/modals/new-target.js"></script>
    <script src="assets/js/custom/modals/new-card.js"></script>
    <script src="assets/js/custom/modals/new-address.js"></script>
    <script src="assets/js/custom/modals/create-account.js"></script>
    <script src="assets/js/custom/modals/create-api-key.js"></script>
    <script src="assets/js/custom/pages/search/horizontal.js"></script>
    <script src="assets/js/custom/pages/projects/users/users.js"></script>
    <script src="assets/js/custom/modals/offer-a-deal.bundle.js"></script>
    <script src="assets/plugins/custom/leaflet/leaflet.bundle.js"></script>
    <script src="assets/js/custom/pages/company/contact.js"></script>
    <script src="assets/js/custom/pages/careers/apply.js"></script>
    <script src="assets/plugins/custom/fslightbox/fslightbox.bundle.js"></script>
    <script src="assets/plugins/custom/typedjs/typedjs.bundle.js"></script>
    <script src="assets/js/custom/landing.js"></script>
    <script src="assets/js/custom/pages/company/pricing.js"></script>
    <script src="assets/js/custom/documentation/forms/daterangepicker.js"></script>
    <script src="assets/js/custom/documentation/forms/recaptcha.js"></script>
    <script src="assets/js/custom/documentation/forms/image-input.js"></script>
    <script src="assets/js/custom/documentation/forms/inputmask.js"></script>
    <script src="assets/js/custom/documentation/general/datatables/advanced.js"></script>
    <script src="assets/js/custom/documentation/general/datatables/api.js"></script>
    <script src="assets/js/custom/documentation/general/datatables/basic.js"></script>
    <script src="assets/js/custom/documentation/general/scroll.js"></script>
    <script src="assets/js/custom/documentation/general/search/basic.js"></script>
    <script src="assets/js/custom/documentation/general/search/menu.js"></script>
    <script src="assets/js/custom/documentation/general/search/responsive.js"></script> --}}
    {{-- <script>
        $("#kt_multiselectsplitter_example_1").multiselectsplitter();
        $("#kt_multiselectsplitter_example_2").multiselectsplitter();
        $("#kt_datepicker_1").flatpickr();
        $("#kt_datepicker_2").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
        });
        $("#kt_datepicker_3").flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });
        $("#kt_datepicker_4").flatpickr({
            altInput: true,
            altFormat: "j F Y",
            dateFormat: "Y-m-d",
            mode: "range"
        });
        $("#kt_datepicker_5").daterangepicker();
        $("#kt_daterangepicker_6").daterangepicker({
            timePicker: true,
            startDate: moment().startOf("hour"),
            endDate: moment().startOf("hour").add(32, "hour"),
            locale: {
                format: "M/DD hh:mm A"
            }
        });

        // DATE PICKER SOLID
        $("#kt_datepicker_1_solid").flatpickr();
        $("#kt_datepicker_2_solid").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
        });
        $("#kt_datepicker_3_solid").flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });
        $("#kt_datepicker_4_solid").flatpickr({
            altInput: true,
            altFormat: "j F Y",
            dateFormat: "Y-m-d",
            mode: "range"
        });
        $("#kt_datepicker_5_solid").daterangepicker();
        $("#kt_daterangepicker_6_solid").daterangepicker({
            timePicker: true,
            startDate: moment().startOf("hour"),
            endDate: moment().startOf("hour").add(32, "hour"),
            locale: {
                format: "M/DD hh:mm A"
            }
        });

    </script> --}}
    <!-- DROP FILES -->
    {{-- <script>
        var myDropzone = new Dropzone("#kt_dropzonejs_example_1", {
            url: "https://keenthemes.com/scripts/void.php", // Set the url for your upload script location
            paramName: "file", // The name that will be used to transfer the file
            maxFiles: 10,
            maxFilesize: 10, // MB
            addRemoveLinks: true,
            accept: function (file, done) {
                if (file.name == "wow.jpg") {
                    done("Naha, you don't.");
                } else {
                    done();
                }
            }
        });

    </script> --}}

    <!-- COPY TO CLIPBOARD -->
    {{-- <script>
        // Select elements
        const target = document.getElementById('kt_clipboard_1');
        const button = target.nextElementSibling;

        // Init clipboard -- for more info, please read the offical documentation: https://clipboardjs.com/
        var clipboard = new ClipboardJS(button, {
            target: target,
            text: function () {
                return target.value;
            }
        });

        // Success action handler
        clipboard.on('success', function (e) {
            const currentLabel = button.innerHTML;

            // Exit label update when already in progress
            if (button.innerHTML === 'Copied!') {
                return;
            }

            // Update button label
            button.innerHTML = 'Copied!';

            // Revert button label after 3 seconds
            setTimeout(function () {
                button.innerHTML = currentLabel;
            }, 3000)
        });

    </script> --}}

    <!-- DATA TABLES -->
    {{-- <script>
        // 1
        $("#kt_datatable_example_1").DataTable();
        // 2
        var status = [{
                "title": "Pending",
                "state": "primary"
            },
            {
                "title": "Delivered",
                "state": "danger"
            },
            {
                "title": "Canceled",
                "state": "primary"
            },
            {
                "title": "Success",
                "state": "success"
            },
            {
                "title": "Info",
                "state": "info"
            },
            {
                "title": "Danger",
                "state": "danger"
            },
            {
                "title": "Warning",
                "state": "warning"
            },
        ];

        $("#kt_datatable_example_2").DataTable({
            "columnDefs": [{
                "render": function (data, type, row) {
                    var index = KTUtil.getRandomInt(1, 7);

                    return data +
                        '<span class="ms-2 badge badge-light-primary fw-bold">pending</span>';
                },
                "targets": 1
            }]
        });
        $("#kt_datatable_example_3").DataTable({
            "columnDefs": [{
                "visible": false,
                "targets": -1
            }]
        });
        $("#kt_datatable_example_4").DataTable({
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api(),
                    data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    return typeof i === "string" ?
                        i.replace(/[\$,]/g, "") * 1 :
                        typeof i === "number" ?
                        i : 0;
                };

                // Total over all pages
                var total = api
                    .column(4)
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Total over this page
                var pageTotal = api
                    .column(4, {
                        page: "current"
                    })
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Update footer
                $(api.column(4).footer()).html(
                    "$" + pageTotal + " ( $" + total + " total)"
                );
            }
        });

    </script> --}}

    @if(session()->has('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session()->get('success') }}",
            });
        </script>
    @endif

    @if(session()->has('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{{ session()->get('error') }}",
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            $(document).ready(function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    html: '@foreach($errors->all() as $error) {!! $error."<br>" !!}@endforeach',
                })
            });
        </script>
    @endif

    <script>
        $(document).ready(function() {
            $(document).on('click', '#deleteModal', function(e) {
            var url = $(this).attr('data-href');
            $('#destroy').attr('action', url );
            $('#delete-modal').modal('show');
            e.preventDefault();
            });
        });
    </script>
    
<script>
    $('#dataTable').dataTable({
        "language": {
            "lengthMenu": "Show _MENU_",
        },
        "dom":
        "<'row'" +
        "<'col-sm-6 d-flex align-items-center justify-conten-start'l>" +
        "<'col-sm-6 d-flex align-items-center justify-content-end'f>" +
        ">" +

        "<'table-responsive'tr>" +

        "<'row'" +
        "<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i>" +
        "<'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>" +
        ">",
        // "processing": true,
        // "serverSide": true
    });

</script>
<script>
    $(function(){
      
      $('.number-only').keyup(function(e) {
            if(this.value!='-')
              while(isNaN(this.value))
                this.value = this.value.split('').reverse().join('').replace(/[\D]/i,'')
                                       .split('').reverse().join('');
        })
        .on("cut copy paste",function(e){
            e.preventDefault();
        });
    
    });
</script>


@stack('script')