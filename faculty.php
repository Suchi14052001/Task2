<?php
session_start();
if(!isset($_SESSION['user'])){
	header('location: login.php');
	die();
}//open this only if user session variable is set
?>
<!DOCTYPE html>
<html>
<?php
include("connection.php");
if(isset($_POST['SignOut'])){
		unset($_SESSION['user']);
		unset($_SESSION['pass']);
		unset($_SESSION['role']);
		session_destroy();
		setcookie("username", "", time() - 3600);
		echo "<script>window.location.href = 'index.php'</script>";
	}//delete the session variable on clicking on sign out button
	
if(isset($_POST['upsyll'])){
	$id = intval($conn -> real_escape_string($_POST['uid']));
	$target_dir = "C:/data/";//all the uploaded files are stored there
	$sy = htmlspecialchars(basename($_FILES["syl"]["name"]));
	$us = intval($_SESSION['user']);
	$sql = "select * from course where cid='$id'";//check if that course exists or not
	$result = mysqli_query($conn, $sql);  
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);  
	$count = mysqli_num_rows($result);
	if($count==1){
		$sql = "select * from course where cid='$id' and fno = '$us'";
		$result = mysqli_query($conn, $sql);  
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);  
		$count = mysqli_num_rows($result);
		if($count==1){
			$sql = "select syllabus from course where cid='$id' and fno='$us'";
			$result = mysqli_query($conn, $sql);  
			$row = mysqli_fetch_assoc($result);
			$target_file = $target_dir . $sy;
			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
			if (file_exists($target_file)) {
				$uploadOk = 2;
			}
			if($imageFileType != "pdf" ) {
				$uploadOk = 3;
			}
			if($uploadOk == 2){
				echo "<script>alert('File already exists!');</script>";
			}else if($uploadOk == 3){
				echo "<script>alert('Only pdf filetype is accepted!');</script>";
			}else{
				if (move_uploaded_file($_FILES["syl"]["tmp_name"], $target_file)) {
					$sql3 = "update course set syllabus='$sy' where cid='$id' and fno='$us'";//update syllabus
					if ($conn->query($sql3) === TRUE) {
						if($conn -> affected_rows == 1){
							unlink("C:/data/".$row['syllabus']);//delete the previous syllabus file
							$sql = "update learning set value=1 where cno='$id'";
							if ($conn->query($sql) === TRUE) {
								echo "<script>alert('Syllabus is updated and all the students are notified');</script>";
							}else{
								echo "<script>alert('Some error occured!');</script>";
							}
						}else{
							echo "<script>alert('No records updated!');</script>";
						}
					}else{
						$s4 = "Error! ";
						$s5 = $conn->error;
						$s6 = $s4.$s5;
						echo '<script>
							alert("'.$s6.'");
						</script>';
					}
				}else{
					echo "<script>alert('Error uploading syllabus!');</script>";
				}
			}
		}else{
			echo "<script>alert('You are not teaching this course');</script>";
		}
	}else{
		echo "<script>alert('You have entered a Invalid Course ID');</script>";
	}
	//$sql = "select syllabus from course where cid";
	//unlink("C:/data/".$sy);
	
}

if(isset($_POST['up'])){
	$oldpa = sha1($conn -> real_escape_string($_POST['uid']));
	$newpa = sha1($conn -> real_escape_string($_POST['uname']));
	$us = intval($conn -> real_escape_string($_SESSION['user']));
	$sql = "select password from faculty where fid='$us'";//check if entered old password is same as that in that database
	$result = mysqli_query($conn, $sql);  
	$row = mysqli_fetch_assoc($result);
	if($row['password']==$oldpa){
		$sql = "update faculty set password='$newpa' where fid='$us'";
		if ($conn->query($sql) === TRUE) {
			if($conn -> affected_rows == 1){
				echo "<script>alert('Password updated successfully');</script>";
			}else{
				echo "<script>alert('No records updated');</script>";
			}
		}else{
			$s4 = "Error! ";
			$s5 = $conn->error;
			$s6 = $s4.$s5;
			echo '<script>
				alert("'.$s6.'");
			</script>';
		}
	}else{
		echo "<script>alert('Entered old password is incorrect! Please retry!');</script>";
	}
}
if(isset($_POST['Contact'])){
	echo "<script> window.location.href='mail.php';</script>";
}
	
