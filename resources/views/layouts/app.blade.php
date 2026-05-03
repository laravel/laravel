<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Terra do Meio - Restaurante Rural')</title>
    <meta name="description" content="Descubra a autêntica comida rural no Terra do Meio Restaurante Rural. Experiência de sítio com pratos no fogão a lenha, natureza exuberante e ambiente acolhedor.">

    <!-- Google Fonts: Cabin Sketch, Courgette, Lato -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cabin+Sketch:wght@400;700&family=Courgette&family=Fredericka+the+Great&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    @yield('styles')
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="{{ route('home') }}" class="navbar-logo">
                <img src="{{ asset('images/logo_TM.png') }}" alt="Terra do Meio Logo">
            </a>
            <ul class="nav-links">
                <li><a href="#sobre">Nossa História</a></li>
                <li><a href="#menu">Cardápio</a></li>
                <li><a href="#experiencia">Experiência</a></li>
            </ul>
            <a href="#reserva" class="btn btn-primary">Fazer Reserva</a>

            <button class="mobile-menu-btn" aria-label="Abrir menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="4" x2="20" y1="12" y2="12" />
                    <line x1="4" x2="20" y1="6" y2="6" />
                    <line x1="4" x2="20" y1="18" y2="18" />
                </svg>
            </button>
        </div>
    </nav>

    @yield('content')

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col brand-col">
                    <h3>Terra do Meio</h3>
                    <p>O sabor autêntico da roça, cercado pela natureza exuberante.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Instagram">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="20" x="2" y="2" rx="5" ry="5" />
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                                <line x1="17.5" x2="17.51" y1="6.5" y2="6.5" />
                            </svg>
                        </a>
                        <a href="#" aria-label="Facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Funcionamento</h4>
                    <ul>
                        <li>Sexta: 11h30 às 16h00</li>
                        <li>Sábado: 11h30 às 17h00</li>
                        <li>Domingo: 11h30 às 17h00</li>
                        <li>Feriados: 11h30 às 17h00</li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Contato</h4>
                    <ul>
                        <li>(11) 99999-9999</li>
                        <li>contato@terradomeio.com.br</li>
                        <li>Estrada Rural, Km 12<br>Vale Verde - SP</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Terra do Meio Restaurante Rural. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="{{ asset('js/script.js') }}"></script>
    @yield('scripts')
</body>

</html>
