<?php
// index.php - CECyTE Jalisco (Plantel 24 Puerto Vallarta - Ixtapa)
require_once 'conexion.php';

$id_editar = "";
$nombre = "";
$apellidos = "";
$telefono_alumno = "";
$telefono_tutor = "";
$secundaria = "";
$colonia = "";
$carrera_interes = "";

// 1. ELIMINAR ALUMNO
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $conn->prepare("DELETE FROM alumnos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

// 2. CARGAR DATOS PARA EDITAR
if (isset($_GET['editar'])) {
    $id_editar = intval($_GET['editar']);
    $stmt = $conn->prepare("SELECT * FROM alumnos WHERE id = ?");
    $stmt->bind_param("i", $id_editar);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($row = $resultado->fetch_assoc()) {
        $nombre = $row['nombre'];
        $apellidos = $row['apellidos'];
        $telefono_alumno = $row['telefono_alumno'];
        $telefono_tutor = $row['telefono_tutor'];
        $secundaria = $row['secundaria'];
        $colonia = $row['colonia'];
        $carrera_interes = $row['carrera_interes'];
    }
    $stmt->close();
}

// 3. GUARDAR (INSERTAR O ACTUALIZAR)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $telefono_alumno = trim($_POST['telefono_alumno']);
    $telefono_tutor = trim($_POST['telefono_tutor']);
    $secundaria = trim($_POST['secundaria']);
    $colonia = trim($_POST['colonia']);
    $carrera_interes = trim($_POST['carrera_interes']);

    if (!empty($nombre) && !empty($apellidos)) {
        if (!empty($id)) {
            // Actualizar registro existente
            $stmt = $conn->prepare("UPDATE alumnos SET nombre=?, apellidos=?, telefono_alumno=?, telefono_tutor=?, secundaria=?, colonia=?, carrera_interes=? WHERE id=?");
            $stmt->bind_param("sssssssi", $nombre, $apellidos, $telefono_alumno, $telefono_tutor, $secundaria, $colonia, $carrera_interes, $id);
        } else {
            // Insertar nuevo registro
            $stmt = $conn->prepare("INSERT INTO alumnos (nombre, apellidos, telefono_alumno, telefono_tutor, secundaria, colonia, carrera_interes) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $nombre, $apellidos, $telefono_alumno, $telefono_tutor, $secundaria, $colonia, $carrera_interes);
        }
        $stmt->execute();
        $stmt->close();
        header("Location: index.php");
        exit();
    }
}

// 4. CONSULTAR LISTA DE ALUMNOS
$alumnos = $conn->query("SELECT * FROM alumnos ORDER BY id DESC");
$total_alumnos = $alumnos ? $alumnos->num_rows : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CECyTE Jalisco - Registro de Aspirantes Plantel 24</title>
    <style>
        :root {
            --cecyte-green: #006837;
            --cecyte-dark-green: #004b28;
            --cecyte-accent: #8dc63f;
            --bg-gray: #eef2f5;
            --text-dark: #2c3e50;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--bg-gray);
            color: var(--text-dark);
        }

        /* HEADER INSTITUCIONAL CECYTE JALISCO */
        .navbar {
            background-color: var(--cecyte-green);
            color: white;
            padding: 15px 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .navbar-brand h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .navbar-brand p {
            margin: 2px 0 0 0;
            font-size: 13px;
            opacity: 0.9;
        }

        .badge-plantel {
            background-color: var(--cecyte-accent);
            color: #004b28;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 13px;
        }

        .container {
            max-width: 1050px;
            margin: 30px auto;
            padding: 0 15px;
        }

        /* TARJETAS ESTILIZADAS */
        .card {
            background: white;
            border-radius: 10px;
            padding: 25px 30px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            border-top: 5px solid var(--cecyte-green);
        }

        .card-title {
            margin-top: 0;
            color: var(--cecyte-dark-green);
            font-size: 20px;
            border-bottom: 2px solid #eef2f5;
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        label {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 6px;
            color: #4A5568;
        }

        input[type="text"], select {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid #CBD5E0;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }

        input[type="text"]:focus, select:focus {
            outline: none;
            border-color: var(--cecyte-green);
            box-shadow: 0 0 0 3px rgba(0, 104, 55, 0.15);
        }

        .btn-group {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        .btn {
            background-color: var(--cecyte-green);
            color: white;
            border: none;
            padding: 11px 22px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: background 0.2s ease;
        }

        .btn:hover {
            background-color: var(--cecyte-dark-green);
        }

        .btn-cancelar {
            background-color: #718096;
            color: white;
            text-decoration: none;
            padding: 11px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
        }

        .btn-cancelar:hover {
            background-color: #4A5568;
        }

        /* TABLA MODERNA */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 12px 14px;
            text-align: left;
            font-size: 13.5px;
            border-bottom: 1px solid #E2E8F0;
        }

        th {
            background-color: #F7FAFC;
            color: #2D3748;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background-color: #F8FAF9;
        }

        .badge-id {
            background: #E2E8F0;
            color: #2D3748;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
        }

        .btn-edit {
            background: #D69E2E;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
        }

        .btn-del {
            background: #E53E3E;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            margin-left: 4px;
        }

        .btn-edit:hover { background: #B7791F; }
        .btn-del:hover { background: #C53030; }

        .counter-badge {
            background-color: var(--cecyte-green);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 13px;
        }
    </style>
</head>
<body>

<!-- ENCABEZADO INSTITUCIONAL -->
<div class="navbar">
    <div class="navbar-brand">
        <div>
            <h1>CECyTE Jalisco</h1>
            <p>Sistema de Registro de Aspirantes a Bachillerato Técnico</p>
        </div>
    </div>
    <div class="badge-plantel">Plantel 24 Puerto Vallarta (Ixtapa)</div>
</div>

<div class="container">

    <!-- FORMULARIO DE CAPTURA / EDICIÓN -->
    <div class="card">
        <h2 class="card-title">
            <span><?php echo $id_editar ? '📝 Editar Datos de Aspirante' : '📋 Registro de Nuevo Aspirante'; ?></span>
        </h2>
        
        <form method="POST" action="index.php">
            <input type="hidden" name="id" value="<?php echo $id_editar; ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Nombre(s) del Alumno: *</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" placeholder="Ej. Juan Pablo" required>
                </div>

                <div class="form-group">
                    <label>Apellidos del Alumno: *</label>
                    <input type="text" name="apellidos" value="<?php echo htmlspecialchars($apellidos); ?>" placeholder="Ej. Gómez López" required>
                </div>

                <div class="form-group">
                    <label>Teléfono del Alumno: *</label>
                    <input type="text" name="telefono_alumno" value="<?php echo htmlspecialchars($telefono_alumno); ?>" placeholder="Ej. 3221234567" required>
                </div>

                <div class="form-group">
                    <label>Teléfono del Tutor / Padre de Familia: *</label>
                    <input type="text" name="telefono_tutor" value="<?php echo htmlspecialchars($telefono_tutor); ?>" placeholder="Ej. 3229876543" required>
                </div>

                <div class="form-group">
                    <label>Escuela Secundaria de Procedencia: *</label>
                    <select name="secundaria" required>
                        <option value="">-- Seleccionar Secundaria --</option>
                        <option value="Escuela Secundaria Técnica #56" <?php if($secundaria == 'Escuela Secundaria Técnica #56') echo 'selected'; ?>>Escuela Secundaria Técnica #56</option>
                        <option value="Escuela Secundaria Técnica #119" <?php if($secundaria == 'Escuela Secundaria Técnica #119') echo 'selected'; ?>>Escuela Secundaria Técnica #119</option>
                        <option value="Escuela Telesecundaria El Ranchito" <?php if($secundaria == 'Escuela Telesecundaria El Ranchito') echo 'selected'; ?>>Escuela Telesecundaria El Ranchito</option>
                        <option value="Escuela Telesecundaria El Colorado" <?php if($secundaria == 'Escuela Telesecundaria El Colorado') echo 'selected'; ?>>Escuela Telesecundaria El Colorado</option>
                        <option value="Escuela Secundaria General #119" <?php if($secundaria == 'Escuela Secundaria General #119') echo 'selected'; ?>>Escuela Secundaria General #119</option>
                        <option value="Escuela Secundaria Foránea #49" <?php if($secundaria == 'Escuela Secundaria Foránea #49') echo 'selected'; ?>>Escuela Secundaria Foránea #49</option>
                        <option value="Escuela Telesecundaria Las Mojoneras" <?php if($secundaria == 'Escuela Telesecundaria Las Mojoneras') echo 'selected'; ?>>Escuela Telesecundaria Las Mojoneras</option>
                        <option value="Escuela San José" <?php if($secundaria == 'Escuela San José') echo 'selected'; ?>>Escuela San José</option>
                        <option value="Escuela San Vicente" <?php if($secundaria == 'Escuela San Vicente') echo 'selected'; ?>>Escuela San Vicente</option>
                        <option value="Colegio Vasco de Gama" <?php if($secundaria == 'Colegio Vasco de Gama') echo 'selected'; ?>>Colegio Vasco de Gama</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Colonia de Procedencia (Cercana a Ixtapa): *</label>
                    <select name="colonia" required>
                        <option value="">-- Seleccionar Colonia --</option>
                        <option value="Eco Terra" <?php if($colonia == 'Eco Terra') echo 'selected'; ?>>Eco Terra</option>
                        <option value="El Ranchito" <?php if($colonia == 'El Ranchito') echo 'selected'; ?>>El Ranchito</option>
                        <option value="Las Palmas" <?php if($colonia == 'Las Palmas') echo 'selected'; ?>>Las Palmas</option>
                        <option value="Ixtapa Centro" <?php if($colonia == 'Ixtapa Centro') echo 'selected'; ?>>Ixtapa Centro</option>
                        <option value="Santo Domingo" <?php if($colonia == 'Santo Domingo') echo 'selected'; ?>>Santo Domingo</option>
                        <option value="La Desembocada" <?php if($colonia == 'La Desembocada') echo 'selected'; ?>>La Desembocada</option>
                        <option value="Llanitos" <?php if($colonia == 'Llanitos') echo 'selected'; ?>>Llanitos</option>
                        <option value="El Colorado" <?php if($colonia == 'El Colorado') echo 'selected'; ?>>El Colorado</option>
                        <option value="Las Mojoneras" <?php if($colonia == 'Las Mojoneras') echo 'selected'; ?>>Las Mojoneras</option>
                        <option value="Banus / San Ildefonso" <?php if($colonia == 'Banus / San Ildefonso') echo 'selected'; ?>>Banus / San Ildefonso</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label>Carrera Técnica de Interés: *</label>
                    <select name="carrera_interes" required>
                        <option value="">-- Seleccionar Carrera Técnica --</option>
                        <option value="Técnico en Programación" <?php if($carrera_interes == 'Técnico en Programación') echo 'selected'; ?>>Técnico en Programación</option>
                        <option value="Técnico en Mantenimiento" <?php if($carrera_interes == 'Técnico en Mantenimiento') echo 'selected'; ?>>Técnico en Mantenimiento</option>
                        <option value="Técnico en Inteligencia Artificial (IA)" <?php if($carrera_interes == 'Técnico en Inteligencia Artificial (IA)') echo 'selected'; ?>>Técnico en Inteligencia Artificial (IA)</option>
                    </select>
                </div>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn"><?php echo $id_editar ? '💾 Actualizar Registro' : '✅ Guardar Aspirante'; ?></button>
                <?php if ($id_editar): ?>
                    <a href="index.php" class="btn-cancelar">Cancelar Editado</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- TABLA DE ALUMNOS REGISTRADOS -->
    <div class="card">
        <h2 class="card-title">
            <span>🎓 Aspirantes Registrados</span>
            <span class="counter-badge">Total: <?php echo $total_alumnos; ?></span>
        </h2>

        <table>
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Aspirante</th>
                    <th>Tel. Alumno</th>
                    <th>Tel. Tutor</th>
                    <th>Secundaria</th>
                    <th>Colonia</th>
                    <th>Carrera de Interés</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total_alumnos > 0): ?>
                    <?php while ($row = $alumnos->fetch_assoc()): ?>
                        <tr>
                            <td><span class="badge-id">#<?php echo str_pad($row['id'], 4, "0", STR_PAD_LEFT); ?></span></td>
                            <td><strong><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellidos']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['telefono_alumno']); ?></td>
                            <td><?php echo htmlspecialchars($row['telefono_tutor']); ?></td>
                            <td><?php echo htmlspecialchars($row['secundaria']); ?></td>
                            <td><?php echo htmlspecialchars($row['colonia']); ?></td>
                            <td><?php echo htmlspecialchars($row['carrera_interes']); ?></td>
                            <td>
                                <a href="index.php?editar=<?php echo $row['id']; ?>" class="btn-edit">Editar</a>
                                <a href="index.php?eliminar=<?php echo $row['id']; ?>" class="btn-del" onclick="return confirm('¿Seguro que deseas eliminar el registro de este aspirante?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: #718096; padding: 20px;">No hay aspirantes registrados actualmente.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>