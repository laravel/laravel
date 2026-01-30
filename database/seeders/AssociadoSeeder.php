<?php

namespace Database\Seeders;

use App\Models\Associado;
use Illuminate\Database\Seeder;

class AssociadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $associados = [
            [
                'nome' => 'Dr. Ricardo Camargo',
                'cargo' => 'Sócio Fundador',
                'bio' => 'Com mais de 30 anos de experiência em Direito Empresarial, Ricardo Camargo é referência nacional em operações de M&A e reestruturação societária. Formado pela USP com mestrado em Direito Comercial e especializações em Harvard Law School.',
                'oab' => 'SP-12345',
                'email' => 'ricardo@camargoneves.adv.br',
                'foto' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=500&h=600&fit=crop',
                'ordem' => 1,
                'is_active' => true,
            ],
            [
                'nome' => 'Dra. Fernanda Neves',
                'cargo' => 'Sócia Fundadora',
                'bio' => 'Especialista em Direito Tributário com foco em planejamento fiscal internacional. Doutora pela PUC-SP, professora convidada, reconhecida como uma das principais tributaristas do país.',
                'oab' => 'SP-23456',
                'email' => 'fernanda@camargoneves.adv.br',
                'foto' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=500&h=600&fit=crop',
                'ordem' => 2,
                'is_active' => true,
            ],
            [
                'nome' => 'Dr. Marcos Oliveira',
                'cargo' => 'Sócio - Direito Trabalhista',
                'bio' => 'Especialista em Direito Trabalhista Empresarial. MBA em Gestão Empresarial pela FGV. 15 anos de experiência em contencioso trabalhista de alto volume.',
                'oab' => 'SP-34567',
                'email' => 'marcos@camargoneves.adv.br',
                'foto' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop',
                'ordem' => 3,
                'is_active' => true,
            ],
            [
                'nome' => 'Dra. Carolina Santos',
                'cargo' => 'Sócia - Compliance',
                'bio' => 'Especialista em Compliance e LGPD. Certificação em Data Privacy pela IAPP. Experiência em programas anticorrupção para multinacionais.',
                'oab' => 'SP-45678',
                'email' => 'carolina@camargoneves.adv.br',
                'foto' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400&h=400&fit=crop',
                'ordem' => 4,
                'is_active' => true,
            ],
            [
                'nome' => 'Dr. André Moreira',
                'cargo' => 'Associado Sênior - Contratos',
                'bio' => 'Especialista em Contratos Internacionais pela FGV. Atuação em operações cross-border e contratos de tecnologia.',
                'oab' => 'SP-56789',
                'email' => 'andre@camargoneves.adv.br',
                'foto' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop',
                'ordem' => 5,
                'is_active' => true,
            ],
            [
                'nome' => 'Dra. Beatriz Lima',
                'cargo' => 'Associada Sênior - Propriedade Intelectual',
                'bio' => 'Mestre em Direito da Propriedade Intelectual pela USP. Especialista em registro de marcas e patentes.',
                'oab' => 'SP-67890',
                'email' => 'beatriz@camargoneves.adv.br',
                'foto' => 'https://images.unsplash.com/photo-1594744803329-e58b31de8bf5?w=400&h=400&fit=crop',
                'ordem' => 6,
                'is_active' => true,
            ],
            [
                'nome' => 'Dr. Felipe Costa',
                'cargo' => 'Associado - Tributário',
                'bio' => 'Especialista em Direito Tributário pela PUC-SP. Foco em contencioso tributário e recuperação de créditos.',
                'oab' => 'SP-78901',
                'email' => 'felipe@camargoneves.adv.br',
                'foto' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=400&h=400&fit=crop',
                'ordem' => 7,
                'is_active' => true,
            ],
            [
                'nome' => 'Dra. Marina Alves',
                'cargo' => 'Associada - Empresarial',
                'bio' => 'LL.M. em Corporate Law pela Columbia University. Experiência em M&A e due diligence.',
                'oab' => 'SP-89012',
                'email' => 'marina@camargoneves.adv.br',
                'foto' => 'https://images.unsplash.com/photo-1598550874175-4d0ef436c909?w=400&h=400&fit=crop',
                'ordem' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($associados as $associado) {
            Associado::create($associado);
        }
    }
}
