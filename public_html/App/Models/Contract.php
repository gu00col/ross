<?php

namespace App\Models;

use MF\Model\Model;

/**
 * Model para gerenciar contratos do sistema
 * 
 * @author Luis Gustavo Barbosa de Oliveira
 * @version 1.0.0
 */
class Contract extends Model
{
    /**
     * Obtém todos os contratos
     * 
     * @param int $limit Limite de registros
     * @param int $offset Offset para paginação
     * @return array Lista de contratos
     */
    public function getAllContracts(int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, u.nome as user_name, u.email as user_email
            FROM contracts c
            LEFT JOIN users u ON c.user_id = u.id
            ORDER BY c.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtém contratos por usuário
     * 
     * @param string $userId UUID do usuário
     * @param int $limit Limite de registros
     * @param int $offset Offset para paginação
     * @return array Lista de contratos do usuário
     */
    public function getContractsByUser(string $userId, int $limit = 10, int $offset = 0, array $filters = []): array
    {
        $sql = "
            SELECT 
                c.id,
                c.status,
                c.original_filename,
                c.storage_path,
                c.created_at,
                c.analyzed_at,
                EXTRACT(EPOCH FROM (c.analyzed_at - c.created_at)) AS segundos_para_analisar
            FROM contracts c
        ";

        $where = ['c.user_id = :user_id'];
        $params = [':user_id' => $userId];

        if (!empty($filters['search'])) {
            $where[] = 'c.original_filename ILIKE :search';
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['status'])) {
            $where[] = 'c.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['start_date'])) {
            $where[] = 'c.created_at >= :start_date';
            $params[':start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            // Adiciona 1 dia para incluir todos os registros do dia final
            $where[] = 'c.created_at < :end_date::date + interval \'1 day\'';
            $params[':end_date'] = $filters['end_date'];
        }

        if (count($where) > 0) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset";
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => &$val) {
             $stmt->bindParam($key, $val, is_int($val) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Conta o número total de contratos para um usuário específico.
     *
     * @param string $userId O UUID do usuário.
     * @return integer A contagem total de contratos.
     */
    public function getContractCountByUser(string $userId, array $filters = []): int
    {
        $sql = "SELECT COUNT(*) FROM contracts c";
        
        $where = ['c.user_id = :user_id'];
        $params = [':user_id' => $userId];

        if (!empty($filters['search'])) {
            $where[] = 'c.original_filename ILIKE :search';
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['status'])) {
            $where[] = 'c.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['start_date'])) {
            $where[] = 'c.created_at >= :start_date';
            $params[':start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $where[] = 'c.created_at < :end_date::date + interval \'1 day\'';
            $params[':end_date'] = $filters['end_date'];
        }

        if (count($where) > 0) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => &$val) {
             $stmt->bindParam($key, $val, \PDO::PARAM_STR);
        }

        $stmt->execute();
        
        return (int)$stmt->fetchColumn();
    }

    /**
     * Busca o JSON da análise de um contrato específico.
     *
     * @param string $contractId O UUID do contrato.
     * @param string $userId O UUID do usuário.
     * @return string|null O resultado da análise em formato JSON ou null se não encontrado.
     */
    public function getContractAnalysisJson(string $contractId, string $userId): ?string
    {
        $sql = "
            SELECT
                jsonb_agg(
                    jsonb_build_object(
                        'section_id', s.id,
                        'display_order', adp.display_order,
                        'label', adp.label,
                        'content', adp.content,
                        'details', adp.details
                    )
                    ORDER BY s.display_order, adp.display_order
                ) AS analysis_json
            FROM
                public.contracts c
            JOIN
                public.analysis_data_points adp ON c.id = adp.contract_id
            JOIN
                public.analysis_sections s ON adp.section_id = s.id
            WHERE
                c.id = :contract_id AND c.user_id = :user_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':contract_id', $contractId, \PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $userId, \PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result['analysis_json'] ?? null;
    }

    /**
     * Obtém um contrato por ID
     * 
     * @param string $id ID do contrato
     * @return array|null Dados do contrato ou null se não encontrado
     */
    public function getContractById(string $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, u.nome as user_name, u.email as user_email
            FROM contracts c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.id = :id
        ");
        $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Cria um novo contrato
     * 
     * @param string $id ID único do contrato
     * @param string $originalFilename Nome original do arquivo
     * @param string $storagePath Caminho de armazenamento
     * @param string|null $userId ID do usuário (opcional)
     * @param string $status Status do contrato
     * @return string ID do contrato criado
     */
    public function createContract(string $id, string $originalFilename, string $storagePath, ?string $userId = null, string $status = 'pending'): string
    {
        $stmt = $this->db->prepare("
            INSERT INTO contracts (id, original_filename, storage_path, user_id, status) 
            VALUES (:id, :original_filename, :storage_path, :user_id, :status)
        ");
        $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
        $stmt->bindParam(':original_filename', $originalFilename, \PDO::PARAM_STR);
        $stmt->bindParam(':storage_path', $storagePath, \PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $userId, \PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, \PDO::PARAM_STR);
        $stmt->execute();
        
        return $id;
    }

    /**
     * Atualiza um contrato
     * 
     * @param string $id ID do contrato
     * @param string|null $originalFilename Nome original do arquivo
     * @param string|null $storagePath Caminho de armazenamento
     * @param string|null $status Status do contrato
     * @param string|null $rawText Texto extraído do contrato
     * @param string|null $userId ID do usuário
     * @return int Número de linhas afetadas
     */
    public function updateContract(string $id, ?string $originalFilename = null, ?string $storagePath = null, ?string $status = null, ?string $rawText = null, ?string $userId = null): int
    {
        $fields = [];
        $params = [':id' => $id];

        if ($originalFilename !== null) {
            $fields[] = "original_filename = :original_filename";
            $params[':original_filename'] = $originalFilename;
        }
        if ($storagePath !== null) {
            $fields[] = "storage_path = :storage_path";
            $params[':storage_path'] = $storagePath;
        }
        if ($status !== null) {
            $fields[] = "status = :status";
            $params[':status'] = $status;
        }
        if ($rawText !== null) {
            $fields[] = "raw_text = :raw_text";
            $params[':raw_text'] = $rawText;
        }
        if ($userId !== null) {
            $fields[] = "user_id = :user_id";
            $params[':user_id'] = $userId;
        }

        if (empty($fields)) {
            return 0;
        }

        $sql = "UPDATE contracts SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, \PDO::PARAM_STR);
        }
        $stmt->execute();
        
        return $stmt->rowCount();
    }

    /**
     * Marca um contrato como analisado
     * 
     * @param string $id ID do contrato
     * @param string $rawText Texto extraído do contrato
     * @return int Número de linhas afetadas
     */
    public function markAsAnalyzed(string $id, string $rawText): int
    {
        $stmt = $this->db->prepare("
            UPDATE contracts 
            SET status = 'analyzed', raw_text = :raw_text, analyzed_at = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
        $stmt->bindParam(':raw_text', $rawText, \PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->rowCount();
    }

    /**
     * Remove um contrato
     * 
     * @param string $id ID do contrato
     * @return int Número de linhas afetadas
     */
    public function deleteContract(string $id): int
    {
        $stmt = $this->db->prepare("DELETE FROM contracts WHERE id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->rowCount();
    }

    /**
     * Obtém estatísticas dos contratos
     * 
     * @return array Estatísticas dos contratos
     */
    public function getContractStats(): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_contracts,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_contracts,
                COUNT(CASE WHEN status = 'analyzed' THEN 1 END) as analyzed_contracts,
                COUNT(CASE WHEN status = 'error' THEN 1 END) as error_contracts,
                COUNT(CASE WHEN created_at >= CURRENT_DATE THEN 1 END) as today_contracts
            FROM contracts
        ");
        $stmt->execute();
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtém contratos por status
     * 
     * @param string $status Status dos contratos
     * @param int $limit Limite de registros
     * @param int $offset Offset para paginação
     * @return array Lista de contratos com o status especificado
     */
    public function getContractsByStatus(string $status, int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, u.nome as user_name, u.email as user_email
            FROM contracts c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.status = :status
            ORDER BY c.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindParam(':status', $status, \PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca contratos por nome de arquivo
     * 
     * @param string $search Termo de busca
     * @param int $limit Limite de registros
     * @param int $offset Offset para paginação
     * @return array Lista de contratos encontrados
     */
    public function searchContracts(string $search, int $limit = 50, int $offset = 0): array
    {
        $searchTerm = '%' . $search . '%';
        $stmt = $this->db->prepare("
            SELECT c.*, u.nome as user_name, u.email as user_email
            FROM contracts c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.original_filename ILIKE :search
            ORDER BY c.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindParam(':search', $searchTerm, \PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca os últimos 5 contratos de um usuário específico.
     *
     * @param string $userId O UUID do usuário.
     * @return array Uma lista dos últimos 5 contratos.
     */
    public function getLatestByUserId(string $userId): array
    {
        $query = "
            SELECT 
                c.id,
                c.original_filename, 
                c.status, 
                c.created_at,
                c.analyzed_at,
                EXTRACT(EPOCH FROM (c.analyzed_at - c.created_at)) AS segundos_para_analisar
            FROM 
                contracts c
            WHERE 
                c.user_id = :user_id
            ORDER BY 
                c.created_at DESC 
            LIMIT 5
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca as estatísticas do dashboard para um usuário específico.
     *
     * @param string $userId O UUID do usuário.
     * @return array Um array associativo com as contagens.
     */
    public function getDashboardStats(string $userId): array
    {
        $query = "
            SELECT
                COUNT(CASE WHEN created_at >= NOW() - INTERVAL '24 hours' THEN 1 END) as sent_today,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 'processed' THEN 1 END) as processed,
                COUNT(CASE WHEN status = 'error' THEN 1 END) as with_error
            FROM
                contracts
            WHERE
                user_id = :user_id
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // Garante que o retorno seja um array mesmo que não haja resultados
        return $result ?: [
            'sent_today' => 0,
            'pending' => 0,
            'processed' => 0,
            'with_error' => 0
        ];
    }
}

?>
