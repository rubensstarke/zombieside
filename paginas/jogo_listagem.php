 <div class="col-md-4">
    <div class="thumbnail">
        <img style="height:150px" src="../<?php echo $jogo['imagem']; ?>" alt="Sem imagem" class="img-responsive" />
        <div class="caption">
             <h3><?php echo $jogo['nome'] ?> 
                <?php if(isset($_SESSION['usuario'])) { ?>
                <span data-id="<?php echo $jogo['id_jogo'] ?>" class="star <?php if(isset($_SESSION['favoritos']) && in_array($jogo['id_jogo'], $_SESSION['favoritos'])) echo 'favorito' ?>"><i class="fa fa-star"></i></span>
                <?php } ?>
             </h3>
            <p>Data lançamento: <?php 
                $lacamento = new Datetime($jogo['data_lancamento']);
                echo $lacamento->format('d/m/Y');
            ?></p>
            <p align="center">
                <a href="/paginas/jogo.php?id=<?php echo $jogo['id_jogo'] ?>" class="btn btn-danger btn-block">Ver</a>
            </p>
        </div>
    </div>
</div>