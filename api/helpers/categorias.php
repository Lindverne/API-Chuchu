<?php

function obterIconeCategoria(string $nome): string
{
    $icones = [
        'Pizzas'          => 'fa-pizza-slice',
        'Hambúrgueres'    => 'fa-burger',
        'Massas'          => 'fa-bowl-food',
        'Salgados Fritos' => 'fa-cookie-bite',
        'Bebidas'         => 'fa-glass-water',
        'Sobremesas'      => 'fa-ice-cream',
        'Lanches'         => 'fa-hotdog',
        'Marmitex'        => 'fa-square-full',
        'Açaí'            => 'fa-bowl-food',
        'Sucos'           => 'fa-blender',
        'Cafés'           => 'fa-mug-hot',
        'Artesanais'      => 'fa-hand-holding-droplet',
    ];

    return $icones[$nome] ?? 'fa-utensils';
}

function obterCategorias(mysqli $con): array
{
    $sql       = 'SELECT id, nome, icone FROM categorias ORDER BY nome ASC';
    $resultado = mysqli_query($con, $sql);

    $categorias = [];
    while ($linha = mysqli_fetch_assoc($resultado))
    {
        if (empty($linha['icone']))
        {
            $linha['icone'] = obterIconeCategoria($linha['nome']);
        }
        $categorias[] = $linha;
    }

    return $categorias;
}

function obterNomeCategoria(mysqli $con, int $id): string
{
    $sql  = 'SELECT nome FROM categorias WHERE id = ? LIMIT 1';
    $stmt = mysqli_prepare($con, $sql);

    if (!$stmt)
    {
        return 'Sem categoria';
    }

    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $linha     = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);

    return $linha ? $linha['nome'] : 'Sem categoria';
}
