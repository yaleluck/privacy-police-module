# Módulo PrivacyPolice

## Overview
O módulo PrivacyPolicy foi desenvolvido para notificar os clientes sobre alterações na política de privacidade da loja. Após o login, se o cliente ainda não tiver aceitado a nova política, um modal será exibido (nas páginas *"My Account"* e *"My Wish List"*, neste cenário) informando sobre a atualização e permitindo que o usuário aceite a nova política. Ao clicar no botão "Aceitar", uma requisição AJAX atualiza o atributo do cliente *(privacy_policy_accepted)*, e essa informação também é exibida na grid de clientes no painel administrativo.

### Funcionalidades Principais

- **Exibição Condicional do Modal:**
Se o cliente não aceitou a política (atributo com valor 0), o modal é exibido logo após o login.

- **Atualização via AJAX:**
O botão "Aceitar" envia uma requisição AJAX para atualizar o atributo do cliente para 1 (aceito).

- **Extensão da Grid no Admin:**
A grid de clientes é estendida para incluir a coluna "Política de Privacidade Aceita", mostrando se o cliente aceitou (1) ou não (0) a política.

- **Arquitetura Modular e Extensível:**
Utiliza práticas recomendadas do Magento 2, como observers, blocks, UI Components, EAV para atributos e integração via RequireJS para os scripts.


---

## Instalação e Configuração

### Pré-requisitos

- Magento 2
  
- Ambiente de desenvolvimento configurado (ex.: docker-magento ou instalação local)
  
- Git
  

