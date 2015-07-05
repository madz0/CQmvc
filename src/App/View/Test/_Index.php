<?php

?>

<html>
<body>

<fieldset>
<legend><b>Form1</b></legend>
This simply gets user's name and his children and post the data
<form action="/Test/postTest" method="post">
id <input name="id" /> <br /> 
Name <input name="myModel->name" /><br />
children[0] <input name="myModel->children{0}" /><br />
children[1] <input name="myModel->children{1}" /><br />
<input type="submit" value="post" />
</form>
</fieldset>
<br /><br />
<fieldset>
<legend><b>Form2</b></legend>
This form shows uploading one or multiple files related to a model
<form action="/Test/uploadTest" method="post" enctype="multipart/form-data" >

Profile Image <input name="myModel->profileImage" type="file" /><br />
<br />File1 <input name="myModel->postedFiles{0}" type="file" /><br />
File2 <input name="myModel->postedFiles{1}" type="file" /><br />
<input type="submit" value="post" />
</form>
</fieldset>
<br /><br />
<fieldset>
<legend><b>Form3</b></legend>
This form shows a complex form posting and file uploading. In this form, some users with their names
and their children and thire profile image and their uploaded files will be posted
to an action in controller. As you noticed, an object graph is created in this very form and
post it to an action in a controller.

<form action="/Test/complexFormTest" method="post" enctype="multipart/form-data" >

<fieldset>
<legend>User1</legend>
	Name <input name="myModels{0}->name" /></br>
	children{0}->name <input name="myModels{0}->children{0}->name" /><br />
	children{0}->lastName <input name="myModels{0}->children{0}->lastName" /><br />
	children{1}->name <input name="myModels{0}->children{1}->name" /><br />
	children{1}->lastName <input name="myModels{0}->children{1}->lastName" /><br /><br />
	Profile Image <input name="myModels{0}->profileImage" type="file" /><br />
	<br />File1 <input name="myModels{0}->postedFiles{0}" type="file" /><br />
	File2 <input name="myModels{0}->postedFiles{1}" type="file" /><br />
</fieldset>
<br />

<fieldset>
<legend>User2</legend>
	Name <input name="myModels{1}->name" /></br>
	children{0}->name <input name="myModels{1}->children{0}->name" /><br />
	children{0}->lastName <input name="myModels{1}->children{0}->lastName" /><br />
	children{1}->name <input name="myModels{1}->children{1}->name" /><br />
	children{1}->lastName <input name="myModels{1}->children{1}->lastName" /><br /><br />
	Profile Image <input name="myModels{1}->profileImage" type="file" /><br />
	<br />File1 <input name="myModels{1}->postedFiles{0}" type="file" /><br />
	File2 <input name="myModels{1}->postedFiles{1}" type="file" /><br />
</fieldset>

<input type="submit" value="post" />
</form>
</fieldset>
<br />

<fieldset>
<legend><b>Two Dimensional Array</b></legend>
<form action="/Test/twoDinension" method="post" >
	arr{0}{0} <input name="arr{0}{0}" /></br>
	arr{0}{1} <input name="arr{0}{1}" /><br />
	arr{1}{0} <input name="arr{1}{0}" /></br>
	arr{1}{1} <input name="arr{1}{1}" /><br />
	<br />
	<input type="submit" value="post" />
</form>
</fieldset>
</fieldset>
<br />

<br />

<fieldset>
<legend><b>Two Dimensional Users</b></legend>
<form action="/Test/twoDinensionalUsers" method="post" >
	users{0}{0} <input name="users{0}{0}->name" /></br>
	users{0}{1} <input name="users{0}{1}->name" /><br />
	users{1}{0} <input name="users{1}{0}->name" /></br>
	users{1}{1} <input name="users{1}{1}->name" /><br />
	<br />

	<input type="submit" value="post" />
</form>
</fieldset>


</body>
</html>