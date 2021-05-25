<?php
$cwd = str_replace('\\', '/', getcwd());
session_start();
define('DB_NAME', $cwd . '/data/db.txt');
function seed() {
    $data = [
        [
            'id'    => 1,
            'fname' => 'Kamal',
            'lname' => 'Ahmed',
            'roll'  => '11',
        ],
        [
            'id'    => 2,
            'fname' => 'Jamal',
            'lname' => 'Ahmed',
            'roll'  => '12',
        ],
        [
            'id'    => 3,
            'fname' => 'Ripon',
            'lname' => 'Ahmed',
            'roll'  => '9',
        ],
        [
            'id'    => 4,
            'fname' => 'Nikhil',
            'lname' => 'Chandra',
            'roll'  => '8',
        ],
        [
            'id'    => 5,
            'fname' => 'John',
            'lname' => 'Rozario',
            'roll'  => '9',
        ],
    ];

    $serializedData = serialize($data);
    file_put_contents(DB_NAME, $serializedData, LOCK_EX);
}

function generateReport() {
    $serializedData = file_get_contents(DB_NAME);
    $students = unserialize($serializedData);
?>
    <table>
        <tr>
            <th>Name</th>
            <th>Roll</th>
            <?php if (hasPrivilege()) : ?>
                <th>Action</th>
            <?php endif; ?>
        </tr>
        <?php foreach ($students as $student) : ?>
            <tr>
                <td><?php printf("%s %s", $student['fname'], $student['lname']) ?></td>
                <td><?php echo $student['roll']; ?></td>
                <?php if (isAdmin()) : ?>
                    <td><?php printf("<a href='index.php?task=edit&id=%s'>Edit | </a> <a class='delete' href='index.php?task=delete&id=%s'>Delete</a>", $student['id'], $student['id']) ?></td>
                <?php elseif (isEditor()) : ?>
                    <td><?php printf("<a href='index.php?task=edit&id=%s'>Edit</a>", $student['id']) ?></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>
<?php
}

function addStudent($fname, $lname, $roll) {
    $found = false;
    $unserializedData = file_get_contents(DB_NAME);
    $students = unserialize($unserializedData);
    foreach ($students as $_student) {
        if ($_student['roll'] == $roll) {
            $found = true;
            break;
        }
    }
    if (!$found) {
        $newId = getNewId($students);
        $newStudent = [
            'id'    => $newId + 1,
            'fname' => $fname,
            'lname' => $lname,
            'roll'  => $roll,
        ];

        array_push($students, $newStudent);

        $serializedData = serialize($students);
        file_put_contents(DB_NAME, $serializedData, LOCK_EX);
        return true;
    }
    return false;
}

function getStudent($id) {
    $unserializedData = file_get_contents(DB_NAME);
    $students = unserialize($unserializedData);
    foreach ($students as $student) {
        if ($student['id'] == $id) {
            return $student;
        }
    }
    return false;
}

function updateStudent($id, $fname, $lname, $roll) {
    $found = false;
    $unserializedData = file_get_contents(DB_NAME);
    $students = unserialize($unserializedData);

    foreach ($students as $_student) {
        if ($_student['id'] == $id && $_student['roll'] != $roll) {
            $found = true;
        }
    }

    if (!$found) {
        $students[$id - 1]['fname'] = $fname;
        $students[$id - 1]['lname'] = $lname;
        $students[$id - 1]['roll'] = $roll;

        $serializedData = serialize($students);
        file_put_contents(DB_NAME, $serializedData, LOCK_EX);
        return true;
    }
    return false;
}

function deleteStudent($id) {
    $unserializedData = file_get_contents(DB_NAME);
    $students = unserialize($unserializedData);

    unset($students[$id - 1]);
    $serializedData = serialize($students);
    file_put_contents(DB_NAME, $serializedData, LOCK_EX);

    return true;
}

function getNewId($students) {
    $maxId = max(array_column($students, 'id'));
    return $maxId + 1;
}

function isAdmin() {
    if (isset($_SESSION['role'])) {
        return ('admin' == $_SESSION['role']);
    }
}

function isEditor() {
    if (isset($_SESSION['role'])) {
        return ('editor' == $_SESSION['role']);
    }
}

function hasPrivilege() {
    return (isAdmin() || isEditor());
}

?>