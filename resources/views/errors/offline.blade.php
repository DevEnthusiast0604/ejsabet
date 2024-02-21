<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none">

<head>

    <meta charset="utf-8" />
    <title>Offline Website</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Salam" name="description" />
    <meta content="Yuyuan Zhang" name="author" />
    <!-- App favicon -->

    <!-- Bootstrap Css -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <style>
        body {
            background-color: #212529;
        }
    </style>

</head>

<body>

    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-5">
                        <div class="card overflow-hidden">
                            <div class="card-body p-4">
                                <div class="text-center">
                                    <img src="{{asset('assets/images/auth-offline.gif')}}" alt="" height="210">
                                    <h3 class="mt-4 fw-semibold">{{ __('page.we_are_currently_offline') }}</h3>
                                    <p class="text-muted mb-4 fs-14">We can't show you this images because you aren't connected to the internet. When you’re back online refresh the page or hit the button below</p>
                                    <button class="btn btn-success btn-border" onClick="window.location.href=window.location.href">Refresh</button>
                                </div>
                            </div>
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->

                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->
    </div>
    <!-- end auth-page-wrapper -->

</body>

</html>