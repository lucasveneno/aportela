<?php

return [

    'demands_group' => 'Demandas',


    /*
    |--------------------------------------------------------------------------
    | Resources Menu Language Lines
    |--------------------------------------------------------------------------
    */

    'menu' => [
        'areas' => 'Áreas',
        'categories' => 'Categorias',
        'demands' => 'Demandas',
        'regions' => 'Regiões',
        'users' => 'Usuários',
    ],

    /*
    |--------------------------------------------------------------------------
    | Areas Resource Page Language Lines
    |--------------------------------------------------------------------------
    */

    'areas' => [
        'label' => 'Área',
        'plural_label' => 'Áreas',
        'name' => 'Nome',
        'status' => 'Status',
        'enable_this' => 'Habilitar esta área?',

    ],

    /*
    |--------------------------------------------------------------------------
    | Categories Resource Page Language Lines
    |--------------------------------------------------------------------------
    */

    'categories' => [
        'label' => 'Categoria',
        'plural_label' => 'Categorias',
        'area' => 'Área',
        'name' => 'Nome',
    ],


    /*
    |--------------------------------------------------------------------------
    | Regions Resource Page Language Lines
    |--------------------------------------------------------------------------
    */

    'regions' => [
        'label' => 'Região',
        'plural_label' => 'Regiões',
        'name' => 'Nome',
        'status' => 'Status',
        'enable_this' => 'Habilitar esta região?',
    ],

    /*
    |--------------------------------------------------------------------------
    | Users Resource Page Language Lines
    |--------------------------------------------------------------------------
    */

    'users' => [
        'Name' => 'Nome',
        'email' => 'E-mail',
        'password' => 'Senha',
        'ermission ' => 'Permissão',
        'Region' => 'região',

    ],

    'demands' => [
        'label' => 'Demanda',
        'plural_label' => 'Demandas',
        // Classify box
        'classify_demand' => 'Classificar demanda',
        'area' => 'Área',
        'category' => 'Categoria',
        'description' => 'Descrição da demanda',
        // Priority box
        'section_priority_title' => 'Prioridade',
        'section_priority_description' => 'Selecione os critérios para definição de prioridade',
        'max' => 'Prioridade Máxima (Ação imediata)',
        'max_description' => 'Situações importantes e críticas que exigem resposta imediata para evitar problemas futuros, como riscos à segurança, saúde ou grandes impactos na mobilidade.',
        'high' => 'Prioridade Alta (Planejamento rápido)',
        'high_description' => 'Questões relevantes que precisam ser resolvidas rapidamente para evitar agravamentos, como infraestrutura essencial ou serviços públicos deficientes.',
        'medium' => 'Prioridade Média (Médio prazo)',
        'medium_description' => 'Melhorias necessárias, mas que podem ser planejadas dentro de um prazo médio razoável sem impactos imediatos graves.',
        'low' => 'Prioridade Baixa (Longo prazo)',
        'low_description' => 'Ajustes e melhorias de longo prazo que aumentam a qualidade de vida, mas que não afetam diretamente a segurança ou o funcionamento essencial da cidade.',

        //
        'section_location_title' => 'Localização do Ocorrido',
        'section_location_description' => 'Informe o endereço exato ou as coordenadas geográficas onde a solicitação deve ser atendida.',

        'section_files_title' => 'Adicione arquivos a esta solicitação',
        'section_files_description' => 'Obrigatório para análise: Fotos do local, documentos oficiais ou relatórios técnicos. Tamanho máximo total: 25MB. Todos os arquivos serão mantidos em sigilo.',

        // Applicant data
        'section_applicant_title' => 'Identificação do Solicitante',
        'section_applicant_description' => 'Informe os dados obrigatórios da pessoa ou entidade que está formalizando esta demanda.',
        'applicant_demand_origin' => 'Esta demanda tem um solicitante?',
        'applicant' => 'Solicitante da demanda',
        'applicant_name' => 'Nome',
        'applicant_role' => 'Tipo',
        'applicant_cpf' => 'CPF',
        'applicant_full_address' => 'Endereço',
        'applicant_phone' => 'Telefone',
        'applicant_email' => 'E-mail',
        'applicant_instagram' => 'Instagram',
        'applicant_facebook' => 'Facebook',

        'requires_councilor_on_site' => 'Vereador(a) precisa ir até o local?',
        'draft' => 'Rascunho',
        'draft_description' => 'Atenção: Se você não marcar esta opção, o formulário será enviado como finalizado e não será possível editá-lo posteriormente. Certifique-se de que todas as informações estão corretas antes de prosseguir.',
        'draft_input' => 'Salvar demanda como rascunho?',
    ],

    'widgets' => [
        'stats_overview' => [
            'new_demands' => 'Novas demandas',
            'total_demands' => 'Demandas totais',
            'pending_demands' => 'Demandas pendentes',
            'completed_demands ' => 'Demandas finalizadas',
        ],
    ],



];