?>
<head>
<title> Faculty dashboard </title>
<style>
.tab{
	width: 1180px;  
        overflow: hidden;  
        margin: auto;
        margin: 50px 50px;  
        padding: 30px;  
        background: #00539CFF;  
        border-radius: 40px ;  
        font-family: Verdana;
		color:white;
}
table, th, td {
  border: 1px solid white;
  border-collapse: collapse;
  padding: 15px;
  text-align:center;
}
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  text-decoration: underline;
  padding: 10px 27px;
  transition: 0.3s;
  font-size: 17px;
  color:white;
  margin: auto;
        margin: 10px 10px;
}
.tab button:hover {
  background-color: powderblue;
  color:black;
  font-weight:bolder;
}
.tabcontent {
  display: none;
  border-top: none;
  width: 662px;  
        overflow: hidden;  
        margin: auto;
        margin: 60px 200px;  
        padding: 60px 50px 30px 240px;  
        background: #00539CFF;  
        border-radius: 15px ;  
        font-family: Verdana;
		color:white;
}
.tabcontent1 {
  display: none;
  border-top: none;
  width: 662px;  
        overflow: hidden;  
        margin: auto;
        margin: 60px 260px;  
        padding: 60px 50px 50px 50px;  
        background: #00539CFF;  
        border-radius: 15px ;  
        font-family: Verdana;
		color:white;
}
#uname, #pass, #name, #phone, #role, #uid, #uc, #syl{
	width: 220px;  
    height: 30px;  
    border: none;  
    border-radius: 3px;  
padding-left: 8px; 
}
#log, #inc, #instc, #inftc, #rms, #rmf, #rmc,#upsyll,#up{
	width: 300px;  
    height: 30px;  
    border: none;  
    border-radius: 17px;  
    color: blue;  
	cursor:pointer;
	margin: 50px 50px;  
	font-weight: bold;
}
</style>
</head>
<body style="background-color:#FFD662FF;">
<div class="tab">
<br>
<span style="font-size:34px;"> <b>FACULTY DASHBOARD</b></span>
<span class="tablinks" style="float: right;"> WELCOME, <?php 

include("connection.php");
$temp = $_SESSION['user'];
$sql = "select fname from faculty where fid='$temp'";
$result = mysqli_query($conn,$sql);
$row=mysqli_fetch_assoc($result);
echo $row['fname'];
?>! </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>
<form method="post" action="faculty.php">
<button type="submit" id="SignOut" name="SignOut" class="tablinks" style="float: right;" >SIGN OUT</button><br><br><br><br>
</form>
<form method="post" action="student.php">
<button type="submit" id="Contact" name="Contact" class="tablinks" style="float: right;" >Contact form</button><br><br><br><br>
</form>
<button class="tablinks" onclick="openForm(event, 'ViewCourses')">View your courses</button>
<button class="tablinks" onclick="openForm(event, 'ViewStudents')">View students in each course</button>
<button class="tablinks" onclick="openForm(event,'UpdateSyllabus')"> Update syllabus</button>
<button class="tablinks" onclick="openForm(event,'ChangePassword')"> Change your password</button>

</div>


<div id="ViewCourses"  class="tabcontent1">
<form>
<input style="width: 220px;  
    height: 30px;  
    border: none;  
    border-radius: 3px;  
padding-left: 8px; margin-bottom:30px;" type="text" placeholder="Enter Course ID" id="numc">&nbsp;&nbsp;&nbsp;

<input type="button" onclick="show('cou','numc','res2','f')" name="vc" id="vc" style="width: 100px;  
    height: 30px;  
    border: none;  
    border-radius: 17px;  
    color: blue;  
	cursor:pointer;
	font-weight: bold;" Value="Search"><br> 
