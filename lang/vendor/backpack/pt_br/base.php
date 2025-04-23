<?php

// Please note it is recommended to use the subtag [pt-BR], not [pr_BR]
// That is the one formalized by the W3C in the IANA Language Subtag Registry
// - https://www.iana.org/assignments/language-subtag-registry/language-subtag-registry
// - https://www.w3.org/International/questions/qa-choosing-language-tags
//
// Also, that is the one used by the most popular Laravel translation package
// - https://github.com/caouecs/Laravel-lang/tree/master/src
//
// Backpack provides translations for both subtags, for backwards compatibility.
// But this will change at some point, and we will only support [pt-BR].

return [
    /*
    |--------------------------------------------------------------------------
    | Backpack\Base Language Lines
    |--------------------------------------------------------------------------
    */
    'registration_closed' => 'Novos registros estão desabiltados.',
    'no_email_column' => 'Usuários não possuem um endereço de email associado.',
    'first_page_you_see' => 'A primeira página que você vê depois de logar',
    'login_status' => 'Status do login',
    'logged_in' => 'Você está logado!',
    'toggle_navigation' => 'Alternar navegação',
    'administration' => 'ADMINISTRAÇÃO',
    'user' => 'USUÁRIO',
    'logout' => 'Logout',
    'login' => 'Login',
    'register' => 'Registrar',
    'name' => 'Nome',
    'email_address' => 'E-Mail',
    'password' => 'Senha',
    'old_password' => 'Senha antiga',
    'new_password' => 'Nova senha',
    'confirm_password' => 'Confirmar senha',
    'remember_me' => 'Manter-me logado',
    'forgot_your_password' => 'Esqueci minha senha',
    'reset_password' => 'Resetar senha',
    'send_reset_link' => 'Enviar link de recuperação de senha',
    'click_here_to_reset' => 'Clique aqui para resetar sua senha',
    'change_password' => 'Mudar senha',
    'unauthorized' => 'Sem autorização.',
    'dashboard' => 'Dashboard',
    'handcrafted_by' => 'Feito por',
    'powered_by' => 'Distribuído por',
    'my_account' => 'Minha conta',
    'update_account_info' => 'Atualizar minha conta',
    'save' => 'Salvar',
    'cancel' => 'Cancelar',
    'error' => 'Erro',
    'success' => 'Sucesso',
    'warning' => 'Atenção',
    'notice' => 'Aviso',
    'old_password_incorrect' => 'A senha antiga está incorreta.',
    'password_dont_match' => 'Senhas não são iguais.',
    'password_empty' => 'Certifique-se que ambos os campos de senha estão preenchidos.',
    'password_updated' => 'Senha atualizada.',
    'account_updated' => 'Conta atualizada com sucesso.',
    'unknown_error' => 'Um erro desconhecido aconteceu. Por favor, tente novamente.',
    'error_saving' => 'Erro ao salvar. Por favor, tente novamente.',
    'session_expired_error' => 'Sua sessão expirou. Faça login novamente em sua conta.',
    'welcome' => 'Bem vindo!',
    'use_sidebar' => 'Use a barra de menu à esquerda para criar, editar ou excluir conteúdo.',
    'password_reset' => [
        'greeting' => 'Olá!',
        'subject' => 'Notificação de redefinição de senha',
        'line_1' => 'Você está recebendo este e-mail porque nós recebemos um solicitação de redefinição de senha para sua conta.',
        'line_2' => 'Clique no botão abaixo para redefinir sua senha:',
        'button' => 'Redefinir Senha',
        'notice' => 'Se você não solicitou uma redefinição de senha, nenhuma ação adicional é necessária.',
    ],
    'step' => 'Passo',
    'confirm_email' => 'Confirmar E-mail',
    'choose_new_password' => 'Escolher Nova Senha',
    'confirm_new_password' => 'Confirmar Nova senha',
];
