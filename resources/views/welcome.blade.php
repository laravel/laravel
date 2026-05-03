@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <header class="hero" data-nav-style="dark">
        <div class="hero-bg" style="background-image: url('{{ asset('images/hero_main.webp') }}');"></div>
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <img src="{{ asset('images/logoterra.png') }}" alt="Terra do Meio Logo" class="hero-logo reveal-text">
            <h1 class="reveal-text">Um lugar para ser feliz!</h1>
            <p class="reveal-text delay-1">Uma experiência imersiva na natureza, com o melhor da culinária feita na panela de barro.</p>
            <div class="hero-actions reveal-text delay-2">
                <a href="#menu" class="btn btn-primary">Conhecer o Cardápio</a>
                <a href="#sobre" class="btn btn-outline">Nossa História</a>
            </div>
        </div>
    </header>

    <!-- About Section -->
    <section id="sobre" class="about section" data-nav-style="light">
        <div class="container about-container">
            <div class="about-text scroll-reveal">
                <h2>Por que Terra do Meio?</h2>
                <p>Nas entranhas da Amazônia, e mais precisamente em Altamira, no estado do Pará, existe uma terra desenhada por dois rios, o Iriri e o Xingu - o último, afluente da margem direita do rio Amazonas – essa terra de verdes florestas, contornada por cachoeiras, chama-se “Terra do Meio”.</p>

                <p>Por ali, o Velho André chegou com apenas poucos dias de vida no seringal de seu pai. Por assim dizer, nasceu lá. Sua vida se dividia entre Belém e a “Terra do Meio”. Foi nessa terra, também, onde se refugiou quando perseguido pela Ditadura em 1964.</p>

                <p>Quando chegou por essa área, fim dos anos 60, já quase não havia floresta. Era tudo roçado de mandioca. Roça de terra ruim, pedra, piçarra. O rio Uriboquinha estava quase sumindo. Reflorestou mais de 15ha com a ajuda luxuosa de macacos, coatis, tucanos, papagaios e sabiás. O rio reviveu. A mata voltou. Deu certo!</p>

                <p>Tempos depois, aos 70 anos, o velho comunista inventou de abrir um restaurante, bem aqui, no sítio onde morava, com sua cúmplice de toda uma vida, Dona Esther. O sítio, nas cercanias de Belém, mais exatamente na cabeceira do rio Uriboquinha, sua nascente, Marituba. “Coragem de mamar em onça”, ele dizia. Dona Esther, concordava.</p>

                <p>E assim, ele transplantou pra cá um pedaço do Xingu e criou o “Terra do Meio”, restaurante rural e parque ecológico, onde as pessoas podem tomar banho de igarapé e passear de canoa, enquanto desfrutam de pratos típicos da culinária regional feitos em panela de barro e no forno à lenha.</p>

                <p>No “Terra do Meio” se pratica, sem firulas, a proteção da natureza ambiental e humana, com um bom papo, uma conversa desavisada, um cafezinho depois da chuva da tarde, com os causos dos eternos habitantes da área, que já estavam aqui antes mesmo de ter acampamento cabano: O Curupira, a Mantinta Perera, a Mãe d'água, a Mula-sem-cabeça. Encantarias.</p>

                <p>Dizem que é bonito e que a comida é de se comer e lamber os beiços. Acreditamos, pois pavulagem é com a gente mesmo, até porque, todo mundo sabe que caboco é bicho pávulo.</p>

                <blockquote class="about-quote">
                    <p>“Enfim, o Terra do Meio, como aquela outra, mesopotâmica, entre os rios Xingu e Iriri, da minha paixão e da qual sou oriundo, tem uma única missão: <strong>Fazer as pessoas felizes</strong>”</p>
                    <cite>— André Costa Nunes</cite>
                </blockquote>
            </div>
            <div class="about-image scroll-reveal delay-1">
                <img src="{{ asset('images/iriri-xingu.png') }}" alt="Mapa Iriri-Xingu" class="full-width-img">
                <div class="image-accent"></div>
            </div>
        </div>
    </section>

    <!-- Menu Section -->
    <section id="menu" class="menu section dark-section" data-nav-style="orange">
        <div class="container">
            <div class="section-header scroll-reveal">
                <h2>Especialidades da Casa</h2>
                <p>Nossos pratos são releituras das receitas de nossos avós, preparados lentamente para apurar todo o sabor.</p>
            </div>

            <div class="menu-grid">
                @foreach($featuredProducts as $index => $product)
                    <div class="menu-card scroll-reveal {{ $index > 0 ? 'delay-'.$index : '' }}">
                        @if($product->image_path)
                            @if(\Illuminate\Support\Str::startsWith($product->image_path, ['http://', 'https://']))
                                <img src="{{ $product->image_path }}" alt="{{ $product->name }}" class="menu-img">
                            @elseif(\Illuminate\Support\Str::startsWith($product->image_path, 'images/'))
                                <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" class="menu-img">
                            @else
                                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="menu-img">
                            @endif
                        @else
                            <div class="menu-img-placeholder">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m11 12 2-2 2 2" />
                                    <path d="M22 9.04V21a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V9.04" />
                                    <path d="M22 6.53v.97a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-.97c0-.83.67-1.53 1.5-1.53h17c.83 0 1.5.7 1.5 1.53z" />
                                    <path d="M12 12v9" />
                                </svg>
                            </div>
                        @endif
                        <div class="menu-card-content">
                            <div class="menu-title-row">
                                <h3>{{ $product->name }}</h3>
                                <span class="price">R$ {{ number_format($product->price, 0, ',', '.') }}</span>
                            </div>
                            <p class="menu-desc">{{ $product->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-12 scroll-reveal">
                <a href="#reserva" class="btn btn-outline-light">Ver Cardápio Completo</a>
            </div>
        </div>
    </section>

    <!-- Experience / Gallery -->
    <section id="experiencia" class="gallery section" data-nav-style="light">
        <div class="container">
            <div class="gallery-container">
                <div class="gallery-text scroll-reveal">
                    <h2>Um dia na Terra do Meio</h2>
                    <p>Aqui o tempo passa mais devagar. Desfrute de um passeio pelos nossos pomares, descanse nas redes à sombra das árvores após o almoço, e deixe as crianças brincarem livres na grama.</p>
                    <div class="stats mt-8">
                        <div class="stat-item">
                            <span class="stat-num">5ha</span>
                            <span class="stat-label">de Natureza Preservada</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-num">100%</span>
                            <span class="stat-label">Comida Artesanal</span>
                        </div>
                    </div>
                </div>
                <div class="gallery-grid scroll-reveal delay-1">
                    <img src="{{ asset('images/hero_bg.png') }}" alt="O sítio visto de cima" class="gallery-img main-img">
                    <img src="{{ asset('images/interior_1.png') }}" alt="Salão do restaurante" class="gallery-img">
                    <img src="{{ asset('images/food_1.png') }}" alt="Nossa comida" class="gallery-img">
                </div>
            </div>
        </div>
    </section>

    <!-- Reservation CTA Section -->
    <section id="reserva" class="cta section" data-nav-style="light">
        <div class="container">
            <div class="cta-box scroll-reveal">
                <div class="cta-content">
                    <h2>Garanta sua mesa</h2>
                    <p>Aos finais de semana e feriados, nossa casa enche rápido. Faça sua reserva antecipada e venha viver essa experiência rural.</p>
                </div>
                <div class="cta-form">
                    <form onsubmit="event.preventDefault(); alert('Pedido de reserva enviado! Entraremos em contato em breve.');">
                        <div class="form-group">
                            <input type="text" placeholder="Seu Nome" required>
                        </div>
                        <div class="form-group">
                            <input type="tel" placeholder="Telefone/WhatsApp" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <input type="date" required>
                            </div>
                            <div class="form-group">
                                <input type="number" placeholder="Pessoas" min="1" max="20" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Solicitar Reserva</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
