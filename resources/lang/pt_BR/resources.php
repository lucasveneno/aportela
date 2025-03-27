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
        'section_priority_description' => 'Selecione os critérios para Definição de Prioridade',
        'max' => 'Prioridade Máxima (Ação imediata)',
        'max_description' => 'Situações críticas que exigem resposta imediata, como riscos à segurança, saúde ou grandes impactos na mobilidade.',

        'high' => 'Prioridade Alta (Planejamento rápido)',
        'high_description' => 'Questões relevantes que precisam ser resolvidas rapidamente para evitar agravamentos, como infraestrutura essencial ou serviços públicos deficientes.',

        'medium' => 'Prioridade Média (Médio prazo)',
        'medium_description' => 'Melhorias necessárias, mas que podem ser planejadas dentro de um prazo razoável sem impactos imediatos graves.',

        'low' => 'Prioridade Baixa (Longo prazo)',
        'low_description' => 'Ajustes e melhorias de longo prazo que aumentam a qualidade de vida, mas que não afetam diretamente a segurança ou o funcionamento essencial da cidade.',

    ],



];
