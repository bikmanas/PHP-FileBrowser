<?php
//Login: 
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FileBrowser</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Path for reading current and new directories/files: -->
    <div class="container">
        <?php
        $rootDir = __DIR__;
        $diff = '';
        if (!empty($_GET['path'])) {
            $currentPath = $rootDir . $_GET['path'];
            $diff = str_replace($rootDir, '', $currentPath);
        } else {
            $currentPath = $rootDir;
            $diff = '/';
        }

        // How to create a directory: 
        if (isset($_POST['submit'])) {
            $directoryName = $_POST["directoryName"];
            if (is_dir($rootDir . (isset($_GET['path']) ? $_GET['path'] : '') . '/' . $directoryName)) {
                $message = 'Directory ' . $directoryName . ' already exists!';
            } else if ($directoryName == "") {
                $message = 'Enter name for new directory!';
            } else {
                mkdir($currentPath . '/' . $directoryName);
                $message = 'Directory ' . $directoryName . ' was succesfuly created!';
            }
            echo $message;
        }

        // How to delete a file: 
        if (isset($_POST['delete'])) {
            if (empty($_GET['path'])) {
                unlink($_POST['delete']);
                echo 'You deleted the file!';
            } else {
                unlink($currentPath . '/' . $_POST['delete']);
                echo 'You deleted the file!';
            }
        }
        // How to upload a file: 
        if (isset($_FILES['image'])) {
            $errors = array();

            $file_name = $_FILES['image']['name'];
            $file_size = $_FILES['image']['size'];
            $file_tmp = $_FILES['image']['tmp_name'];
            $file_type = $_FILES['image']['type'];

            // File extension check:
            $explode = explode('.', $_FILES['image']['name']);
            $end = end($explode);
            $file_ext = strtolower($end);

            $extensions = array("jpeg", "jpg", "png");
            if (in_array($file_ext, $extensions) === false) {
                $errors[] = "Extension is not allowed, please choose a JPEG or PNG file.";
            }
            if ($file_size > 2097152) {
                $errors[] = 'File size must be exactly 2 MB';
            }
            if (empty($errors) == true) {
                move_uploaded_file($file_tmp, './' . $diff . '/' . $file_name);
                echo "Uploaded successfully!";
            } else {
                echo 'Extension is not allowed, please choose a JPEG or PNG file.';
            }
        }
        //How to download a file:
        if (isset($_POST['download'])) {
            $file = $currentPath . '/' . $_POST['download'];
            $fileToDownloadEscaped = $file;
            ob_clean();
            ob_start();
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename=' . basename($fileToDownloadEscaped));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fileToDownloadEscaped));
            ob_end_flush();

            readfile($fileToDownloadEscaped);
            exit;
        }

        echo "<h1>Content: $diff</h1>";
        $files = array_slice(scandir($currentPath), 2);
        echo '<table class="table table-striped">
                <tr class="cnames">
                    <td>Type</td>
                    <td>Name</td>
                    <td>Actions</td>
                </tr>';
        for ($i = 0; $i < count($files); $i++) {
            $name = $files[$i];
            if (is_dir($rootDir . $diff . '/' . $name)) {
                if ($diff === '/') {
                    $noSpace = urlencode($diff . $name);
                } else {
                    $noSpace = urlencode($diff . '/' . $name);
                }
                $type = 'Directory';
                $name = "<a href=\"index.php?path=$noSpace\">$name</a>";
                $actions = '';
            } else {
                $type = 'File';
                $actions = '<form method="POST">
                                <button type="submit" class="btn-delete" name="delete" value="' . $name . '">Delete</button>
                            </form></br>
                            <form action="" method="POST">
                                <button type="submit" class="btn-download" name="download" value="' . $name . '">Download</button>
                            </form>';
            }
            echo '<tr>
                        <td>' . $type . '</td>
                        <td>' . $name . '</td>
                        <td>' . $actions . '</td>
                    </tr>';
        }
        echo '</table>';
        if ($rootDir != $currentPath) {
            $back = '?path=' . dirname($diff);
            if (dirname($diff) == '/') {
                $back = '';
            }
            echo '<button class="back"><a href="index.php' . $back . '">Back</a></button>';
        }
        ?>
    </div>

    <!-- How to create new directory form: -->
    <div class="form">
        <form method="POST">
            <label>Create a new directory here</label>
            <input type="text" name="directoryName" placeholder="Enter directory name" />
            <button class="subDir" type="submit" name="submit">Submit</button>
        </form>
    </div>

    <!-- Upload file form: -->
    <div class="form">
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="file" name="image" />
            <input class="submitF" type="submit" />
        </form>
    </div>
    <div class="logout">Click <a href="login.php?action=logout"> here to logout

    </div>
</body>

</html>