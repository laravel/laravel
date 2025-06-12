@extends('Admin.layout.shared')
 @section('content')

<div class="main-content">
    <div class="page-content">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="page-title mb-0 font-size-18">
                        Dashboard
                    </h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item active">
                                Welcome to Qovex Dashboard
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="avatar-sm font-size-20 me-3">
                                <span class="avatar-title bg-soft-primary text-primary rounded">
                                    <i class="mdi mdi-tag-plus-outline"></i>
                                </span>
                            </div>
                            <div class="flex-1">
                                <div class="font-size-16 mt-2">
                                    New Orders
                                </div>
                            </div>
                        </div>
                        <h4 class="mt-4">1,368</h4>
                        <div class="row">
                            <div class="col-7">
                                <p class="mb-0">
                                    <span class="text-success me-2">
                                        0.28%
                                        <i class="mdi mdi-arrow-up"></i>
                                    </span>
                                </p>
                            </div>
                            <div class="col-5 align-self-center">
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 62%"
                                        aria-valuenow="62" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="avatar-sm font-size-20 me-3">
                                <span class="avatar-title bg-soft-primary text-primary rounded">
                                    <i class="mdi mdi-account-multiple-outline"></i>
                                </span>
                            </div>
                            <div class="flex-1">
                                <div class="font-size-16 mt-2">
                                    New Users
                                </div>
                            </div>
                        </div>
                        <h4 class="mt-4">2,456</h4>
                        <div class="row">
                            <div class="col-7">
                                <p class="mb-0">
                                    <span class="text-success me-2">
                                        0.16%
                                        <i class="mdi mdi-arrow-up"></i>
                                    </span>
                                </p>
                            </div>
                            <div class="col-5 align-self-center">
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 62%"
                                        aria-valuenow="62" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            Sales Report
                        </h4>

                        <div id="line-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Revenue</h4>

                        <div id="column-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-xl-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            Sales Analytics
                        </h4>

                        <div class="row align-items-center">
                            <div class="col-sm-6">
                                <div id="donut-chart" class="apex-charts"></div>
                            </div>
                            <div class="col-sm-6">
                                <div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="py-3">
                                                <p class="mb-1 text-truncate">
                                                    <i class="mdi mdi-circle text-primary me-1"></i>
                                                    Online
                                                </p>
                                                <h5>$ 2,652</h5>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="py-3">
                                                <p class="mb-1 text-truncate">
                                                    <i class="mdi mdi-circle text-success me-1"></i>
                                                    Offline
                                                </p>
                                                <h5>$ 2,284</h5>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="py-3">
                                                <p class="mb-1 text-truncate">
                                                    <i class="mdi mdi-circle text-warning me-1"></i>
                                                    Marketing
                                                </p>
                                                <h5>$ 1,753</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            Monthly Sales
                        </h4>

                        <div id="scatter-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3">
                <div class="card bg-primary">
                    <div class="card-body">
                        <div class="text-white-50">
                            <h5 class="text-white">
                                2400 + New Users
                            </h5>
                            <p>
                                At vero eos et accusamus et
                                iusto odio dignissimos ducimus
                            </p>
                            <div>
                                <a href="javascript: void(0);" class="btn btn-outline-success btn-sm">View more</a>
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <div class="col-8">
                                <div class="mt-4">
                                    <img src="assets/images/widget-img.png" alt="widget-img"
                                        class="img-fluid mx-auto d-block" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            Overview
                        </h4>

                        <div>
                            <div class="pb-3 border-bottom">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <p class="mb-2">
                                            New Visitors
                                        </p>
                                        <h4 class="mb-0">
                                            3,524
                                        </h4>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-end">
                                            <div>
                                                2.06 %
                                                <i class="mdi mdi-arrow-up text-success ms-1"></i>
                                            </div>
                                            <div class="progress progress-sm mt-3">
                                                <div class="progress-bar" role="progressbar"
                                                    style="
                                                                        width: 62%;
                                                                    "
                                                    aria-valuenow="62" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="py-3 border-bottom">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <p class="mb-2">
                                            Product Views
                                        </p>
                                        <h4 class="mb-0">
                                            2,465
                                        </h4>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-end">
                                            <div>
                                                0.37 %
                                                <i class="mdi mdi-arrow-up text-success ms-1"></i>
                                            </div>
                                            <div class="progress progress-sm mt-3">
                                                <div class="progress-bar bg-warning" role="progressbar"
                                                    style="
                                                                        width: 48%;
                                                                    "
                                                    aria-valuenow="48" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-3">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <p class="mb-2">
                                            Revenue
                                        </p>
                                        <h4 class="mb-0">
                                            $ 4,653
                                        </h4>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-end">
                                            <div>
                                                2.18 %
                                                <i class="mdi mdi-arrow-up text-success ms-1"></i>
                                            </div>
                                            <div class="progress progress-sm mt-3">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="
                                                                        width: 78%;
                                                                    "
                                                    aria-valuenow="78" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Reviews</h4>
                        <div class="mb-4">
                            <h5>
                                <span class="text-primary">500</span>+ Satisfied clients
                            </h5>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-quote-left h4 text-primary"></i>
                        </div>
                        <div id="reviewExampleControls" class="carousel slide review-carousel" data-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <div>
                                        <p>
                                            To achieve this, it
                                            would be necessary
                                            to have
                                            achieveuniform
                                            grammar,
                                            pronunciation and
                                            more common words
                                        </p>
                                        <div class="d-flex align-items-start mt-4">
                                            <div class="avatar-sm me-3">
                                                <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                    J
                                                </span>
                                            </div>
                                            <div class="flex-1">
                                                <h5 class="font-size-16 mb-1">
                                                    Jessie
                                                    Mitchell
                                                </h5>
                                                <p class="mb-2">
                                                    CEO of ABC
                                                    Company
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div>
                                        <p>
                                            For science, music,
                                            sport, pronunciation
                                            etc, Europe uses the
                                            same vocabulary
                                            languages only
                                            differ in their
                                            grammar
                                        </p>
                                        <div class="d-flex align-items-start mt-4">
                                            <div class="avatar-sm me-3">
                                                <img src="assets/images/users/avatar-4.jpg" alt="avatar-4"
                                                    class="img-fluid rounded-circle" />
                                            </div>
                                            <div class="flex-1">
                                                <h5 class="font-size-16 mb-1">
                                                    Kelly Rivera
                                                </h5>
                                                <p class="mb-2">
                                                    Web
                                                    Developer
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div>
                                        <p>
                                            The new common
                                            language will be
                                            more simple language
                                            and regular than the
                                            existing European
                                            languages.
                                        </p>
                                        <div class="d-flex align-items-start mt-4">
                                            <div class="avatar-sm me-3">
                                                <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                    S
                                                </span>
                                            </div>
                                            <div class="flex-1">
                                                <h5 class="font-size-16 mb-1">
                                                    Simon
                                                    Hawkins
                                                </h5>
                                                <p class="mb-2">
                                                    CEO of XYZ
                                                    Company
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <a class="carousel-control-prev" href="#reviewExampleControls" role="button"
                                data-bs-slide="prev">
                                <i class="mdi mdi-chevron-left carousel-control-icon"></i>
                            </a>
                            <a class="carousel-control-next" href="#reviewExampleControls" role="button"
                                data-bs-slide="next">
                                <i class="mdi mdi-chevron-right carousel-control-icon"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            Revenue by location
                        </h4>

                        <div class="row">
                            <div class="col-sm-6">
                                <div id="usa-vectormap" style="height: 230px"></div>
                            </div>

                            <div class="col-sm-5 ms-auto">
                                <div class="mt-4 mt-sm-0">
                                    <p>Last month Revenue</p>

                                    <div class="d-flex align-items-start py-3">
                                        <div class="flex-1">
                                            <p class="mb-2">
                                                California
                                            </p>
                                            <h5 class="mb-0">
                                                $ 2,256
                                            </h5>
                                        </div>
                                        <div class="ms-auto">
                                            2.52 %
                                            <i class="mdi mdi-arrow-up text-success ms-1"></i>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start py-3 border-top">
                                        <div class="flex-1">
                                            <p class="mb-2">
                                                Nevada
                                            </p>
                                            <h5 class="mb-0">
                                                $ 1,853
                                            </h5>
                                        </div>
                                        <div class="ms-auto">
                                            1.26 %
                                            <i class="mdi mdi-arrow-up text-success ms-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Inbox</h4>

                        <ul class="inbox-wid list-unstyled">
                            <li class="inbox-list-item">
                                <a href="javascript: void(0);">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3 align-self-center">
                                            <img src="assets/images/users/avatar-3.jpg" alt="avatar-3"
                                                class="avatar-sm rounded-circle" />
                                        </div>
                                        <div class="flex-1 overflow-hidden">
                                            <h5 class="font-size-16 mb-1">
                                                Paul
                                            </h5>
                                            <p class="text-truncate mb-0">
                                                Hey! there I'm
                                                available
                                            </p>
                                        </div>
                                        <div class="font-size-12 ms-auto">
                                            05 min
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="inbox-list-item">
                                <a href="javascript: void(0);">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3 align-self-center">
                                            <img src="assets/images/users/avatar-4.jpg" alt="avatar-4"
                                                class="avatar-sm rounded-circle" />
                                        </div>
                                        <div class="flex-1 overflow-hidden">
                                            <h5 class="font-size-16 mb-1">
                                                Mary
                                            </h5>
                                            <p class="text-truncate mb-0">
                                                This theme is
                                                awesome!
                                            </p>
                                        </div>
                                        <div class="font-size-12 ms-auto">
                                            12 min
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="inbox-list-item">
                                <a href="javascript: void(0);">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3 align-self-center">
                                            <img src="assets/images/users/avatar-5.jpg" alt="avatar-5"
                                                class="avatar-sm rounded-circle" />
                                        </div>
                                        <div class="flex-1 overflow-hidden">
                                            <h5 class="font-size-16 mb-1">
                                                Cynthia
                                            </h5>
                                            <p class="text-truncate mb-0">
                                                Nice to meet you
                                            </p>
                                        </div>
                                        <div class="font-size-12 ms-auto">
                                            18 min
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="inbox-list-item">
                                <a href="javascript: void(0);">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3 align-self-center">
                                            <img src="assets/images/users/avatar-6.jpg" alt="avatar-6"
                                                class="avatar-sm rounded-circle" />
                                        </div>
                                        <div class="flex-1 overflow-hidden">
                                            <h5 class="font-size-16 mb-1">
                                                Darren
                                            </h5>
                                            <p class="text-truncate mb-0">
                                                I've finished
                                                it! See you so
                                            </p>
                                        </div>
                                        <div class="font-size-12 ms-auto">
                                            2hr ago
                                        </div>
                                    </div>
                                </a>
                            </li>
                        </ul>

                        <div class="text-center">
                            <a href="javascript: void(0);" class="btn btn-primary btn-sm">Load more</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            Latest Transactions
                        </h4>

                        <div class="table-responsive">
                            <table class="table table-centered">
                                <thead>
                                    <tr>
                                        <th scope="col">
                                            Date
                                        </th>
                                        <th scope="col">
                                            Id no.
                                        </th>
                                        <th scope="col">
                                            Billing Name
                                        </th>
                                        <th scope="col">
                                            Amount
                                        </th>
                                        <th scope="col" colspan="2">
                                            Payment Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>15/01/2020</td>
                                        <td>
                                            <a href="javascript: void(0);" class="text-body fw-medium">#SK1235</a>
                                        </td>
                                        <td>Werner Berlin</td>
                                        <td>$ 125</td>
                                        <td>
                                            <span class="badge badge-soft-success font-size-12">Paid</span>
                                        </td>
                                        <td>
                                            <a href="javascript: void(0);" class="btn btn-primary btn-sm">View</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>16/01/2020</td>
                                        <td>
                                            <a href="javascript: void(0);" class="text-body fw-medium">#SK1236</a>
                                        </td>
                                        <td>Robert Jordan</td>
                                        <td>$ 118</td>
                                        <td>
                                            <span class="badge badge-soft-danger font-size-12">Chargeback</span>
                                        </td>
                                        <td>
                                            <a href="javascript: void(0);" class="btn btn-primary btn-sm">View</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>17/01/2020</td>
                                        <td>
                                            <a href="javascript: void(0);" class="text-body fw-medium">#SK1237</a>
                                        </td>
                                        <td>Daniel Finch</td>
                                        <td>$ 115</td>
                                        <td>
                                            <span class="badge badge-soft-success font-size-12">Paid</span>
                                        </td>
                                        <td>
                                            <a href="javascript: void(0);" class="btn btn-primary btn-sm">View</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>18/01/2020</td>
                                        <td>
                                            <a href="javascript: void(0);" class="text-body fw-medium">#SK1238</a>
                                        </td>
                                        <td>James Hawkins</td>
                                        <td>$ 121</td>
                                        <td>
                                            <span class="badge badge-soft-warning font-size-12">Refund</span>
                                        </td>
                                        <td>
                                            <a href="javascript: void(0);" class="btn btn-primary btn-sm">View</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <ul class="pagination pagination-rounded justify-content-center mb-0">
                                <li class="page-item">
                                    <a class="page-link" href="javascript: void(0);">Previous</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="javascript: void(0);">1</a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link" href="javascript: void(0);">2</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="javascript: void(0);">3</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="javascript: void(0);">Next</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->
    </div>
    <!-- End Page-content -->

    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <script>
                        document.write(
                            new Date().getFullYear()
                        );
                    </script>
                    &copy; Qovex.
                </div>
                <div class="col-sm-6">
                    <div class="text-sm-end d-none d-sm-block">
                        Design & Develop by Themesbrand
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>
@endsection
