<?php
require_once "./inc/functions.php";
$info = '';
$errors = [];
$task = $_GET['task'] ?? 'report';
$duplicateError = 0;
if ('edit' == $task) {
    if (!hasPrivilege()) {
        header('location: index.php?task=report');
        return;
    }
}
if ('delete' == $task) {
    if (!isAdmin()) {
        header('location: index.php?task=report');
        return;
    }
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
    if ($id > 0) {
        $delete = deleteStudent($id);
        if ($delete) {
            header('location: index.php?task=report');
        }
    }
}
if ('seed' == $task) {
    if (!isAdmin()) {
        header('location: index.php?task=report');
        return;
    }
    seed();
    $info = "seeding is complete";
}

$fname = '';
$lname = '';
$roll = '';

// Form submission 
if (isset($_POST['submit'])) {

    // Sanitize all form input
    $fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
    $lname = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_STRING);
    $roll = filter_input(INPUT_POST, 'roll', FILTER_SANITIZE_STRING);
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);

    // check inpute value empty or not (minimal validation)
    if (empty($fname)) {
        array_push($errors, 'First Name can\'t be empty!');
    }
    if (empty($lname)) {
        array_push($errors, 'Last Name can\'t be empty!');
    }
    if (empty($roll)) {
        array_push($errors, 'Age can\'t be empty!');
    }

    if ($id) {
        // Update the existing student

        if (!empty($fname) && !empty($lname) && !empty($roll)) {
            $result = updateStudent($id, $fname, $lname, $roll);
            if ($result) {
                header('location: index.php?task=report');
            } else {
                $duplicateError = 1;
            }
        }
    } else {
        // Add a new student

        if (!empty($fname) && !empty($lname) && !empty($roll)) {
            $result = addStudent($fname, $lname, $roll);
            if ($result) {
                header('location: index.php?task=report');
            } else {
                $duplicateError = 1;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>From Example</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.css">
    <style>
        body {
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="column column-60 column-offset-20">
                <h2>CRUD Project</h2>
                <p>A sample project to perform CRUD operations using plain files and PHP</p>
                <hr>
                <?php include_once './inc/templates/nav.php' ?>
                <br>
                <?php
                if ($info) :
                ?>
                    <div class="row">
                        <div class="column column-60 column-offset-20">
                            <blockquote><?php echo $info; ?></blockquote>
                        </div>
                    </div>
                <?php
                endif;
                ?>
            </div>
        </div>

        <?php if ('report' == $task) : ?>
            <div class="row">
                <div class="column column-60 column-offset-20">
                    <?php generateReport() ?>
                </div>
            </div>
        <?php endif; ?>


        <?php if ($errors) : ?>
            <div class="row">
                <div class="column column-60 column-offset-20">
                    <blockquote>
                        <?php
                        foreach ($errors as $_error) {
                            echo $_error . "<br>";
                        }
                        ?>
                    </blockquote>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($duplicateError === 1) : ?>
            <div class="row">
                <div class="column column-60 column-offset-20">
                    <blockquote>Duplicate Roll Number</blockquote>
                </div>
            </div>
        <?php endif; ?>


        <?php if ('add' == $task) : ?>
            <div class="row">
                <div class="column column-60 column-offset-20">
                    <form action="./index.php?task=add" method="POST">
                        <label for="fname">First Name</label>
                        <input type="text" name="fname" id="fname" value="<?php echo $fname; ?>">

                        <label for="lname">Last Name</label>
                        <input type="text" name="lname" id="lname" value="<?php echo $lname; ?>">

                        <label for="roll">Roll</label>
                        <input type="number" name="roll" id="roll" value="<?php echo $roll; ?>">

                        <button type="submit" class="button-primary" name="submit">Submit</button>

                    </form>
                </div>
            </div>
        <?php endif; ?>

        <?php
        if ('edit' == $task) :
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
            $student = getStudent($id);
            if ($student) :
        ?>
                <div class="row">
                    <div class="column column-60 column-offset-20">
                        <form action="" method="POST">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <label for="fname">First Name</label>
                            <input type="text" name="fname" id="fname" value="<?php echo $student['fname']; ?>">

                            <label for="lname">Last Name</label>
                            <input type="text" name="lname" id="lname" value="<?php echo $student['lname']; ?>">

                            <label for="roll">Roll</label>
                            <input type="number" name="roll" id="roll" value="<?php echo $student['roll']; ?>">

                            <button type="submit" class="button-primary" name="submit">Update</button>

                        </form>
                    </div>
                </div>
        <?php
            endif;
        endif;
        ?>
    </div>
    <script src="./asstes/js/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".delete").click(function() {
                if (!confirm("Do you want to delete")) {
                    return false;
                }
            });
        });
    </script>
</body>