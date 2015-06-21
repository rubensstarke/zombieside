<?php 
    include '../verifica_login.php';
    include '../conexao.php';

	$id = $_GET['id'];

	if ((int)$id > 0) {
		$sql = "UPDATE opiniao set excluido = true WHERE id = :id";

        $prepara = $conexao->prepare($sql);

        $params = array(':id' => $id);

        $excluir = $prepara->execute($params);

        if ($excluir) {
            echo '<META HTTP-EQUIV="Refresh" CHARSET=UTF-8 Content="0; URL=/admin/opinioes/listar.php?msg=Opinião excluida com sucesso!">';
        	exit();
        }
	}
?>