<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create an admin user for authorship
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin',
                'email' => 'admin@camargoneves.adv.br',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);
        }

        $posts = [
            [
                'titulo' => 'Reforma Tributária 2026: Guia Completo para Empresas',
                'resumo' => 'Análise detalhada das mudanças previstas na reforma tributária e como sua empresa pode se preparar para o novo sistema de impostos.',
                'conteudo' => "A reforma tributária aprovada em 2024 traz mudanças significativas para o sistema tributário brasileiro. Este guia explica os principais pontos que as empresas precisam conhecer.\n\n## O que muda com a Reforma Tributária?\n\nA principal mudança é a unificação de cinco tributos (PIS, Cofins, IPI, ICMS e ISS) em dois novos impostos: o IBS (Imposto sobre Bens e Serviços) e a CBS (Contribuição sobre Bens e Serviços).\n\n## Período de Transição\n\nO período de transição será gradual, começando em 2026 e se estendendo até 2033. Durante esse período, as empresas precisarão se adaptar ao novo modelo.\n\n## Como se Preparar?\n\n1. Revise seus contratos de longo prazo\n2. Avalie o impacto no preço de seus produtos e serviços\n3. Atualize seus sistemas de gestão fiscal\n4. Consulte especialistas tributários\n\nNossa equipe está à disposição para ajudar sua empresa nessa transição.",
                'categoria' => 'Tributário',
                'tags' => 'reforma tributária, IBS, CBS, impostos, planejamento fiscal',
                'status' => 'publicado',
                'published_at' => now()->subDays(1),
            ],
            [
                'titulo' => 'LGPD: Novas Sanções da ANPD Entram em Vigor',
                'resumo' => 'A ANPD intensifica fiscalização e aplica primeiras multas significativas. Saiba como adequar sua empresa e evitar penalidades.',
                'conteudo' => "A Autoridade Nacional de Proteção de Dados (ANPD) iniciou uma nova fase de fiscalização mais rigorosa, aplicando multas significativas a empresas que não estão em conformidade com a LGPD.\n\n## Principais Sanções Aplicadas\n\nAs sanções podem variar desde advertências até multas de até 2% do faturamento, limitadas a R$ 50 milhões por infração.\n\n## O Que Sua Empresa Precisa Fazer\n\n1. Mapear todos os dados pessoais tratados\n2. Implementar políticas de privacidade adequadas\n3. Treinar colaboradores sobre proteção de dados\n4. Designar um Encarregado de Proteção de Dados (DPO)\n5. Estabelecer procedimentos para atender direitos dos titulares\n\n## Prazo para Adequação\n\nNão há mais prazo de carência. As empresas que ainda não se adequaram estão sujeitas a sanções imediatas.\n\nEntre em contato conosco para uma avaliação de conformidade.",
                'categoria' => 'Compliance',
                'tags' => 'LGPD, proteção de dados, ANPD, compliance, multas',
                'status' => 'publicado',
                'published_at' => now()->subDays(5),
            ],
            [
                'titulo' => 'Cláusulas Essenciais em Contratos de M&A',
                'resumo' => 'Guia prático sobre as cláusulas indispensáveis para proteger seus interesses em operações de fusão e aquisição.',
                'conteudo' => "Operações de fusões e aquisições (M&A) exigem contratos bem elaborados para proteger todas as partes envolvidas. Conheça as cláusulas mais importantes.\n\n## Cláusulas de Representações e Garantias\n\nEssas cláusulas estabelecem declarações sobre o estado atual da empresa-alvo, incluindo situação financeira, passivos trabalhistas e tributários.\n\n## Cláusulas de Indenização\n\nDefinem as responsabilidades por perdas decorrentes de informações incorretas ou passivos ocultos descobertos após o fechamento.\n\n## Cláusula de Earn-Out\n\nPermite ajustar o preço de compra com base no desempenho futuro da empresa adquirida.\n\n## Cláusulas de Não-Competição\n\nImpedem que vendedores-chave iniciem negócios concorrentes após a venda.\n\n## Material Adverse Change (MAC)\n\nProtege o comprador de mudanças significativas negativas entre a assinatura e o fechamento.\n\nConsulte nossa equipe para estruturar adequadamente sua operação de M&A.",
                'categoria' => 'Empresarial',
                'tags' => 'M&A, fusões, aquisições, contratos, due diligence',
                'status' => 'publicado',
                'published_at' => now()->subDays(10),
            ],
            [
                'titulo' => 'Trabalho Híbrido: Aspectos Jurídicos e Práticos',
                'resumo' => 'As principais questões trabalhistas relacionadas ao modelo híbrido e como formalizar adequadamente essa modalidade de trabalho.',
                'conteudo' => "O trabalho híbrido se consolidou como modelo preferido por muitas empresas. Entenda os aspectos jurídicos envolvidos.\n\n## Formalização do Acordo\n\nO modelo híbrido deve ser formalizado através de aditivo contratual ou acordo individual, especificando dias presenciais e remotos.\n\n## Controle de Jornada\n\nMesmo no trabalho remoto, o controle de jornada é obrigatório para empresas com mais de 20 empregados.\n\n## Equipamentos e Infraestrutura\n\nA responsabilidade pelo fornecimento de equipamentos deve estar clara em contrato. A empresa pode fornecer ou ressarcir o empregado.\n\n## Ergonomia e Saúde\n\nA empresa mantém responsabilidade pela saúde e segurança do trabalhador, mesmo em home office.\n\n## Despesas do Empregado\n\nCustos com internet, energia e outros podem ser reembolsados, devendo constar em acordo.\n\nProcure orientação jurídica para implementar o modelo híbrido sem riscos.",
                'categoria' => 'Trabalhista',
                'tags' => 'trabalho híbrido, home office, CLT, teletrabalho, ergonomia',
                'status' => 'publicado',
                'published_at' => now()->subDays(15),
            ],
            [
                'titulo' => 'Holding Familiar: Planejamento Patrimonial',
                'resumo' => 'Entenda as vantagens da constituição de holding familiar para proteção patrimonial e planejamento sucessório.',
                'conteudo' => "A holding familiar é uma estratégia eficaz para proteção patrimonial e planejamento sucessório. Conheça seus benefícios.\n\n## O Que é uma Holding Familiar?\n\nÉ uma empresa constituída para administrar o patrimônio de uma família, centralizando bens imóveis, participações societárias e outros ativos.\n\n## Vantagens Tributárias\n\n- Redução na tributação sobre aluguéis\n- Planejamento fiscal na sucessão\n- Menor carga tributária em comparação com ITCMD\n\n## Proteção Patrimonial\n\n- Separação entre patrimônio pessoal e empresarial\n- Proteção contra riscos de atividades profissionais\n- Blindagem contra credores pessoais\n\n## Planejamento Sucessório\n\n- Transmissão organizada do patrimônio\n- Evita conflitos familiares\n- Redução de custos com inventário\n\n## Governança Familiar\n\n- Estabelecimento de regras claras\n- Profissionalização da gestão patrimonial\n- Preparação de sucessores\n\nAgende uma consulta para avaliar se a holding familiar é adequada para sua família.",
                'categoria' => 'Empresarial',
                'tags' => 'holding familiar, planejamento sucessório, proteção patrimonial, ITCMD',
                'status' => 'publicado',
                'published_at' => now()->subDays(20),
            ],
            [
                'titulo' => 'Recuperação de Créditos Tributários: Oportunidades',
                'resumo' => 'Análise das principais teses para recuperação de tributos pagos indevidamente e como estruturar pedidos de restituição.',
                'conteudo' => "Muitas empresas pagam tributos além do devido sem saber. Conheça as principais oportunidades de recuperação.\n\n## Exclusão do ICMS da Base do PIS/Cofins\n\nTese consolidada pelo STF que permite recuperar valores dos últimos 5 anos.\n\n## INSS sobre Verbas Indenizatórias\n\nContribuições previdenciárias não incidem sobre determinadas verbas trabalhistas.\n\n## IPI na Revenda de Importados\n\nImportadores podem recuperar IPI cobrado indevidamente na revenda.\n\n## Como Proceder\n\n1. Realizar auditoria tributária\n2. Identificar créditos recuperáveis\n3. Preparar documentação comprobatória\n4. Protocolar pedido administrativo ou judicial\n5. Aguardar homologação e compensação\n\n## Formas de Recuperação\n\n- Restituição em dinheiro\n- Compensação com tributos vincendos\n- Precatórios (via judicial)\n\nSolicite uma análise tributária para identificar oportunidades em sua empresa.",
                'categoria' => 'Tributário',
                'tags' => 'créditos tributários, restituição, PIS, Cofins, ICMS',
                'status' => 'publicado',
                'published_at' => now()->subDays(25),
            ],
        ];

        foreach ($posts as $postData) {
            $postData['user_id'] = $admin->id;
            $postData['slug'] = Str::slug($postData['titulo']);
            
            // Check if post already exists
            if (!Post::where('slug', $postData['slug'])->exists()) {
                Post::create($postData);
            }
        }
    }
}
