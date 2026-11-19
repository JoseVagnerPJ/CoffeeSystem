# Q-Grader — Sistema de gestão de cafés

Aplicação web para administrar operações de café especial, da origem dos lotes de café verde ao controle de estoque, torra, logística e avaliação sensorial. O projeto também oferece endpoints autenticados para clientes móveis e uma página pública para provadores convidados participarem de mesas de prova por link.

> O repositório contém o backend Laravel e as interfaces web Blade. Os aplicativos Flutter/mobile consumidores da API não fazem parte deste repositório.

## Índice

- [Visão geral](#visão-geral)
- [Funcionalidades](#funcionalidades)
- [Fluxos de trabalho](#fluxos-de-trabalho)
- [Acesso, permissões e isolamento de dados](#acesso-permissões-e-isolamento-de-dados)
- [API e integrações](#api-e-integrações)
- [Tecnologias](#tecnologias)
- [Arquitetura do código](#arquitetura-do-código)
- [Modelo de dados](#modelo-de-dados)
- [Instalação e execução local](#instalação-e-execução-local)
- [Seeder e acesso de demonstração](#seeder-e-acesso-de-demonstração)
- [Configurações de integração](#configurações-de-integração)
- [Comandos úteis](#comandos-úteis)
- [Pontos de atenção](#pontos-de-atenção)

## Visão geral

O Q-Grader é um sistema multiempresa. Cada unidade possui seus usuários, perfis de acesso, cadastros e dados operacionais. O acesso pelo navegador começa pela identificação da empresa através do CNPJ; em seguida, o usuário informa seu nome de usuário e senha.

O sistema reúne módulos de gestão e operação:

1. **Dashboard** — indicadores e gráficos de estoque, entradas, saídas e atividades de laboratório.
2. **Estoque verde** — cadastro, localização, movimentação e inventário de lotes de café verde.
3. **Laboratório** — mesas de prova, amostras, avaliações sensoriais e relatórios.
4. **Torrefação** — demandas, programação de bateladas, execução e manutenção de máquinas.
5. **Logística** — pedidos sincronizados do Omie, exibidos em um quadro operacional.
6. **Cadastros** — fornecedores, amostras, máquinas, perfis sensoriais, protocolos SCA e equipe.
7. **Portal Master** — administração de empresas, módulos contratados, pagamentos e suspensão de acesso.

Os módulos e as permissões do usuário determinam quais opções ficam disponíveis. O layout compartilhado inclui navegação lateral no computador e botão de menu com gaveta lateral em telas móveis.

## Funcionalidades

### Dashboard

- Mostra os totais de estoque agrupados por perfil sensorial e referência do lote.
- Exibe gráficos de entradas e saídas de estoque, quantidade de mesas de prova e xícaras avaliadas.
- Permite filtrar os dados por perfil sensorial, período e tipo de movimentação.
- Carrega os pontos dos gráficos através de chamadas assíncronas às rotas do dashboard.
- Utiliza Chart.js para desenhar gráficos no navegador.

### Estoque de café verde

- Mantém lotes com referência, origem, perfil sensorial, peso, número e peso unitário das sacas.
- Permite associar a origem a uma empresa, produtor ou fazenda cadastrados.
- Busca lotes por texto e perfil sensorial.
- Distribui lotes em endereços de armazém por linha e posição; a tela apresenta essa localização em um mapa visual.
- Mantém anexos vinculados aos lotes.
- Registra saídas manuais e seus motivos, usuário responsável e localização.
- Exibe entradas e saídas recentes e o histórico de consumo associado às torras.
- Oferece inventário físico com sessão, posições, quantidade de sacas, peso restante, divergências e justificativas.
- Ao encerrar o inventário, consolida o peso físico como saldo do lote, recalcula sacas e atualiza as posições de armazenagem.

O peso físico de uma posição considera a quantidade de sacas fechadas multiplicada pelo peso unitário, mais o peso restante. As operações de ajuste do inventário são agrupadas em transações de banco para evitar aplicar somente parte de uma atualização.

### Laboratório e prova sensorial

- Cria mesas de prova com título, data, itens/amostras e perfil de prova.
- Permite consultar sessões, itens, avaliações e dados técnicos.
- Pesquisa amostras para seleção durante o cadastro de uma mesa.
- Gera relatório de prova em PDF no formato paisagem, usando a ficha do projeto.
- Mantém tags sensoriais associadas às avaliações.
- Pode ser utilizado no navegador e através dos endpoints da API móvel.

As avaliações armazenam atributos como fragrância/aroma, sabor, finalização, acidez, corpo, equilíbrio, uniformidade, xícara limpa, doçura e avaliação geral. O perfil de prova define quais atributos participam do protocolo. O modelo `Cup` calcula a nota final ao salvar: soma os dez atributos positivos e subtrai os defeitos (`xícaras com defeito × intensidade do defeito`). A avaliação inválida recebe nota final zero. Atributos desativados no perfil são normalizados para 10 no cálculo.

### Prova por visitante

Uma mesa pode ser acessada por um endereço público com UUID, sob `/prova/v1/{uuid}`. O visitante informa nome e CPF, entra na avaliação daquela mesa e envia suas notas pelo navegador. A avaliação fica associada ao visitante e à xícara; tags e observações também podem ser registradas. O envio atualiza a avaliação do mesmo visitante para o mesmo item, evitando duplicação em reenvios. Mesas finalizadas mostram a página de mesa encerrada.

### Torrefação

- Cria demandas de torra vinculadas a lotes e perfis sensoriais.
- Distribui e reordena bateladas dentro da programação operacional.
- Permite iniciar, finalizar ou descartar bateladas, com registros de peso de entrada e saída e dados de execução.
- Mantém filas de trabalho, lotes concluídos e indicadores/gráficos por máquina e período.
- Oferece busca de lotes, perfis e mestres de torra para montar demandas.
- Registra limpeza e manutenção operacional de máquinas.
- Permite solicitar divisão de uma demanda/batelada quando necessário.

### Logística e Omie

- Sincroniza pedidos de venda do Omie e seus itens para a base local.
- Consulta dados do cliente e dados de entrega para completar cada pedido.
- Atualiza pedidos já importados usando o identificador do pedido no Omie, evitando duplicá-los a cada sincronização.
- Ignora pedidos cancelados ou faturados na importação para o quadro operacional.
- Mostra pedidos ativos em um quadro com etapa, cliente, valores, peso e tipo/valor de frete.
- Detecta regras configuradas pela empresa a partir do texto de observações do pedido; há reconhecimento auxiliar dos termos `F.C` e `F.U`.

O serviço de integração utiliza a API do Omie. Sem as credenciais configuradas, a sincronização não consegue consultar pedidos.

### Central de cadastros

- **Fornecedores e origens:** empresas, produtores e fazendas, com busca, inativação e relacionamento entre origens.
- **Amostras:** cadastro de informações de origem, variedade, lote do fornecedor, processo, fermentação, umidade, peneira, altitude e safra. Amostras podem ser filtradas e selecionadas nas telas de laboratório e perfis.
- **Perfis sensoriais:** nome, notas, cor e atributos sensoriais. Um perfil pode ser composto por várias amostras; a soma das porcentagens da composição deve totalizar 100%.
- **Perfis de prova SCA:** seleção de atributos e escalas que compõem o protocolo de avaliação.
- **Máquinas:** cadastro, ativação/inativação e histórico de manutenções.
- **Equipe e unidade:** edição de dados da empresa, criação de perfis de permissão e gestão de usuários, senha, foto e estado ativo.

### Configurações e Portal Master

- A página **Configurações de Sistema** apresenta protótipos de opções de interface, fluxo de trabalho, segurança e performance. Os controles dessa tela são demonstrativos e não estão ligados a uma rota de gravação; permissões efetivas são gerenciadas pelos perfis da equipe.
- Usuários autorizados pelo perfil `dev_access` acessam o Portal Master.
- O Portal Master cria empresas e seu primeiro usuário administrador, consulta os dados cadastrados, atualiza empresas, registra pagamentos e ativa ou suspende o acesso.
- A empresa controla quais módulos estão habilitados. A suspensão da empresa bloqueia o acesso web e invalida tokens de API apresentados por usuários daquela unidade.

### Privacidade

Há uma página pública de política de privacidade disponível pela rota `/politica-de-privacidade`.

## Fluxos de trabalho

### Acesso pelo navegador

1. Acesse `/identify` e informe o CNPJ da empresa.
2. O sistema normaliza a entrada para dígitos e procura uma empresa ativa.
3. O identificador da empresa fica na sessão do navegador e a aplicação encaminha o usuário para a tela de login.
4. O usuário informa nome de usuário e senha. A autenticação também verifica se esse usuário pertence à empresa selecionada.
5. As páginas privadas exigem autenticação e passam pelo middleware `company.active`.

### Ciclo de estoque e torra

1. Cadastre origens, amostras e perfis sensoriais.
2. Registre os lotes de café verde e os endereços de armazenagem.
3. Acompanhe o saldo, registre saídas e realize inventários periódicos.
4. Crie demandas de torra para os lotes disponíveis.
5. Programe e execute as bateladas, registrando pesos, máquina e operador.
6. Consulte o histórico de consumo e os indicadores no dashboard.

### Ciclo de prova

1. Crie um perfil de prova com os atributos e escalas desejados.
2. Crie uma mesa e associe amostras ou itens avulsos.
3. Os provadores internos podem participar pelo sistema/API; convidados podem entrar pelo link público da mesa.
4. As avaliações e tags são guardadas por item e provador.
5. Consulte o resultado e gere o relatório PDF.

## Acesso, permissões e isolamento de dados

- **Tenant:** `MainCompany` representa a empresa principal/unidade que utiliza o sistema. Usuários, perfis e registros operacionais carregam `main_company_id`.
- **Escopo por empresa:** o trait `BelongsToCompany` adiciona um escopo global às consultas dos modelos que o utilizam. No navegador, o contexto vem da empresa guardada na sessão; na API, vem do usuário autenticado. No console, esse escopo não é aplicado para permitir manutenção e seeders.
- **Perfil do usuário:** permissões são armazenadas como JSON no perfil. `User::hasPermission()` verifica a permissão individual e reconhece `all_access` e `dev_access` como níveis administrativos abrangentes, com tratamento específico para o próprio `dev_access`.
- **Módulos da empresa:** flags como `mod_stock`, `mod_lab`, `mod_roast` e `mod_logistics` representam módulos habilitados para a empresa. O menu e os controladores também usam permissões do perfil.
- **Produção:** existe uma opção/flag no menu e nos dados da empresa, mas não há um módulo operacional correspondente implementado nas rotas web atuais.
- **Portal Master:** rotas administrativas são protegidas pela permissão `dev_access`.
- **API:** o login emite um token pessoal do Laravel Sanctum. Rotas de laboratório e torra exigem token, empresa ativa e permissões adequadas.

> A aplicação combina controles no menu, gates/middlewares e verificações nos controladores. Ao criar novas rotas ou ações, aplique também a autorização e o filtro por empresa no servidor; ocultar um link na interface não protege uma rota.

## API e integrações

### API móvel

As rotas estão em `routes/api.php` e recebem o prefixo `/api` padrão do Laravel. O login é `POST /api/login`, com `cnpj`, `username`, `password` e `device_name`. O retorno contém token Sanctum, dados básicos do usuário e se ele pode criar mesas.

Rotas autenticadas incluem:

- Laboratório: listar e consultar mesas, entrar em mesa, enviar avaliações, consultar perfis/tags, criar mesa (permissão total) e baixar PDF.
- Torra: consultar a fila do usuário, iniciar/finalizar/descartar batelada e registrar limpeza de máquina.
- A API de amostras possui rotas adicionais declaradas em `routes/web.php` sob `/api`, protegidas por autenticação Sanctum e empresa ativa.

O middleware limita chamadas da API a 60 requisições por minuto por usuário autenticado ou endereço IP, conforme configurado no `RouteServiceProvider`.

### Integração Omie

O serviço `App\Services\OmieService` usa chamadas HTTP para listar pedidos e consultar clientes. As rotas e o mapeamento da importação estão no `LogisticsController`. Configure `OMIE_APP_KEY` e `OMIE_APP_SECRET` no ambiente antes de usar o botão de sincronização.

## Tecnologias

### Backend

- **PHP:** restrição declarada `^7.3|^8.0` no Composer.
- **Laravel Framework:** linha 8.x; `composer.json` declara `^8.75`.
- **Banco relacional:** configuração de exemplo usa MySQL (`DB_CONNECTION=mysql`). As migrations do Laravel definem as tabelas e chaves estrangeiras.
- **Eloquent ORM:** modelos, relacionamentos, casts, escopos globais e eventos de persistência.
- **Laravel Sanctum:** tokens pessoais para autenticação da API.
- **Laravel HTTP Client/Guzzle:** comunicação HTTP com serviços externos, incluindo Omie.
- **barryvdh/laravel-dompdf:** geração de relatórios PDF.
- **Carbon:** datas, períodos e filtros dos indicadores.

### Frontend

- **Blade:** templates renderizados pelo servidor; o layout comum está em `resources/views/layouts/app.blade.php`.
- **Bootstrap 5.3** e **Bootstrap Icons**, carregados por CDN, para layout, responsividade, modais e ícones.
- **JavaScript** no navegador para interações, filtros e requisições assíncronas.
- **jQuery 3.6** e **Select2** para componentes de seleção e busca em telas que os utilizam.
- **Chart.js** para os gráficos do dashboard.
- **Alpine.js**, **Axios**, **Tailwind CSS 3** e **Laravel Mix 6** estão disponíveis/configurados no pipeline frontend do projeto.
- A tela de perfis sensoriais usa Pickr para seleção de cores.

As páginas que carregam bibliotecas por CDN precisam de acesso à rede do navegador. O bundle local de `resources/js/app.js` inicia Alpine e configura Axios; as páginas Blade também podem carregar bibliotecas diretamente conforme o módulo.

## Arquitetura do código

```text
app/
├── Http/
│   ├── Controllers/       # Fluxos web, API, autenticação e Portal Master
│   └── Middleware/        # Verificações compartilhadas (ex.: empresa ativa)
├── Models/                # Entidades Eloquent e regras de persistência
├── Services/              # Integrações externas, como Omie
└── Traits/                # Comportamentos reutilizáveis, como escopo multiempresa

database/
├── migrations/            # Evolução do esquema relacional
├── seeders/               # Dados iniciais e de demonstração
└── factories/             # Fábricas para dados de desenvolvimento/testes

resources/
├── views/                 # Telas Blade e componentes/partials
├── js/                    # Entrada do bundle JavaScript
└── css/                   # CSS de aplicação

routes/
├── web.php                # Navegador, páginas públicas e API web adicional
├── api.php                # API móvel com autenticação por token
└── auth.php               # Login, identificação por CNPJ e logout
```

O fluxo web segue o padrão rota → controlador → modelos/serviços → view Blade ou resposta JSON. Regras como cálculo da nota da xícara vivem no modelo; operações compostas de estoque, prova e logística usam transações de banco quando apropriado. As migrations são a fonte de criação do esquema e podem ser consultadas junto com os modelos para os detalhes de cada coluna.

## Modelo de dados

As entidades centrais incluem:

| Área | Entidades principais | Relação/uso |
|---|---|---|
| Empresa e acesso | `main_companies`, `users`, `profiles`, `personal_access_tokens` | Empresa tenant, usuários, permissões JSON e tokens Sanctum. |
| Origens e amostras | `companies`, `producers`, `farms`, `origin_links`, `samples` | Cadastro de origens e amostras; a origem é polimórfica para empresa, produtor ou fazenda. |
| Estoque | `green_coffee_lots`, `green_coffee_lot_locations`, `stock_movements`, `lot_attachments`, `inventory_sessions`, `inventory_adjustments` | Lotes, endereços, entradas/saídas, arquivos e auditorias físicas. |
| Qualidade | `sensory_profiles`, `sensory_profile_components`, `cupping_profiles`, `cupping_sessions`, `cupping_items`, `cups`, `tags`, `cup_tag` | Composição de perfis, mesas, itens, avaliações e marcações sensoriais. |
| Torra | `roast_demands`, `roasting_batches`, `roast_logs`, `machines`, `machine_maintenances`, `machine_cleaning_logs` | Demandas e execução de bateladas, máquinas e histórico operacional. |
| Logística | `logistic_orders`, `logistic_order_items`, `logistic_order_departments`, `logistic_rules`, `carriers` | Pedidos sincronizados, itens e regras auxiliares. |
| Administração | `company_payments` | Histórico de pagamentos registrado no Portal Master. |

`MainCompany` é a empresa tenant. O modelo `Company` é uma entidade de origem/negócio cadastrada dentro do contexto do tenant; não deve ser confundido com a empresa principal que controla acesso e isolamento.

## Instalação e execução local

### Requisitos

- PHP 8.0 ou superior, com extensões exigidas pelo Laravel e pelo driver de banco utilizado. Embora a restrição Composer também liste PHP 7.3, o código da aplicação usa recursos da linguagem disponíveis a partir do PHP 8.0.
- Composer.
- MySQL ou outro banco compatível configurado para a aplicação; o `.env.example` usa MySQL.
- Node.js e npm para compilar os assets do Laravel Mix.

### Passo a passo

1. Clone o repositório e entre na pasta do projeto.

   ```bash
   git clone <url-do-repositorio>
   cd CoffeeSystem
   ```

2. Instale as dependências PHP e prepare o arquivo de ambiente.

   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

   No Windows PowerShell, use `Copy-Item .env.example .env` no lugar de `cp` se necessário.

3. Crie um banco vazio e ajuste no `.env` os valores de `APP_NAME`, `APP_URL`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` e `DB_PASSWORD`.

4. Crie as tabelas e os dados de demonstração.

   ```bash
   php artisan migrate
   php artisan db:seed
   ```

   O `DatabaseSeeder` chama `DemoCompanySeeder`, que configura a empresa e o acesso descritos na seção seguinte.

5. Instale e compile os assets locais.

   ```bash
   npm install
   npm run development
   ```

   Para recompilar automaticamente durante o desenvolvimento, use `npm run watch`.

6. Prepare o armazenamento público para fotos e anexos e inicie o servidor local.

   ```bash
   php artisan storage:link
   php artisan serve
   ```

7. Abra o endereço mostrado pelo Artisan, normalmente `http://127.0.0.1:8000`.

As migrations devem ser executadas antes dos seeders. O uso de `php artisan migrate:fresh --seed` apaga todas as tabelas e dados antes de recriá-los; reserve esse comando para ambientes descartáveis.

## Seeder e acesso de demonstração

O seeder `Database\Seeders\DemoCompanySeeder` cria ou atualiza a empresa do CNPJ fixo `99999999999999`, atribui a ela um nome aleatório, habilita os módulos e prepara um perfil com acesso administrativo e um usuário padrão.

| Campo | Valor |
|---|---|
| CNPJ | `99999999999999` |
| Usuário | `zevagner` |
| Senha | `1572` |

Execute somente o seeder específico com:

```bash
php artisan db:seed --class=DemoCompanySeeder
```

Ou execute o `DatabaseSeeder`, que o chama:

```bash
php artisan db:seed
```

O seeder é repetível: mantém uma empresa por CNPJ e um usuário `zevagner` por empresa, atualizando o perfil e a senha para os valores de demonstração. O nome aleatório é escolhido novamente quando o seeder roda. Essas credenciais são públicas e servem apenas para desenvolvimento/demonstração; não as utilize em produção.

## Configurações de integração

### Omie

Adicione as credenciais ao `.env` do ambiente que executará a sincronização:

```dotenv
OMIE_APP_KEY=sua_chave
OMIE_APP_SECRET=seu_segredo
```

Não versione credenciais reais. Depois de alterar variáveis de configuração em ambiente com cache, execute `php artisan config:clear`.

### Arquivos públicos

Fotos de usuários e anexos são gravados no disco `public`. Em instalação local, `php artisan storage:link` cria o link entre `public/storage` e `storage/app/public`. Em produção, configure permissões de escrita para os diretórios `storage` e `bootstrap/cache` e sirva o link público conforme o document root da aplicação.

## Comandos úteis

```bash
# Executar migrations
php artisan migrate

# Popular dados de demonstração
php artisan db:seed

# Recriar banco do zero e popular (APAGA os dados atuais)
php artisan migrate:fresh --seed

# Ver rotas web e API
php artisan route:list

# Limpar caches de aplicação/configuração
php artisan optimize:clear

# Compilar assets para produção com Laravel Mix
npm run production

# Executar a suíte PHPUnit disponível no projeto
php artisan test
```

## Pontos de atenção

- A versão declarada é Laravel 8 (`^8.75`), não Laravel 11.
- O `composer.json` declara suporte a PHP 7.3+, mas o código atual usa recursos que exigem PHP 8.0+. Use PHP 8.0 ou superior e verifique extensões/lockfile no ambiente de implantação.
- O nome e o CNPJ de demonstração são reutilizados pelo `DemoCompanySeeder`; só pode haver uma empresa com esse CNPJ no banco.
- A sincronização Omie depende das chaves da conta e de conectividade externa.
- Algumas bibliotecas de interface são carregadas de CDNs; a compilação local do Mix não substitui essas referências externas.
- O repositório fornece endpoints de integração mobile, mas não contém o aplicativo móvel consumidor.
- Configure `APP_DEBUG=false`, HTTPS, credenciais próprias e senhas seguras antes de publicar o sistema.
