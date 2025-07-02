<?php
session_start();

// Inicializa tarefas se não existir
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

// Adicionar tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $title = trim($_POST['title']);
        $priority = intval($_POST['priority']);
        $difficulty = intval($_POST['difficulty']);

        if ($title !== '') {
            $_SESSION['tasks'][] = [
                'title' => $title,
                'priority' => $priority,
                'difficulty' => $difficulty
            ];
        }
    }

    // Remover tarefa
    if (isset($_POST['remove'])) {
        $index = intval($_POST['remove']);
        array_splice($_SESSION['tasks'], $index, 1);
    }

    // Ordenar tarefas
    if (isset($_POST['sort'])) {
        heapSort($_SESSION['tasks']);
    }

    // Redireciona para evitar reenvio do formulário ao atualizar
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Heap Sort
function heapSort(&$arr) {
    $n = count($arr);

    for ($i = intval($n / 2) - 1; $i >= 0; $i--) {
        heapify($arr, $n, $i);
    }

    for ($i = $n - 1; $i >= 0; $i--) {
        // Swap
        [$arr[0], $arr[$i]] = [$arr[$i], $arr[0]];
        heapify($arr, $i, 0);
    }
}

function heapify(&$arr, $heapSize, $rootIndex) {
    $largest = $rootIndex;
    $left = 2 * $rootIndex + 1;
    $right = 2 * $rootIndex + 2;

    if ($left < $heapSize && compare($arr[$left], $arr[$largest]) > 0) {
        $largest = $left;
    }

    if ($right < $heapSize && compare($arr[$right], $arr[$largest]) > 0) {
        $largest = $right;
    }

    if ($largest !== $rootIndex) {
        [$arr[$rootIndex], $arr[$largest]] = [$arr[$largest], $arr[$rootIndex]];
        heapify($arr, $heapSize, $largest);
    }
}

// Comparador: prioridade maior primeiro, depois dificuldade maior
function compare($a, $b) {
    if ($a['priority'] > $b['priority']) return 1;
    if ($a['priority'] < $b['priority']) return -1;
    if ($a['difficulty'] > $b['difficulty']) return 1;
    if ($a['difficulty'] < $b['difficulty']) return -1;
    return 0;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>To-Do List | Heap Sort</title>
    <script src="https://kit.fontawesome.com/eeeae88e34.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #121212;
            color: #e0e0e0;
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #ffffff;
        }

        form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        form input[type="text"],
        form input[type="number"] {
            flex: 1 1 120px;
            padding: 10px;
            border: 1px solid #444;
            border-radius: 4px;
            background: #1e1e1e;
            color: #e0e0e0;
        }

        form input::placeholder {
            color: #888;
        }

        form button {
            padding: 10px 16px;
            background-color: #3a86ff;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #265dbe;
        }

        .sort-button {
            display: block;
            width: 100%;
            padding: 10px 0;
            margin-bottom: 20px;
            background-color: #00b894;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .sort-button:hover {
            background-color: #00997a;
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        li {
            background: #1e1e1e;
            border: 1px solid #333;
            padding: 12px 16px;
            margin-bottom: 10px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        li span {
            flex: 1;
        }

        li button {
            background-color: #d63031;
            border: none;
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        li button:hover {
            background-color: #b71c1c;
        }
    </style>
</head>
<body>
    <h1>To-Do List com Heap Sort</h1>

    <form method="post">
        <input type="text" name="title" placeholder="Título da Tarefa" required>
        <input type="number" name="priority" placeholder="Prioridade" required>
        <input type="number" name="difficulty" placeholder="Dificuldade" required>
        <button type="submit" name="add"><i class="fa-solid fa-plus"></i></button>
    </form>

    <form method="post">
        <button type="submit" name="sort" class="sort-button">Heap Sort</button>
    </form>

    <ul>
        <?php foreach ($_SESSION['tasks'] as $index => $task): ?>
            <li>
                <span>
                    <?=htmlspecialchars($task['title'])?>
                    (Prioridade: <?=intval($task['priority'])?>,
                    Dificuldade: <?=intval($task['difficulty'])?>)
                </span>
                <form method="post" style="margin:0;">
                    <button type="submit" name="remove" value="<?=$index?>"><i class="fa-solid fa-trash"></i></button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
