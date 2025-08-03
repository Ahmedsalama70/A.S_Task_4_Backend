<?php
$errors = [];
$success_message = "";
$uploaded_image_filename = null;


if ($_SERVER["REQUEST_METHOD"] == "POST") {




    if (empty($_POST["name"])) {
        $errors[] = "Name required";
    } else {
        $name = ["name"];
    }

    if (empty($_POST["email"])) {
        $errors[] = "Email required";
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    } else {
        $email = $_POST["email"];
    }

    if (empty($_POST["password"])) {
        $errors[] = "Password required";
    } else {
        $password = $_POST["password"];
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]).{8,}$/', $password)) {
            $errors[] = "The password is weak. It must contain at least 8 characters, including an uppercase letter, a lowercase letter, a number, and a special character.";
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        }
    }

    $target_dir = "myImage/";

    if (!is_dir($target_dir)) {

        if (!mkdir($target_dir, 0777, true)) {
            $errors[] = "Failed to create image upload directory.";
        }
    }

    if (empty($errors)) {
        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) {
            $file_name = $_FILES["image"]["name"];
            $file_tmp_name = $_FILES["image"]["tmp_name"];
            $file_size = $_FILES["image"]["size"];
            $file_type = $_FILES["image"]["type"];
            move_uploaded_file($file_tmp_name, $target_dir . $file_name);

            $allowed_extensions = array("jpg", "jpeg", "png", "gif");
            $max_file_size = 5 * 1024 * 1024;

            if (!in_array($file_ext, $allowed_extensions)) {
                $errors[] = "File type not allowed. Please upload an image.(JPG, JPEG, PNG, GIF).";
            }
            if ($file_size > $max_file_size) {
                $errors[] = "Image size is too large. Maximum 5MB.";
            }

            if (empty($errors)) {
                $new_file_name = uniqid('img_', true) . '.' . $file_ext;
                $target_file = $target_dir . $new_file_name;

                if (move_uploaded_file($file_tmp_name, $target_file)) {
                    $uploaded_image_filename = $new_file_name;
                } else {
                    $errors[] = "An error occurred while loading the image. Please try again.";
                }
            }
        } else {
            if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_NO_FILE) {
                $errors[] = "Please select a profile image.";
            } else if (isset($_FILES["image"])) {
                $errors[] = "Error loading image: " . $_FILES["image"]["error"];
            } else {
                $errors[] = "Please select a profile image.";
            }
        }
    }


    if (empty($errors)) {


        $success_message = "Registration successful! (This part requires a server and database)
";

        $_POST = array();
        $_FILES = array();

    }
}
?>
<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RegistrationForm</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            direction: rtl;
            text-align: right;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
            margin: 0;
        }

        .container {
            background-color: #a5f580ff;
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 28rem;
        }

        h2 {
            font-size: 2.25rem;
            font-weight: 700;
            text-align: center;
            color: #1f2937;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
        }

        .form-input {
            width: calc(100% - 2rem);
            padding: 0.75rem 1rem;
            border: 1px solid #D1D5DB;
            border-radius: 0.5rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #1F2937;
            background-color: #F9FAFB;
            box-sizing: border-box;
        }

        .form-input:focus {
            outline: none;
            border-color: #6366F1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        }

        .error-message {
            color: #EF4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }

        .success-message {
            color: #22C55E;
            font-size: 1rem;
            margin-top: 1rem;
            text-align: center;
        }

        .submit-button {
            width: 100%;
            background-color: #6366F1;
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.1);
        }

        .submit-button:hover {
            background-color: #4138ecff;
        }

        .php-message-container {
            text-align: center;
            margin-top: 1rem;
        }

        .flex-center {
            display: flex;
            justify-content: center;
        }

        .image-display {
            max-width: 200px;
            max-height: 200px;
            border-radius: 0.5rem;
            margin-top: 1rem;
            display: block;
            object-fit: cover;
            border: 2px solid #6366F1;
            padding: 5px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>

<body class="">
    <div class="container">
        <h2>Create a new account</h2>
        <form id="registrationForm" action="index.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name" class="form-label">Full name</label>
                <input type="text" id="name" name="name" class="form-input"
                    value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-input"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-input">

            </div>

            <div class="form-group">
                <label for="image" class="form-label">Upload profile picture
                </label>

                <input type="file" id="image" name="image" accept="image/*" class="form-input">

            </div>
            <button type="submit" class="submit-button">Register</button>
            <div class="php-message-container">
                <?php if (!empty($errors)): ?>
                    <div class="error-message">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php elseif (!empty($success_message)): ?>
                    <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
                <?php endif; ?>
            </div>
        </form>

        <?php if (!empty($uploaded_image_filename)): ?>
            <h3 class="text-xl font-bold text-center text-gray-800 mt-8 mb-4">Saved image:</h3>
            <div class="flex-center">
                <img src="<?php echo htmlspecialchars($target_dir . $uploaded_image_filename); ?>" alt="Saved image"
                    class="image-display">
            </div>
        <?php endif; ?>
    </div>
</body>

</html>