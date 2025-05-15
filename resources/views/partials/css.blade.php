<!--begin::Fonts-->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
<!--end::Fonts-->
<!--begin::Page Vendor Stylesheets(used by this page)-->
<link href="{{ asset('assets/plugins/custom/leaflet/leaflet.bundle.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
<!--end::Page Vendor Stylesheets-->
<!--begin::Global Stylesheets Bundle(used by all pages)-->
<link href="{{ asset('assets/plugins/custom/prismjs/prismjs.bundle.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ asset('assets/css/style.bundle.rtl.css') }}" rel="stylesheet" type="text/css" /> --}}
{{-- <link href="{{ asset('assets/css/style.dark.bundle.css') }}" rel="stylesheet" type="text/css" /> --}}
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
<!--end::Global Stylesheets Bundle-->

<style>
    .text-justify {
        text-align: justify;
    }
    .white_img{
        filter: contrast(1000%) invert(100%);
    }
    i{
        font-size: unset;
        color: unset;
    }
</style>
<style>
    .img_carousel{
        /* background-color: rgba(0, 0, 0, 0.445); */
        position: relative;
    }
    .img_carousel img{
        width: 100%;
        height: 600px;
        object-fit: cover;
        object-position: center;
    }
    .img_carousel::before{
        background-color: rgba(0, 0, 0, 0.445);
        content: "Preview";
        margin: auto;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
        height: 100%;
    }
</style>
@stack('css')