</form>
<div id="res2"> Click on search button without entering anything, to view your courses<br> For viewing a specific record, enter Course ID, then click on search button</div>
</div>

<div id="ViewStudents" class="tabcontent1">
<form>
<input style="width: 220px;  
    height: 30px;  
    border: none;  
    border-radius: 3px;  
padding-left: 8px; margin-bottom:30px;" type="text" placeholder="Enter Course ID" id="nums">&nbsp;&nbsp;&nbsp;

<input type="button" onclick="show3('stu','nums','res3','f')" name="v" id="v" style="width: 100px;  
    height: 30px;  
    border: none;  
    border-radius: 17px;  
    color: blue;  
	cursor:pointer;
	font-weight: bold;" Value="Search"><br> 
</form>
<div id="res3"> Enter Course ID and click on search to view students of that course</div>
</div>


<div id="UpdateSyllabus" class="tabcontent">
<form name="f3" method="post" action="faculty.php" enctype="multipart/form-data">
    <label style="color:white;"><b>Course ID</b></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="text" name="uid" id="uid" placeholder="Enter course ID" required><br><br><br>
<label style="color:white;"><b>Upload syllabus</b></label>&nbsp;&nbsp;
<input type="file" name="syl" id="syl" placeholder="Enter course name" required><br>
<input type="submit" value="Update Syllabus and Notify Students" name="upsyll" id="upsyll">
</form>
</div>

<div id="ChangePassword" class="tabcontent">
<form name="f4" method="post" action="faculty.php">
<label style="color:white;"><b>Enter previous password</b></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="password" name="uid" id="uid" placeholder="Enter previous password" required><br><br><br>
<label style="color:white;"><b>Set your new password</b></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="password" name="uname" id="uname" placeholder="Set your new password" required>
<input type="submit" value="Update password" name="up" id="up">
</form>
</div>

<script>
function openForm(evt, divName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
tabcontent1 = document.getElementsByClassName("tabcontent1");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  for (i = 0; i < tabcontent1.length; i++) {
    tabcontent1[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(divName).style.display = "block";
  evt.currentTarget.className += " active";
  document.getElementById('numc').value = "";
document.getElementById("res2").innerHTML = "Click on search button without entering anything, to view all your records<br> For viewing a specific record, enter course ID, then click on search button";
  
}

function show(role,num,r,e){
	str =  document.getElementById(num).value;
		if(window.XMLHttpRequest){
			xmlhttp = new XMLHttpRequest();
		}else{
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange = function(){
			if(this.readyState == 4 && this.status == 200){
				document.getElementById(r).innerHTML = this.responseText;
				var table = document.getElementById('couTable');
	var temp=1;
	for(var i=1;i<table.rows.length;i++){
		var cells = table.rows[i];
		cells.getElementsByTagName('button')[0].id=temp.toString();
		temp=temp+1;
	}
	temp=1;
			}
			
		};
		xmlhttp.open("GET","function.php?q="+str+"&role="+role+"&ekk="+e,true);
		xmlhttp.send();
		
	}//using ajax to print the table
	
	function show3(role,num,r,e){
	str =  document.getElementById(num).value;
		if(window.XMLHttpRequest){
			xmlhttp = new XMLHttpRequest();
		}else{
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange = function(){
			if(this.readyState == 4 && this.status == 200){
				document.getElementById(r).innerHTML = this.responseText;
			}
			
		};
		xmlhttp.open("GET","function.php?q="+str+"&role="+role+"&ekk="+e,true);
		xmlhttp.send();
		
	}
	
	
	function show1(id)
            {
				if(window.XMLHttpRequest){
					xmlhttp = new XMLHttpRequest();
				}else{
					xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						document.getElementById('mm').innerHTML = this.responseText;
					}
				};
				xmlhttp.open("GET","function2.php?q="+id+"&cond="+'fac',true);
		xmlhttp.send();
			}//using ajax to send download request
</script>
</body>
</html>