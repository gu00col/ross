<?php

/**
 * Console de Comandos - ROSS Analista Jurídico
 * 
 * Este arquivo gerencia os comandos de console do sistema,
 * incluindo migrações de banco de dados.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

// Carregar variáveis de ambiente
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

class MigrateCommand extends Command
{
    protected static $defaultName = 'migrations:migrate';
    protected static $defaultDescription = 'Executa as migrações do banco de dados';

    protected function configure(): void
    {
        $this->setDescription('Executa as migrações do banco de dados')
             ->addOption('force', 'f', InputOption::VALUE_NONE, 'Força a execução mesmo se o banco já tiver tabelas')
             ->addOption('dry-run', 'd', InputOption::VALUE_NONE, 'Simula a execução sem aplicar as mudanças');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>🚀 Iniciando migrações do banco de dados...</info>');

        // Verificar se o banco está configurado
        if (!getenv('DB_HOST') || !getenv('DB_DATABASE')) {
            $output->writeln('<error>❌ Variáveis de banco de dados não configuradas no .env</error>');
            return Command::FAILURE;
        }

        try {
            // Conectar ao banco
            $pdo = new PDO(
                "pgsql:host=" . getenv('DB_HOST') . ";port=" . getenv('DB_PORT', '5432') . ";dbname=" . getenv('DB_DATABASE'),
                getenv('DB_USERNAME'),
                getenv('DB_PASSWORD'),
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $output->writeln('<info>✅ Conectado ao banco de dados</info>');

            // Verificar se já existem tabelas
            $stmt = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public'");
            $tableCount = $stmt->fetchColumn();

            if ($tableCount > 0 && !$input->getOption('force')) {
                $output->writeln("<warning>⚠️  Banco de dados já possui $tableCount tabela(s).</warning>");
                $output->writeln('<comment>Use --force para executar mesmo assim, ou --dry-run para simular</comment>');
                return Command::SUCCESS;
            }

            if ($input->getOption('dry-run')) {
                $output->writeln('<info>🔍 Modo simulação ativado - nenhuma mudança será aplicada</info>');
            }

            // Executar migrações
            $this->runMigrations($pdo, $output, $input->getOption('dry-run'));

            $output->writeln('<info>✅ Migrações concluídas com sucesso!</info>');
            return Command::SUCCESS;

        } catch (PDOException $e) {
            $output->writeln('<error>❌ Erro de conexão com o banco: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        } catch (Exception $e) {
            $output->writeln('<error>❌ Erro durante migração: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }

    private function runMigrations(PDO $pdo, OutputInterface $output, bool $dryRun = false): void
    {
        // Criar tabela de controle de migrações se não existir
        if (!$dryRun) {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS migrations (
                    id SERIAL PRIMARY KEY,
                    migration VARCHAR(255) NOT NULL,
                    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
        }

        // Lista de migrações
        $migrations = [
            '001_create_users_table' => $this->getCreateUsersTableSQL(),
            '002_create_contracts_table' => $this->getCreateContractsTableSQL(),
            '003_create_analysis_table' => $this->getCreateAnalysisTableSQL(),
        ];

        foreach ($migrations as $migrationName => $sql) {
            // Verificar se a migração já foi executada
            if (!$dryRun) {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM migrations WHERE migration = ?");
                $stmt->execute([$migrationName]);
                if ($stmt->fetchColumn() > 0) {
                    $output->writeln("<comment>⏭️  Migração $migrationName já executada</comment>");
                    continue;
                }
            }

            $output->writeln("<info>🔄 Executando migração: $migrationName</info>");

            if (!$dryRun) {
                $pdo->exec($sql);
                
                // Registrar migração
                $stmt = $pdo->prepare("INSERT INTO migrations (migration) VALUES (?)");
                $stmt->execute([$migrationName]);
            }

            $output->writeln("<info>✅ Migração $migrationName concluída</info>");
        }
    }

    private function getCreateUsersTableSQL(): string
    {
        return "
            CREATE TABLE IF NOT EXISTS users (
                id SERIAL PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(50) DEFAULT 'user',
                is_active BOOLEAN DEFAULT true,
                email_verified_at TIMESTAMP NULL,
                remember_token VARCHAR(100) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
    }

    private function getCreateContractsTableSQL(): string
    {
        return "
            CREATE TABLE IF NOT EXISTS contracts (
                id SERIAL PRIMARY KEY,
                user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
                title VARCHAR(255) NOT NULL,
                file_path VARCHAR(500) NOT NULL,
                file_size INTEGER NOT NULL,
                file_type VARCHAR(100) NOT NULL,
                status VARCHAR(50) DEFAULT 'pending',
                analysis_data JSONB NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
    }

    private function getCreateAnalysisTableSQL(): string
    {
        return "
            CREATE TABLE IF NOT EXISTS analysis (
                id SERIAL PRIMARY KEY,
                contract_id INTEGER REFERENCES contracts(id) ON DELETE CASCADE,
                analysis_type VARCHAR(100) NOT NULL,
                result JSONB NOT NULL,
                confidence_score DECIMAL(5,2) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
    }
}

// Criar aplicação de console
$application = new Application('ROSS Console', '1.0.0');

// Adicionar comando de migração
$application->add(new MigrateCommand());

// Executar aplicação
$application->run();

