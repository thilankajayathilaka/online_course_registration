<?php
session_start();
include('includes/config.php');
error_reporting(0);
if (strlen($_SESSION['login']) == 0) {
  header('location:index.php');
} else {

  if (isset($_POST['submit'])) {
    $regid = intval($_GET['id']);
    $studentname = $_POST['studentname'];
    $photo = $_FILES["photo"]["name"];
    $cgpa = $_POST['cgpa'];

    // Allowed file types
    $allowed = array("jpg", "jpeg", "png", "gif");

    // File type validation
    $ext = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
    if (!in_array($ext, $allowed)) {
      echo "Error: Invalid file type. Only jpg, jpeg, png and gif are allowed.";
      exit();
    }

    // File size validation
    if ($_FILES["photo"]["size"] > 5000000) {
      echo "Error: File size must be less than 5MB.";
      exit();
    }

    // File name validation
    $file_name = $_FILES["photo"]["name"];
    if (preg_match("/[^a-zA-Z0-9._-]/", $file_name)) {
      echo "Error: Only alphanumeric characters, dots, underscores and dashes are allowed in the file name.";
      exit();
    }

    // File path validation
    $upload_path = "studentphoto/";
    $upload_path = realpath($upload_path) . '/';
    $allowedpath = "studentphoto/";
    if (strpos($$allowedpath, $upload_path) === 0) {
      echo "Error: Upload directory traversal detected.";
      exit();
    }

    // Move the uploaded file
    move_uploaded_file($_FILES["photo"]["tmp_name"], $upload_path . $file_name);


    move_uploaded_file($_FILES["photo"]["tmp_name"], "studentphoto/" . $_FILES["photo"]["name"]);

    //$ret=mysqli_query($con,"update students set studentName='$studentname',studentPhoto='$photo',cgpa='$cgpa'  where StudentRegno='$regid'");
    $stmt = $con->prepare("UPDATE students SET studentName = ?, studentPhoto = ?, cgpa = ? WHERE StudentRegno = ?");
    $stmt->bind_param("ssds", $studentname, $photo, $cgpa, $regid);
    $stmt->execute();

    if ($stmt) {
      $_SESSION['msg'] = "Student Record updated Successfully !!";
    } else {
      $_SESSION['msg'] = "Error : Student Record not update";
    }
  }
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Student Profile</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
</head>

<body>
    <?php include('includes/header.php'); ?>
    <!-- LOGO HEADER END-->
    <?php if ($_SESSION['login'] != "") {
      include('includes/menubar.php');
    }
    ?>
    <!-- MENU SECTION END-->
    <div class="content-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="page-head-line">Student Registration </h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Student Registration
                        </div>
                        <font color="green" align="center">
                            <?php echo htmlentities($_SESSION['msg']); ?><?php echo htmlentities($_SESSION['msg'] = ""); ?>
                        </font>
                        <?php
              $regid = intval($_GET['id']);

              $sql = mysqli_query($con, "select * from students where StudentRegno='$regid'");
              $cnt = 1;
              while ($row = mysqli_fetch_array($sql)) { ?>

                        <div class="panel-body">
                            <form name="dept" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="studentname">Student Name </label>
                                    <input type="text" class="form-control" id="studentname" name="studentname"
                                        value="<?php echo htmlentities($row['studentName']); ?>" />
                                </div>

                                <div class="form-group">
                                    <label for="studentregno">Student Reg No </label>
                                    <input type="text" class="form-control" id="studentregno" name="studentregno"
                                        value="<?php echo htmlentities($row['StudentRegno']); ?>"
                                        placeholder="Student Reg no" readonly />

                                </div>



                                <div class="form-group">
                                    <label for="Pincode">Pincode </label>
                                    <input type="text" class="form-control" id="Pincode" name="Pincode" readonly
                                        value="<?php echo htmlentities($row['pincode']); ?>" required />
                                </div>

                                <div class="form-group">
                                    <label for="CGPA">CGPA </label>
                                    <input type="text" class="form-control" id="cgpa" name="cgpa"
                                        value="<?php echo htmlentities($row['cgpa']); ?>" required />
                                </div>


                                <div class="form-group">
                                    <label for="studentphoto">Student Photo </label>
                                    <?php if ($row['studentPhoto'] == "") { ?>
                                    <img src="../studentphoto/noimage.png" width="200" height="200"><?php } else { ?>
                                    <img src="../studentphoto/<?php echo htmlentities($row['studentPhoto']); ?>"
                                        width="200" height="200">
                                    <?php } ?>
                                </div>
                                <div class="form-group">
                                    <label for="studentphoto">Upload New Photo </label>
                                    <input type="file" class="form-control" id="photo" name="photo"
                                        value="<?php echo htmlentities($row['studentPhoto']); ?>" />
                                </div>


                                <?php } ?>

                                <button type="submit" name="submit" id="submit" class="btn btn-default">Update</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

        </div>





    </div>
    </div>
    <?php include('includes/footer.php'); ?>
    <script src="assets/js/jquery-1.11.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>


</body>

</html>
<?php } ?>