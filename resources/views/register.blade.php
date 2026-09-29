<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SB Admin 2 - Register</title>
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>

<body class="bg-gradient-primary">

    <div class="container">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <div class="row">
                    <!-- Bagian Kiri: Gambar/Gradient -->
                    <div class="col-lg-5 d-none d-lg-block" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);"></div>
                    
                    <!-- Bagian Kanan: Form -->
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                            </div>
                            
                            <form class="user" action="/register" method="POST">
                                @csrf
                                
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Input Nama dengan Ikon -->
                                <div class="form-group position-relative">
                                    <input type="text" name="name" class="form-control form-control-user" 
                                        id="exampleFirstName" placeholder="First Name" required>
                                    <span class="position-absolute" style="right: 25px; top: 50%; transform: translateY(-50%); color: #b7b9cc;">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>

                                <!-- Input Email dengan Ikon -->
                                <div class="form-group position-relative">
                                    <input type="email" name="email" class="form-control form-control-user" 
                                        id="exampleInputEmail" placeholder="Email Address" required>
                                    <span class="position-absolute" style="right: 25px; top: 50%; transform: translateY(-50%); color: #b7b9cc;">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                </div>

                                <!-- Input Password dengan Ikon & Tombol Mata -->
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0 position-relative">
                                        <input type="password" name="password" class="form-control form-control-user" 
                                            id="password" placeholder="Password" required>
                                        <span class="position-absolute" style="right: 25px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #4e73df; z-index: 10;" onclick="togglePassword('password', 'eyeIcon1')">
                                            <i class="fas fa-eye" id="eyeIcon1"></i>
                                        </span>
                                    </div>
                                    <div class="col-sm-6 position-relative">
                                        <input type="password" name="password_confirmation" class="form-control form-control-user" 
                                            id="password_confirmation" placeholder="Repeat Password" required>
                                        <span class="position-absolute" style="right: 25px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #4e73df; z-index: 10;" onclick="togglePassword('password_confirmation', 'eyeIcon2')">
                                            <i class="fas fa-eye" id="eyeIcon2"></i>
                                        </span>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Register Account
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="/login">Already have an account? Login!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script untuk Toggle Password (Dua Input) -->
    <script>
        function togglePassword(inputId, iconId) {
            var passwordInput = document.getElementById(inputId);
            var eyeIcon = document.getElementById(iconId);
            
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            }
        }
    </script>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

</body>
</html>