O módulo foi desenvolvido utilizando uma imagem do docker-magento (https://github.com/markshust/docker-magento). A seguir, será mostrado como essa mesma imagem pode ser instalada e configurada.

**Instalação do docker-magento**

```bash
# No diretório do seu projeto:
mkdir -p Sites/magento
cd $_
# Comando automatizado que irá instalar o docker
curl -s https://raw.githubusercontent.com/markshust/docker-magento/master/lib/onelinesetup | bash -s -- magento.test community 2.4.7-p3
```


O trecho ***magento.test** define o hostname que será usado, **community** é a edição do Magento e **2.4.7-p3** define a versão que será instalada.

*OBS: durante a instalação, caso ocorra algum erro de autenticação deverá ser adicionada às suas variáveis globais as credenciais (chaves pública e privada) de uma conta na Adobe que pode ser criada no seguinte link (https://account.magento.com/customer/account/login/). Suas chaves podem ser acessadas em Meu Perfil > Marketplace > My Products > Chaves de acesso. A seguir, será feita a demonstração usando composer.*

```bash
# Instalando o composer
sudo apt install composer

# Configuração das credenciais
composer config http-basic.repo.magento.com <SUA-CHAVE-PÚBLICA> <SUA-CHAVE-PRIVADA>
```

***Feito isso, o comando automatizado de instalação deve funcionar.***

---

### Instalação do módulo

**Passo 1. Clone o repositório:**

```bash
git clone https://github.com/yourusername/PrivacyPolicy.git
cd PrivacyPolicy
```

**Passo 2. Copie o Módulo para o Magento:**

```bash
# Coloque a pasta do módulo em:
app/code/Vendor/PrivacyPolicy/
```

**Passo 3. Detectando o novo módulo:**

```bash
# Para verificar se o Magento detectou o novo módulo, o seguinte comando pode ser executado:
bin/magento module:status
```

Se o módulo for detectado, a saída deverá ser:

```bash
List of disabled modules:
YaleLuck_PrivacyPolice
```

**Passo 4. Habilitação e execução do setup:**

```bash
O seguinte comando deverá ser executado para ativar o módulo:
bin/magento module:enable YaleLuck_PrivacyPolice
```

Após a ativação do módulo, deverá ser realizada a atualização dos módulos existentes no Magento através do seguinte código:

```bash
bin/magento setup:upgrade
```

(OPICIONAL) Se preferir, poderá recompilar o código e limpar o cache.

```bash
bin/magento setup:di:compile
bin/magento cache:flush
```

---

## Testes e Validação

### 1. Teste de Exibição do Modal

- **Criar um Cliente Novo:**

  - Crie uma nova conta de cliente para garantir que o atributo privacy_policy_accepted seja 0 por padrão.

- **Realizar Login:**

  - Faça login com esse cliente.

  - Verifique que o modal é exibido nas páginas "My Account" e "My Wish List" (devido ao FPC na Home, se aplicável).
 
![policy-privacy-modal](https://github.com/user-attachments/assets/a6a868cf-7c74-4ef8-adbe-5f1d2a9c1387)

### 2. Teste de Aceitação via AJAX

- **Clique em "Aceitar":**

  - Abra o Console do navegador e a aba Network para confirmar que a requisição POST para /privacypolicy/index/accept retorna um JSON com "success": true.
 
  - Após aceitar, verifique no banco (na tabela customer_entity_int) que o valor do atributo para o cliente foi atualizado para 1.
 
  - Faça logout e login novamente para confirmar que o modal não aparece mais.

### 3. Verificação na Grid de Clientes do Admin

- Navegue até Customers > All Customers no Admin.
  
- Confirme que a coluna "Política de Privacidade Aceita" aparece e exibe corretamente "Aceito" (ou o valor 1) para clientes que aceitaram, e "Não Aceito" (ou 0) para os que não aceitaram.

![customer-grid](https://github.com/user-attachments/assets/034c7c09-d792-4664-8c20-27960c63f499)

---

## Estrutura do código e Arquitetura

### Estrutura dos diretórios


```
app/code/Vendor/PrivacyPolicy/
├── Block
│   └── PrivacyModal.php           # Lógica para exibição condicional do modal
├── Controller
│   └── Index
│       └── Accept.php             # Processa a requisição AJAX para atualizar o aceite
├── etc
│   ├── module.xml                 # Declaração do módulo e setup_version
│   └── frontend
│       ├── events.xml             # Observer para o evento de login do cliente
│       └── routes.xml             # Define as rotas frontend para o módulo
├── Observer
│   └── CustomerLoginObserver.php  # Verifica o login e seta a flag para exibir o modal
├── Setup
│   └── InstallData.php            # Cria o atributo EAV "privacy_policy_accepted"
├── registration.php               # Registra o módulo no Magento
└── view
    ├── adminhtml
    │   └── ui_component
    │       └── customer_listing.xml  # Extende a grid de clientes no Admin
    └── frontend
        ├── layout
        │   └── default.xml        # Insere o bloco do modal na página
        ├── templates
        │   └── privacy_modal.phtml    # Template HTML do modal
        ├── requirejs-config.js    # Mapeia o módulo JS
        └── web
            ├── css
            │   └── modal.css      # Estilos do modal (centralização, fundo escurecido)
            └── js
                └── privacy-modal.js   # Lógica do modal (eventos e AJAX)
```

### Principais decisões de arquitetura

- **Observer e Sessão:**
  
  Um observer ligado ao evento customer_login define uma flag na sessão para indicar se o modal deve ser exibido. Esse mecanismo evita acoplamento direto com o processo de login.

- **Atributo EAV:**
  
  O status de aceite é armazenado como um atributo customizado (privacy_policy_accepted) do cliente, possibilitando a visualização e manipulação via Admin e extensibilidade futura.

- **AJAX Controller:**
  
  Um controller dedicado processa a requisição AJAX para atualizar o atributo do cliente, garantindo que a ação seja executada de forma assíncrona.

- **UI Component Extension:**
  
  A grid de clientes no Admin é estendida através de um arquivo de UI Component para incluir a nova coluna, permitindo filtrar e ordenar conforme o status do aceite.

- **Modularidade no Frontend:**
  
  Os arquivos CSS e JavaScript são organizados em pastas específicas (view/frontend/web/css e view/frontend/web/js), e o RequireJS é utilizado para gerenciar a inicialização do script do modal, seguindo as boas práticas do Magento 2.
