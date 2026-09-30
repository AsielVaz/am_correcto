<?php
require_once __DIR__ . '/../config/conectorBD.php';
require_once __DIR__ . '/../config/ConectorBD.php'; // Ensure the class file is included

class AdminDocumentosPermanentesBusqueda
{
    private $db;

    public function __construct()
    {
        $this->db = ConectorBD::getInstance();
    }

    /**
     * Busca documentos permanentes por empresa y texto
     * @param int $empresaId
     * @param string $query
     * @return array
     */
    public function buscar($empresaId, $query)
    {
        $query = trim($query);
        if (!$empresaId || !$query) return [];
        $sql = "SELECT * FROM documentos_permanentes WHERE empresa_id = ? AND (
            descripcion LIKE ? OR
            numero LIKE ? OR
            nombre LIKE ? OR
            fecha LIKE ?
        ) ORDER BY fecha DESC";
        $param = "%$query%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$empresaId, $param, $param, $param, $param]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Uso directo para pruebas
// $ctrl = new AdminDocumentosPermanentesBusqueda();
// print_r($ctrl->buscar(1, '2023'));
