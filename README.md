<p align="center">
  <a href="#" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

# ☕ Sistema de Gestão de Torrefação

Ainda não decidi o nome, mas é uma aplicação robusta desenvolvida em Laravel para o controle total do ciclo de vida do café. O sistema foca na rastreabilidade, desde a entrada do lote de café verde até a análise sensorial (cupping) e o registro final da torra.

---

## Estrutura do Banco de Dados

O banco de dados foi projetado para garantir integridade referencial e facilitar a auditoria de processos. Abaixo estão os principais módulos modelados:

### 1. Núcleo e Acesso
* **`companies`**: Suporte multi-empresa, permitindo que várias torrefações usem a mesma instância.
* **`users`**: Gestão de usuários com níveis de acesso (Admin, Gestor, Mestre de Torra e Provador).

### 2. Inventário de Café Verde
* **`partners`**: Registro de produtores, fornecedores e parceiros.
* **`green_coffee_lots`**: Controle de lotes recebidos, incluindo variedade, safra, umidade e localização no armazém.

### 3. Laboratório e Qualidade (SCA)
* **`samples`**: Amostras vinculadas aos lotes para testes de qualidade.
* **`cupping_sessions`**: Sessões de prova organizadas por data e responsável.
* **`cups`**: Avaliações individuais seguindo o protocolo SCA (Fragrância, Sabor, Acidez, Corpo, Balanço, etc.).
* **`tags`**: Sistema de hashtags para identificar notas sensoriais (ex: #frutado, #chocolate).

### 4. Produção e Torra
* **`roasting_machines`**: Cadastro de máquinas com limite de ciclos para manutenção preventiva.
* **`roast_demands`**: Ordens de serviço geradas pela gestão para os Mestres de Torra.
* **`roast_logs`**: Registro real da execução da torra, controlando o peso de entrada e saída (quebra).

---

## Tecnologias Utilizadas

- **Framework:** [Laravel 11](https://laravel.com)
- **Banco de Dados:** MySQL 8.0 (Relacional)
- **Linguagem:** PHP 8.3
- **Documentação do Banco:** Migrations e SQL nativo.

---

## Como subir o Banco de Dados

Para criar a estrutura do banco de dados em seu ambiente local (MySQL Workbench ou via terminal), utilize o script SQL disponível na pasta `/database` ou execute as migrations do Laravel:

```bash
php artisan migrate
