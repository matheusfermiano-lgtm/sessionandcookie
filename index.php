<?php
session_start();

$temaAtual = isset($_COOKIE['tema']) ? $_COOKIE['tema'] : 'light';
$classeBody = ($temaAtual === 'dark') ? 'dark-mode' : '';

if (!isset($_SESSION['receitas'])) {
    $_SESSION['receitas'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao']) && $_POST['acao'] === 'adicionar') {
        $nomeReceita = htmlspecialchars($_POST['nome']);
        $ingredientes = htmlspecialchars($_POST['ingredientes']);

        $_SESSION['receitas'][] = [
            'nome' => $nomeReceita,
            'ingredientes' => $ingredientes
        ];

        setcookie('ultima_receita', $nomeReceita, time() + 86400, "/");

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['acao']) && $_POST['acao'] === 'limpar') {
        session_destroy();
        setcookie('ultima_receita', '', time() - 3600, "/");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar e Listar Receitas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        :root{
            --bg1: #ffdbb2;
            --bg2: #e2ae6a;
            --card: #fff5e6;
            --shadow:#00000022;
            --nav-bg: #fff5e6;

            --text: rgb(202, 98, 0);
            --primary: rgb(243, 146, 0);
            --textbtn: #642800;

            --input: #ffffff;
            --inputborder: #462a00;

            --savebut: #6cff8e;
            --savebuthov: #3cff69;
            --mostbut: #7ec5ff;
            --mostbuthov: #5bb5ff;
            --excbut: #ff655b;
            --excbuthov: #ff4949;
            
        }

        body.dark-mode {
            --bg1: #9b5c42;
            --bg2: #b3732a;
            --card: #6F4F28;
            --shadow:#00000022;
            --nav-bg: #5e3310;

            --text: rgb(255, 151, 53);
            --primary: rgb(243, 146, 0);
            --textbtn: #291102;

            --input: #292626;
            --inputborder: #462a00;

            --savebut: #175e26;
            --savebuthov: #064113;
            --mostbut: #163a5a;
            --mostbuthov: #0c2c46;
            --excbut: #BF2626;
            --excbuthov: #73061A;
        }

        body {
            background: linear-gradient(120deg, var(--bg1), var(--bg2));
            min-height: 100vh;
            color: var(--text); /* Adicionado para texto adaptar ao tema */
        }

        nav {
            background: var(--nav-bg);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 10px var(--shadow);
            margin-bottom: 20px;
        }

        nav a {
            text-decoration: none;
            color: var(--text);
            font-weight: bold;
            font-size: 1.2rem;
        }

        .btn-theme {
            background: var(--mostbut);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            max-width: 125px;
        }

        .btn-theme:hover {
            background: var(--mostbuthov);
        }

        .container {
            background: transparent; /* Deixei transparente para os cards aparecerem melhor */
            justify-content: space-between;
            padding: 0 30px;
            width: 100%;
            max-width: 90%;
            margin: auto;
        }

        .card {
            background: var(--card);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px var(--shadow);
            margin-bottom: 20px;
            color: var(--text);
        }

        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
            color: var(--text);
        }

        .form input, .form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid var(--inputborder);
            background: var(--input);
            color: var(--text);
            background: linear-gradient(120deg, var(--bg1), var(--bg2));
        }

        button, input[type="submit"] {
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
            font-weight: bold;
            width: 200px;
            margin-top: 10px;
        }

        input[type="submit"] {
            background: var(--savebut);
            color: var(--textbtn);
        }
        input[type="submit"]:hover {
            background: var(--savebuthov);
        }

        .btn-danger {
            background: var(--excbut);
            color: var(--textbtn);
        }
        .btn-danger:hover {
            background: var(--excbuthov);
        }

        ul {
            margin-top: 20px;
            list-style: none;
        }

        .recipe-item {
            background: var(--input);
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid var(--inputborder);
            background: linear-gradient(120deg, var(--bg1), var(--bg2));
        }

        .cookie-notice {
            background: var(--card);
            padding: 10px;
            text-align: center;
            margin: 0 auto 20px auto;
            max-width: 90%;
            border-radius: 5px;
            box-shadow: 0 4px 10px var(--shadow);
        }

        /* Responsividade */
        @media (max-width: 500px) {
            .botoes {
                flex-direction: column;
            }
        }
    </style>
</head>
<body class="<?= $classeBody ?>">
    
    <nav>
        <a href="index.php">My Receitas</a>
        <button id="toggle-theme" class="btn-theme">
            <?= $temaAtual === 'dark' ? 'Modo Claro' : 'Modo Escuro' ?>
        </button>
    </nav>

    <main>
        <h1>Painel de Receitas</h1>

        <?php if (isset($_COOKIE['ultima_receita'])): ?>
            <div class="cookie-notice">
                A última receita que você cadastrou foi <em><?= htmlspecialchars($_COOKIE['ultima_receita']) ?></em>
            </div>
        <?php endif; ?>

        <div class="container">
            <div class="card">
                <h2>Adicionar Nova Receita</h2>
                <form class="form" action="index.php" method="POST">
                    <input type="hidden" name="acao" value="adicionar">
                    
                    <label for="nome">Nome da Receita:</label><br>
                    <input type="text" id="nome" name="nome" placeholder="Ex: Bolo de Cenoura" required><br><br>

                    <label for="ingredientes">Ingredientes e Preparo:</label><br>
                    <textarea id="ingredientes" name="ingredientes" rows="4" placeholder="Ex: 3 cenouras, 4 ovos..." required></textarea><br><br>

                    <input type="submit" value="Adicionar Receita">
                </form>
            </div>

            <div class="card">
                <h2>Receitas Cadastradas</h2>
                
                <?php if (empty($_SESSION['receitas'])): ?>
                    <p style="text-align: center;">Nenhuma receita cadastrada ainda. Adicione a sua primeira receita acima!</p>
                <?php else: ?>
                    <?php foreach ($_SESSION['receitas'] as $indice => $receita): ?>
                        <div class="recipe-item">
                            <h3 style="color: var(--primary); margin-bottom: 10px;"><?= ($indice + 1) . ". " . $receita['nome'] ?></h3>
                            <p><?= nl2br($receita['ingredientes']) ?></p>
                        </div>
                    <?php endforeach; ?>
                    
                    <form method="POST" action="index.php">
                        <input type="hidden" name="acao" value="limpar">
                        <button type="submit" class="btn-danger">Apagar Todas as Receitas</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        const btnTheme = document.getElementById('toggle-theme');
        const body = document.body;

        btnTheme.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            

            const isDark = body.classList.contains('dark-mode');
            
            btnTheme.innerText = isDark ? 'Modo Claro' : 'Modo Escuro';
            

            const tema = isDark ? 'dark' : 'light';
            document.cookie = `tema=${tema}; max-age=${30 * 24 * 60 * 60}; path=/`;
        });
    </script>
</body>
</html>