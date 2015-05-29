<?php 
    include '../head.php';
    include '../verifica_login.php';
    include '../menu_topo.php';
    $_SESSION['menu_ativo'] = 'jogo';
    include '../menu_lateral.php';

    include '../conexao.php';


    $plataformas = listarPlataformasAtivos($conexao);
    $tipos = listarTiposAtivos($conexao);

    $id = $_GET['id'];

    $sql = "SELECT * FROM jogo WHERE id=:id";

    $prepara = $conexao->prepare($sql);
    $prepara->execute(array(':id' => $id));
    
    $jogo = $prepara->fetchObject();

    // imagens
    $sqlImg = "SELECT * FROM imagem WHERE id_jogo = :id_jogo AND excluido = false";

    $preparaImg = $conexao->prepare($sqlImg);
    $preparaImg->execute(array(':id_jogo' => $jogo->id));
    
    $imagens = $preparaImg->fetchAll();

    $data_lancamento = new Datetime($jogo->data_lancamento);
    $data_lancamento = $data_lancamento->format('d/m/Y');

    if (count($_POST)) {
        $nome = $_POST['nome'];
        $requisitos = $_POST['requisitos'];

        $data_lancamento = $_POST['data_lancamento'];

        $descricao = $_POST['descricao'];
        $id_tipo = $_POST['tipo'];
        $id_plataforma = $_POST['plataforma'];
        $ativo = !isset($_POST['ativo']) ? 'false' : 'true';

        $sql = "UPDATE jogo set nome=:nome, requisitos=:requisitos, data_lancamento=:data_lancamento, ativo=:ativo, descricao=:descricao, id_tipo=:id_tipo, id_plataforma=:id_plataforma
        WHERE id = :id";

        $prepara = $conexao->prepare($sql);

        $params = array(
                        ':nome' => $nome,
                        ':requisitos' => $requisitos,
                        ':data_lancamento' => $data_lancamento,
                        ':ativo' => $ativo,
                        ':descricao' => $descricao,
                        ':id_tipo' => $id_tipo,
                        ':id_plataforma' => $id_plataforma,
                        ':id' => $jogo->id
                  );

        $atualizar = $prepara->execute($params);


         $id_jogo = $jogo->id;


        
        //IMAGENS
        foreach ($_FILES["imagens"]["error"] as $key => $error) {
            if (!$error) {
                $tmp_name = $_FILES["imagens"]["tmp_name"][$key];
                $name = random_string(10).'_'.$_FILES["imagens"]["name"][$key];
                move_uploaded_file($tmp_name, "../../uploads/jogos/$name");

                $sqlImgs = "INSERT INTO imagem (slide, data_enviado, caminho, ativo, id_jogo, excluido) 
                VALUES (:slide, :data_enviado, :caminho, :ativo, :id_jogo, :excluido)";
                $preparaImgs = $conexao->prepare($sqlImgs);

                $data_enviado = new Datetime;
                $data_enviado = $data_enviado->format('Y-m-d');

                $paramsImgs = array(
                                ':slide' => 'true',
                                ':data_enviado' => $data_enviado,
                                ':caminho' => "../../uploads/jogos/$name",
                                ':ativo' => 'true',
                                ':id_jogo' => $id_jogo,
                                ':excluido' => 'false'
                          );
                $inserirImg = $preparaImgs->execute($paramsImgs);

            }
        }
        //END
        if($atualizar) {
          echo '<META HTTP-EQUIV="Refresh" CHARSET=UTF-8 Content="0; URL=/admin/jogo/listar.php?msg=Jogo alterado com sucesso!">';
          exit();
        } else {
          $erro = "Ocorreu um erro com o cadastro, tente novamente!";
        }


    }
    

    //INSERT INTO usuario VALUES (1,'admin', 'admin@zombieside.com.br', 'admin', true, '$1$SQZAG4D2$rZPDi1.Lm4Hi8dIDQXBM61', 'admin');


?>
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <h1 class="page-header">Cadastro de Jogo

        </h1>


        <?php if (isset($erro)) { ?>
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong><?php echo $erro; ?></strong>
        </div>
        <?php } ?>
       
        <form action="/admin/jogo/alterar.php?id=<?php echo $_GET['id'] ?>" method="POST" enctype="multipart/form-data" role="form">
          <legend>Alterar Jogo</legend>
        
          <div class="form-group">
            <label for="">Nome</label>
            <input type="text" value="<?php echo $jogo->nome ?>" required class="form-control" id="nome" name="nome" placeholder="Nome">
          </div>
          <div class="form-group">
            <label for="">Data de Lançamento</label>
            <input type="date" value="<?php echo $data_lancamento ?>" required class="form-control" id="data_lancamento" name="data_lancamento" placeholder="Data de Lançamento">
          </div>
          <div class="form-group">
            <label for="">Plataforma</label>
            <select required class="form-control" name="plataforma" id="plataforma">
              <?php foreach ($plataformas as $plat) { ?>
                <option <?php if ($plat['id'] == $jogo->id_plataforma) echo 'selected'; ?> value="<?php echo $plat['id'] ?>"><?php echo $plat['nome'] ?></option>
              <?php } ?>
            </select>
          </div>

          <div class="form-group">
            <label for="">Tipo</label>
            <select required class="form-control" name="tipo" id="tipo">
              <?php foreach ($tipos as $tipo) { ?>
                <option <?php if ($tipo['id'] == $jogo->id_tipo) echo 'selected'; ?> value="<?php echo $tipo['id'] ?>"><?php echo $tipo['nome'] ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label for="">Requisitos</label>
            <textarea name="requisitos" id="requisitos" cols="30" rows="10"><?php echo $jogo->requisitos ?></textarea>
          </div>

          <div class="form-group">
            <label for="">Descrição</label>
            <textarea name="descricao" id="descricao" cols="30" rows="10"><?php echo $jogo->descricao ?></textarea>
          </div>
          <div class="form-group">
            <label for="">Imagens</label>
            <input name="imagens[]" type="file" multiple>
          </div>

          <div class="row">
            <?php foreach ($imagens as $img)  { ?>
            <div class="col-sm-2 col-md-2 img_<?php echo $img['id']  ?>">
              <div class="thumbnail">
                <img style="height:100px" src="<?php echo $img['caminho'] ?>" alt="...">
                <div class="caption">
                  <p><a href="javascript:" data-id="<?php echo $img['id']; ?>" class="btn btn-primary excluir" role="button">Excluir</a></p>
                </div>
              </div>
            </div>
            <?php } ?>
          </div>
           
          <div class="checkbox">
            <label>
              <input checked name="ativo" type="checkbox" value="1">
              Ativo
            </label>
          </div>
        
          
        
          <button type="submit"  id="submit-all" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>
  <?php 
    include '../footer.php';
  ?>